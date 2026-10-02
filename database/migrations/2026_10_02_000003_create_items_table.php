<?php

return [
    'up' => [
        "CREATE TABLE items (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            category_id BIGINT UNSIGNED NOT NULL,
            name VARCHAR(150) NOT NULL,
            slug VARCHAR(170) NOT NULL UNIQUE,
            description TEXT NULL,
            stock_total INT UNSIGNED NOT NULL DEFAULT 1,
            price_per_day DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            condition_status ENUM('baik', 'perlu_perbaikan', 'rusak') NOT NULL DEFAULT 'baik',
            image_path VARCHAR(255) NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_items_category (category_id),
            KEY idx_items_active (is_active),
            CONSTRAINT fk_items_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    ],
    'down' => [
        "DROP TABLE IF EXISTS items;"
    ]
];
