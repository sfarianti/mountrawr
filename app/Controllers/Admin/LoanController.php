<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Loan;
use App\Models\LoanReturn;

class LoanController extends Controller
{
    public function index(): void
    {
        $status = $_GET['status'] ?? null;
        $loans = Loan::getAdminLoans($status);

        $pendingCount = Loan::getPendingCount();
        $borrowedCount = Loan::getBorrowedCount();
        $dueTodayCount = Loan::getDueTodayCount();

        $this->render('admin/loans/index', [
            'title' => 'Manajemen Peminjaman — Admin Mountrawr',
            'loans' => $loans,
            'currentStatus' => $status,
            'pendingCount' => $pendingCount,
            'borrowedCount' => $borrowedCount,
            'dueTodayCount' => $dueTodayCount
        ]);
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

        $this->render('admin/loans/show', [
            'title' => "Kelola Peminjaman {$loan['code']} — Admin Mountrawr",
            'loan' => $loan
        ]);
    }

    public function approve(string $id): void
    {
        $loanId = (int)$id;
        $res = Loan::changeStatus($loanId, 'approved', Auth::id(), 'Peminjaman disetujui oleh Admin.');

        if ($res['success']) {
            Session::flash('success', 'Peminjaman berhasil disetujui!');
        } else {
            Session::flash('error', $res['message']);
        }

        redirect("/admin/loans/{$loanId}");
    }

    public function reject(string $id): void
    {
        $loanId = (int)$id;
        $reason = trim($_POST['rejection_reason'] ?? '');

        if (empty($reason)) {
            Session::flash('error', 'Alasan penolakan wajib diisi.');
            redirect("/admin/loans/{$loanId}");
        }

        $res = Loan::changeStatus($loanId, 'rejected', Auth::id(), $reason);

        if ($res['success']) {
            Session::flash('success', 'Peminjaman telah ditolak.');
        } else {
            Session::flash('error', $res['message']);
        }

        redirect("/admin/loans/{$loanId}");
    }

    public function markBorrowed(string $id): void
    {
        $loanId = (int)$id;
        $res = Loan::changeStatus($loanId, 'borrowed', Auth::id(), 'Alat telah diambil/diserahkan kepada peminjam.');

        if ($res['success']) {
            Session::flash('success', 'Status berhasil diubah menjadi Dipinjam.');
        } else {
            Session::flash('error', $res['message']);
        }

        redirect("/admin/loans/{$loanId}");
    }

    public function confirmReturn(string $id): void
    {
        $loanId = (int)$id;
        $condition = $_POST['item_condition'] ?? 'baik';
        $penaltyInput = isset($_POST['penalty_amount']) && $_POST['penalty_amount'] !== '' ? (float)$_POST['penalty_amount'] : null;
        $note = trim($_POST['note'] ?? '');

        $res = LoanReturn::confirmReturn($loanId, Auth::id(), $condition, $penaltyInput, $note);

        if ($res['success']) {
            Session::flash('success', 'Pengembalian alat berhasil dikonfirmasi. Status peminjaman Selesai.');
        } else {
            Session::flash('error', $res['message']);
        }

        redirect("/admin/loans/{$loanId}");
    }
}
