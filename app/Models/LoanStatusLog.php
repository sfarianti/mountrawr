<?php

namespace App\Models;

use App\Core\Model;

class LoanStatusLog extends Model
{
    public static function getByLoanId(int $loanId): array
    {
        return self::fetchAll(
            "SELECT lsl.*, u.name as changed_by_name
             FROM loan_status_logs lsl
             LEFT JOIN users u ON u.id = lsl.changed_by
             WHERE lsl.loan_id = ?
             ORDER BY lsl.id ASC",
            [$loanId]
        );
    }
}
