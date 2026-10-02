<?php

return [
    'up' => [
        "CREATE TABLE loan_items (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            loan_id BIGINT UNSIGNED NOT NULL,
            item_id BIGINT UNSIGNED NOT NULL,
            quantity INT UNSIGNED NOT NULL DEFAULT 1,
            price_per_day DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            subtotal DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uk_loan_item (loan_id, item_id),
            KEY idx_loan_items_item (item_id),
            CONSTRAINT fk_loan_items_loan FOREIGN KEY (loan_id) REFERENCES loans(id) ON DELETE CASCADE,
            CONSTRAINT fk_loan_items_item FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    ],
    'down' => [
        "DROP TABLE IF EXISTS loan_items;"
    ]
];
