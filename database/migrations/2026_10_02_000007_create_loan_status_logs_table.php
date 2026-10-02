<?php

return [
    'up' => [
        "CREATE TABLE loan_status_logs (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            loan_id BIGINT UNSIGNED NOT NULL,
            from_status VARCHAR(20) NULL,
            to_status VARCHAR(20) NOT NULL,
            changed_by BIGINT UNSIGNED NULL,
            note VARCHAR(255) NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_logs_loan (loan_id, created_at),
            CONSTRAINT fk_logs_loan FOREIGN KEY (loan_id) REFERENCES loans(id) ON DELETE CASCADE,
            CONSTRAINT fk_logs_changed_by FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    ],
    'down' => [
        "DROP TABLE IF EXISTS loan_status_logs;"
    ]
];
