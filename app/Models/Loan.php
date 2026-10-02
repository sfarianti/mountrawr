<?php

namespace App\Models;

use App\Core\Model;

class Loan extends Model
{
    public const TRANSITIONS = [
        'pending' => ['approved', 'rejected', 'cancelled'],
        'approved' => ['borrowed', 'cancelled'],
        'rejected' => [],
        'cancelled' => [],
        'borrowed' => ['returned', 'completed'],
        'returned' => ['completed'],
        'completed' => []
    ];

    public static function generateCode(): string
    {
        $dateStr = date('Ymd');
        $random = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
        return "MTR-{$dateStr}-{$random}";
    }

    public static function findById(int $id): ?array
    {
        return self::fetch("SELECT * FROM loans WHERE id = ?", [$id]);
    }

    public static function findWithDetails(int $id): ?array
    {
        $loan = self::fetch(
            "SELECT l.*, u.name as user_name, u.email as user_email, u.phone as user_phone,
                    app.name as approved_by_name
             FROM loans l
             JOIN users u ON u.id = l.user_id
             LEFT JOIN users app ON app.id = l.approved_by
             WHERE l.id = ?",
            [$id]
        );

        if (!$loan) {
            return null;
        }

        // Get loan items
        $loan['items'] = self::fetchAll(
            "SELECT li.*, i.name as item_name, i.slug as item_slug, i.image_path, c.name as category_name
             FROM loan_items li
             JOIN items i ON i.id = li.item_id
             JOIN categories c ON c.id = i.category_id
             WHERE li.loan_id = ?",
            [$id]
        );

        // Get return info if any
        $loan['return'] = self::fetch(
            "SELECT lr.*, u.name as confirmed_by_name
             FROM loan_returns lr
             LEFT JOIN users u ON u.id = lr.confirmed_by
             WHERE lr.loan_id = ?",
            [$id]
        );

        // Get status logs
        $loan['logs'] = self::fetchAll(
            "SELECT lsl.*, u.name as changed_by_name
             FROM loan_status_logs lsl
             LEFT JOIN users u ON u.id = lsl.changed_by
             WHERE lsl.loan_id = ?
             ORDER BY lsl.id ASC",
            [$id]
        );

        // Calculate days
        $start = new \DateTime($loan['start_date']);
        $end = new \DateTime($loan['end_date']);
        $loan['duration_days'] = $start->diff($end)->days + 1;

        // Is overdue
        $today = date('Y-m-d');
        $loan['is_overdue'] = ($loan['status'] === 'borrowed' && $loan['end_date'] < $today);

        return $loan;
    }

    public static function getUserLoans(int $userId, bool $activeOnly = true): array
    {
        $activeStatuses = "'pending', 'approved', 'borrowed', 'returned'";
        $historyStatuses = "'completed', 'rejected', 'cancelled'";
        $statusCondition = $activeOnly ? "status IN ({$activeStatuses})" : "status IN ({$historyStatuses})";

        return self::fetchAll(
            "SELECT l.*, 
                    (SELECT COUNT(*) FROM loan_items WHERE loan_id = l.id) as item_count
             FROM loans l
             WHERE l.user_id = ? AND {$statusCondition}
             ORDER BY l.id DESC",
            [$userId]
        );
    }

