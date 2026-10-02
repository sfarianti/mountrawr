<?php

namespace App\Models;

use App\Core\Model;

class Item extends Model
{
    public static function findById(int $id): ?array
    {
        return self::fetch(
            "SELECT i.*, c.name as category_name, c.slug as category_slug 
             FROM items i 
             JOIN categories c ON c.id = i.category_id 
             WHERE i.id = ?",
            [$id]
        );
    }

    public static function findBySlug(string $slug): ?array
    {
        return self::fetch(
            "SELECT i.*, c.name as category_name, c.slug as category_slug 
             FROM items i 
             JOIN categories c ON c.id = i.category_id 
             WHERE i.slug = ? AND i.is_active = 1",
            [$slug]
        );
    }

    public static function allActive(?int $categoryId = null, ?string $search = null, ?int $limit = null, ?int $offset = null): array
    {
        $sql = "SELECT i.*, c.name as category_name 
                FROM items i 
                JOIN categories c ON c.id = i.category_id 
                WHERE i.is_active = 1 AND i.condition_status != 'rusak'";
        $params = [];

        if ($categoryId) {
            $sql .= " AND i.category_id = ?";
            $params[] = $categoryId;
        }

        if (!empty($search)) {
            $sql .= " AND (i.name LIKE ? OR i.description LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $sql .= " ORDER BY i.id DESC";

        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
            if ($offset !== null) {
                $sql .= " OFFSET " . (int)$offset;
            }
        }

        return self::fetchAll($sql, $params);
    }

    public static function countActive(?int $categoryId = null, ?string $search = null): int
    {
        $sql = "SELECT COUNT(*) as cnt 
                FROM items i 
                WHERE i.is_active = 1 AND i.condition_status != 'rusak'";
        $params = [];

        if ($categoryId) {
            $sql .= " AND i.category_id = ?";
            $params[] = $categoryId;
        }

        if (!empty($search)) {
            $sql .= " AND (i.name LIKE ? OR i.description LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $row = self::fetch($sql, $params);
        return (int)($row['cnt'] ?? 0);
    }

    public static function allAdmin(): array
    {
        return self::fetchAll(
            "SELECT i.*, c.name as category_name 
             FROM items i 
             JOIN categories c ON c.id = i.category_id 
             ORDER BY i.id DESC"
        );
    }

    public static function availableStock(int $itemId, string $startDate, string $endDate, ?int $excludeLoanId = null): int
    {
        $sql = "SELECT i.stock_total - COALESCE((
                    SELECT SUM(li.quantity)
                    FROM loan_items li 
                    JOIN loans l ON l.id = li.loan_id
                    WHERE li.item_id = i.id
                      AND l.status IN ('approved','borrowed','returned')
                      AND l.start_date <= :end_date 
                      AND l.end_date >= :start_date";

        $params = [
            'end_date' => $endDate,
            'start_date' => $startDate,
            'id' => $itemId
        ];

        if ($excludeLoanId !== null) {
            $sql .= " AND l.id != :exclude_loan_id";
            $params['exclude_loan_id'] = $excludeLoanId;
        }

        $sql .= "), 0) AS available
                FROM items i 
                WHERE i.id = :id";

        $row = self::fetch($sql, $params);
        if (!$row) {
            return 0;
        }

        return max(0, (int)$row['available']);
    }

    public static function create(array $data): int
    {
        $sql = "INSERT INTO items (category_id, name, slug, description, stock_total, price_per_day, condition_status, image_path, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        self::query($sql, [
            $data['category_id'],
            $data['name'],
            $data['slug'],
            $data['description'] ?? null,
            $data['stock_total'],
            $data['price_per_day'],
            $data['condition_status'] ?? 'baik',
            $data['image_path'] ?? null,
            $data['is_active'] ?? 1
        ]);
        return (int)self::lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $sql = "UPDATE items SET 
                category_id = ?, 
                name = ?, 
                slug = ?, 
                description = ?, 
                stock_total = ?, 
                price_per_day = ?, 
                condition_status = ?, 
                image_path = COALESCE(?, image_path), 
                is_active = ? 
                WHERE id = ?";
        self::query($sql, [
            $data['category_id'],
            $data['name'],
            $data['slug'],
            $data['description'] ?? null,
            $data['stock_total'],
            $data['price_per_day'],
            $data['condition_status'],
            $data['image_path'] ?? null,
            $data['is_active'],
            $id
        ]);
        return true;
    }
}
