<?php

namespace App\Models;

use App\Core\Model;

class LoanItem extends Model
{
    public static function getByLoanId(int $loanId): array
    {
        return self::fetchAll(
            "SELECT li.*, i.name as item_name, i.slug as item_slug, i.image_path, c.name as category_name
             FROM loan_items li
             JOIN items i ON i.id = li.item_id
             JOIN categories c ON c.id = i.category_id
             WHERE li.loan_id = ?",
            [$loanId]
        );
    }
}
