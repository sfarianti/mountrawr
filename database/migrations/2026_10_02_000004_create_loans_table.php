<?php

return [
    'up' => [
        "CREATE TABLE loans (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(30) NOT NULL UNIQUE,
            user_id BIGINT UNSIGNED NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            status ENUM('pending', 'approved', 'rejected', 'cancelled', 'borrowed', 'returned', 'completed') NOT NULL DEFAULT 'pending',
            total_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            note TEXT NULL,
            rejection_reason VARCHAR(255) NULL,
            approved_by BIGINT UNSIGNED NULL,
            approved_at DATETIME NULL,
            borrowed_at DATETIME NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_loans_user (user_id),
            KEY idx_loans_status (status),
            KEY idx_loans_dates (start_date, end_date),
            CONSTRAINT fk_loans_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
            CONSTRAINT fk_loans_approved_by FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    ],
    'down' => [
        "DROP TABLE IF EXISTS loans;"
    ]
];
