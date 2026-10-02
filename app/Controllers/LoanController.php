<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Loan;
use App\Models\LoanReturn;

class LoanController extends Controller
{
    public function index(): void
    {
        $userId = Auth::id();
        $loans = Loan::getUserLoans($userId, true);

        $this->render('loans/index', [
            'title' => 'Peminjaman Aktif Saya — Mountrawr',
            'loans' => $loans,
            'tab' => 'active'
        ]);
    }

    public function history(): void
    {
        $userId = Auth::id();
        $loans = Loan::getUserLoans($userId, false);

        $this->render('loans/history', [
            'title' => 'Riwayat Peminjaman — Mountrawr',
            'loans' => $loans,
            'tab' => 'history'
        ]);
    }

    public function store(): void
    {
        $userId = Auth::id();
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');
        $note = trim($_POST['note'] ?? '');
        $itemsReq = $_POST['items'] ?? [];

        set_old([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'note' => $note
        ]);

        if (empty($startDate) || empty($endDate)) {
            Session::flash('error', 'Tanggal mulai dan tanggal kembali wajib diisi.');
            redirect('/catalog');
        }

        $today = date('Y-m-d');
        if ($startDate < $today) {
            Session::flash('error', 'Tanggal mulai peminjaman tidak boleh tanggal yang sudah lewat.');
            redirect('/catalog');
        }

        // Standardize items input format
        // Supports array of {item_id, quantity} or single item item_id & quantity
        $items = [];
        if (isset($_POST['item_id']) && isset($_POST['quantity'])) {
            $items[] = [
                'item_id' => (int)$_POST['item_id'],
                'quantity' => max(1, (int)$_POST['quantity'])
            ];
        } elseif (is_array($itemsReq)) {
            foreach ($itemsReq as $it) {
                if (isset($it['item_id']) && isset($it['quantity']) && (int)$it['quantity'] > 0) {
                    $items[] = [
                        'item_id' => (int)$it['item_id'],
                        'quantity' => (int)$it['quantity']
                    ];
                }
            }
        }

        if (empty($items)) {
            Session::flash('error', 'Pilih minimal satu alat dengan jumlah valid.');
            redirect('/catalog');
        }

        $result = Loan::createWithItems($userId, $startDate, $endDate, $items, $note);

        if (!$result['success']) {
            Session::flash('error', $result['message']);
            redirect('/catalog');
        }

        clear_old();
        Session::flash('success', "Pengajuan peminjaman berhasil dibuat! Kode: {$result['code']}");
        redirect('/loans/' . $result['loan_id']);
    }

    public function show(string $id): void
    {
        $loanId = (int)$id;
        $loan = Loan::findWithDetails($loanId);

        if (!$loan) {
            http_response_code(404);
            require_once BASE_PATH . '/app/Views/errors/404.php';
            exit;
        }

        // Authorization check: User can only view their own loans unless Admin
        if ($loan['user_id'] != Auth::id() && !Auth::isAdmin()) {
            http_response_code(403);
            require_once BASE_PATH . '/app/Views/errors/403.php';
            exit;
        }

        $this->render('loans/show', [
            'title' => "Detail Peminjaman {$loan['code']} — Mountrawr",
            'loan' => $loan
        ]);
    }

    public function cancel(string $id): void
    {
        $loanId = (int)$id;
        $loan = Loan::findById($loanId);

        if (!$loan || ($loan['user_id'] != Auth::id() && !Auth::isAdmin())) {
            http_response_code(403);
            require_once BASE_PATH . '/app/Views/errors/403.php';
            exit;
        }

        if (!in_array($loan['status'], ['pending', 'approved'])) {
            Session::flash('error', 'Peminjaman ini tidak dapat dibatalkan.');
            redirect("/loans/{$loanId}");
        }

        $res = Loan::changeStatus($loanId, 'cancelled', Auth::id(), 'Dibatalkan oleh peminjam.');
        if ($res['success']) {
            Session::flash('success', 'Peminjaman berhasil dibatalkan.');
        } else {
            Session::flash('error', $res['message']);
        }

        redirect("/loans/{$loanId}");
    }

    public function returnItem(string $id): void
    {
        $loanId = (int)$id;
        $loan = Loan::findById($loanId);

        if (!$loan || ($loan['user_id'] != Auth::id() && !Auth::isAdmin())) {
            http_response_code(403);
            require_once BASE_PATH . '/app/Views/errors/403.php';
            exit;
        }

        $note = trim($_POST['note'] ?? '');
        $res = LoanReturn::submitReturn($loanId, $note);

        if ($res['success']) {
            Session::flash('success', 'Laporan pengembalian telah dikirim. Menunggu konfirmasi admin.');
        } else {
            Session::flash('error', $res['message']);
        }

        redirect("/loans/{$loanId}");
    }
}
