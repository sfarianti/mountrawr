<?php

namespace App\Models;

use App\Core\Auth;
use App\Core\Model;

class LoanReturn extends Model
{
    public static function submitReturn(int $loanId, ?string $note = null, ?int $userId = null): array
    {
        $loan = Loan::findById($loanId);
        if (!$loan || $loan['status'] !== 'borrowed') {
            return ['success' => false, 'message' => 'Peminjaman tidak dalam status dipinjam.'];
        }

        Model::beginTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            $today = date('Y-m-d');
            $endDate = $loan['end_date'];

            // Calculate late days
            $lateDays = 0;
            if ($today > $endDate) {
                $endDt = new \DateTime($endDate);
                $retDt = new \DateTime($today);
                $lateDays = $endDt->diff($retDt)->days;
            }

            // Estimate penalty (100% total daily price per late day)
            $loanItems = LoanItem::getByLoanId($loanId);
            $dailyTotal = 0;
            foreach ($loanItems as $li) {
                $dailyTotal += ((float)$li['price_per_day'] * (int)$li['quantity']);
            }
            $penaltyAmount = $lateDays * $dailyTotal;

            // Check if return record already exists
            $existing = Model::fetch("SELECT id FROM loan_returns WHERE loan_id = ?", [$loanId]);
            if ($existing) {
                $sql = "UPDATE loan_returns SET returned_at = ?, late_days = ?, penalty_amount = ?, note = ? WHERE loan_id = ?";
                Model::query($sql, [$now, $lateDays, $penaltyAmount, $note, $loanId]);
            } else {
                $sql = "INSERT INTO loan_returns (loan_id, returned_at, item_condition, late_days, penalty_amount, note) 
                        VALUES (?, ?, 'baik', ?, ?, ?)";
                Model::query($sql, [$loanId, $now, $lateDays, $penaltyAmount, $note]);
            }

            $changedBy = $userId ?? Auth::id();
            // Transition status borrowed -> returned
            $res = Loan::changeStatus($loanId, 'returned', $changedBy, 'Peminjam melaporkan pengembalian barang.');
            if (!$res['success']) {
                Model::rollBack();
                return $res;
            }

            Model::commit();
            return ['success' => true];
        } catch (\Exception $e) {
            Model::rollBack();
            return ['success' => false, 'message' => 'Gagal memproses laporan pengembalian: ' . $e->getMessage()];
        }
    }

    public static function confirmReturn(int $loanId, int $adminId, string $condition, ?float $penaltyAmount = null, ?string $note = null): array
    {
        $loan = Loan::findById($loanId);
        if (!$loan || !in_array($loan['status'], ['borrowed', 'returned'])) {
            return ['success' => false, 'message' => 'Peminjaman tidak dapat dikonfirmasi pengembaliannya.'];
        }

        Model::beginTransaction();
        try {
            $returnRecord = Model::fetch("SELECT * FROM loan_returns WHERE loan_id = ?", [$loanId]);
            $now = date('Y-m-d H:i:s');

            if ($penaltyAmount === null) {
                $penaltyAmount = $returnRecord ? (float)$returnRecord['penalty_amount'] : 0.0;
            }

            if ($returnRecord) {
                $sql = "UPDATE loan_returns SET 
                        item_condition = ?, 
                        penalty_amount = ?, 
                        note = ?, 
                        confirmed_by = ?, 
                        confirmed_at = ? 
                        WHERE loan_id = ?";
                Model::query($sql, [$condition, $penaltyAmount, $note, $adminId, $now, $loanId]);
            } else {
                $today = date('Y-m-d');
                $endDate = $loan['end_date'];
                $lateDays = 0;
                if ($today > $endDate) {
                    $endDt = new \DateTime($endDate);
                    $retDt = new \DateTime($today);
                    $lateDays = $endDt->diff($retDt)->days;
                }

                $sql = "INSERT INTO loan_returns (loan_id, returned_at, item_condition, late_days, penalty_amount, note, confirmed_by, confirmed_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                Model::query($sql, [$loanId, $now, $condition, $lateDays, $penaltyAmount, $note, $adminId, $now]);
            }

            // Update item condition status if damaged
            if (in_array($condition, ['rusak_berat', 'hilang'])) {
                $loanItems = LoanItem::getByLoanId($loanId);
                foreach ($loanItems as $li) {
                    Model::query("UPDATE items SET condition_status = 'perlu_perbaikan' WHERE id = ?", [$li['item_id']]);
                }
            }

            // Transition status to completed
            $toStatus = 'completed';
            $sqlStatus = "UPDATE loans SET status = ? WHERE id = ?";
            Model::query($sqlStatus, [$toStatus, $loanId]);

            // Record status log
            $sqlLog = "INSERT INTO loan_status_logs (loan_id, from_status, to_status, changed_by, note) VALUES (?, ?, 'completed', ?, ?)";
            Model::query($sqlLog, [$loanId, $loan['status'], $adminId, 'Konfirmasi pengembalian oleh admin']);

            Model::commit();
            return ['success' => true];
        } catch (\Exception $e) {
            Model::rollBack();
            return ['success' => false, 'message' => 'Gagal mengonfirmasi pengembalian: ' . $e->getMessage()];
        }
    }
}
