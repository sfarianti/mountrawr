<?php

return [
    'up' => [
        "CREATE TABLE loan_returns (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            loan_id BIGINT UNSIGNED NOT NULL UNIQUE,
            returned_at DATETIME NOT NULL,
            item_condition ENUM('baik', 'rusak_ringan', 'rusak_berat', 'hilang') NOT NULL DEFAULT 'baik',
            late_days INT UNSIGNED NOT NULL DEFAULT 0,
            penalty_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            note TEXT NULL,
            confirmed_by BIGINT UNSIGNED NULL,
            confirmed_at DATETIME NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_loan_returns_loan FOREIGN KEY (loan_id) REFERENCES loans(id) ON DELETE CASCADE,
            CONSTRAINT fk_loan_returns_confirmed_by FOREIGN KEY (confirmed_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    ],
    'down' => [
        "DROP TABLE IF EXISTS loan_returns;"
    ]
];