    public static function getAdminLoans(?string $status = null): array
    {
        $sql = "SELECT l.*, u.name as user_name, u.email as user_email,
                       (SELECT COUNT(*) FROM loan_items WHERE loan_id = l.id) as item_count
                FROM loans l
                JOIN users u ON u.id = l.user_id";
        $params = [];

        if (!empty($status)) {
            $sql .= " WHERE l.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY l.id DESC";

        return self::fetchAll($sql, $params);
    }

    public static function getPendingCount(): int
    {
        $row = self::fetch("SELECT COUNT(*) as cnt FROM loans WHERE status = 'pending'");
        return (int)($row['cnt'] ?? 0);
    }

    public static function getBorrowedCount(): int
    {
        $row = self::fetch("SELECT COUNT(*) as cnt FROM loans WHERE status = 'borrowed'");
        return (int)($row['cnt'] ?? 0);
    }

    public static function getDueTodayCount(): int
    {
        $today = date('Y-m-d');
        $row = self::fetch("SELECT COUNT(*) as cnt FROM loans WHERE status = 'borrowed' AND end_date = ?", [$today]);
        return (int)($row['cnt'] ?? 0);
    }

    public static function createWithItems(int $userId, string $startDate, string $endDate, array $items, ?string $note = null): array
    {
        // Calculate days
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $days = $start->diff($end)->days + 1;

        if ($days <= 0) {
            return ['success' => false, 'message' => 'Tanggal kembali harus setelah atau sama dengan tanggal mulai.'];
        }

        if ($days > 14) {
            return ['success' => false, 'message' => 'Durasi peminjaman maksimal 14 hari.'];
        }

        self::beginTransaction();
        try {
            // Check stock for each item
            $totalPrice = 0.0;
            $itemsToInsert = [];

            foreach ($items as $itemReq) {
                $itemId = (int)$itemReq['item_id'];
                $qty = (int)$itemReq['quantity'];

                if ($qty <= 0) {
                    continue;
                }

                $item = Item::findById($itemId);
                if (!$item || !$item['is_active'] || $item['condition_status'] === 'rusak') {
                    self::rollBack();
                    return ['success' => false, 'message' => "Alat dengan ID #{$itemId} tidak tersedia."];
                }

                $available = Item::availableStock($itemId, $startDate, $endDate);
                if ($qty > $available) {
                    self::rollBack();
                    return ['success' => false, 'message' => "Stok alat '{$item['name']}' tidak mencukupi untuk periode tanggal tersebut (Tersedia: {$available}, Diminta: {$qty})."];
                }

                $pricePerDay = (float)$item['price_per_day'];
                $subtotal = $pricePerDay * $qty * $days;
                $totalPrice += $subtotal;

                $itemsToInsert[] = [
                    'item_id' => $itemId,
                    'quantity' => $qty,
                    'price_per_day' => $pricePerDay,
                    'subtotal' => $subtotal
                ];
            }

            if (empty($itemsToInsert)) {
                self::rollBack();
                return ['success' => false, 'message' => 'Pilih minimal satu alat untuk dipinjam.'];
            }

            // Insert loan header
            $code = self::generateCode();
            $sqlLoan = "INSERT INTO loans (code, user_id, start_date, end_date, status, total_price, note) 
                        VALUES (?, ?, ?, ?, 'pending', ?, ?)";
            self::query($sqlLoan, [$code, $userId, $startDate, $endDate, $totalPrice, $note]);
            $loanId = (int)self::lastInsertId();

            // Insert loan items
            $sqlItem = "INSERT INTO loan_items (loan_id, item_id, quantity, price_per_day, subtotal) VALUES (?, ?, ?, ?, ?)";
            foreach ($itemsToInsert as $itemRow) {
                self::query($sqlItem, [
                    $loanId,
                    $itemRow['item_id'],
                    $itemRow['quantity'],
                    $itemRow['price_per_day'],
                    $itemRow['subtotal']
                ]);
            }

            // Initial status log
            $sqlLog = "INSERT INTO loan_status_logs (loan_id, from_status, to_status, changed_by, note) VALUES (?, NULL, 'pending', ?, 'Pengajuan peminjaman baru oleh peminjam')";
            self::query($sqlLog, [$loanId, $userId]);

            self::commit();
            return ['success' => true, 'loan_id' => $loanId, 'code' => $code];
        } catch (\Exception $e) {
            self::rollBack();
            return ['success' => false, 'message' => 'Gagal memproses peminjaman: ' . $e->getMessage()];
        }
    }

    public static function changeStatus(int $loanId, string $toStatus, ?int $changedBy = null, ?string $note = null): array
    {
        $loan = self::findById($loanId);
        if (!$loan) {
            return ['success' => false, 'message' => 'Peminjaman tidak ditemukan.'];
        }

        $fromStatus = $loan['status'];

        // Check transition validity
        $allowed = self::TRANSITIONS[$fromStatus] ?? [];
        if (!in_array($toStatus, $allowed, true)) {
            return ['success' => false, 'message' => "Transisi status dari '{$fromStatus}' ke '{$toStatus}' tidak valid."];
        }

        self::beginTransaction();
        try {
            // Additional check for 'approved' status (re-check stock)
            if ($toStatus === 'approved') {
                $loanItems = self::fetchAll("SELECT * FROM loan_items WHERE loan_id = ?", [$loanId]);
                foreach ($loanItems as $li) {
                    $available = Item::availableStock((int)$li['item_id'], $loan['start_date'], $loan['end_date'], $loanId);
                    if ((int)$li['quantity'] > $available) {
                        self::rollBack();
                        return ['success' => false, 'message' => 'Persetujuan gagal: stok alat tidak mencukupi karena telah disetujui peminjaman lain.'];
                    }
                }

                $sql = "UPDATE loans SET status = 'approved', approved_by = ?, approved_at = NOW() WHERE id = ?";
                self::query($sql, [$changedBy, $loanId]);
            } elseif ($toStatus === 'rejected') {
                if (empty($note)) {
                    self::rollBack();
                    return ['success' => false, 'message' => 'Alasan penolakan wajib diisi.'];
                }
                $sql = "UPDATE loans SET status = 'rejected', rejection_reason = ?, approved_by = ?, approved_at = NOW() WHERE id = ?";
                self::query($sql, [$note, $changedBy, $loanId]);
            } elseif ($toStatus === 'borrowed') {
                $sql = "UPDATE loans SET status = 'borrowed', borrowed_at = NOW() WHERE id = ?";
                self::query($sql, [$loanId]);
            } else {
                $sql = "UPDATE loans SET status = ? WHERE id = ?";
                self::query($sql, [$toStatus, $loanId]);
            }

            // Record log
            $sqlLog = "INSERT INTO loan_status_logs (loan_id, from_status, to_status, changed_by, note) VALUES (?, ?, ?, ?, ?)";
            self::query($sqlLog, [$loanId, $fromStatus, $toStatus, $changedBy, $note]);

            self::commit();
            return ['success' => true];
        } catch (\Exception $e) {
            self::rollBack();
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }
}
