<?php

namespace App\Models;

use App\Core\Model;

class Category extends Model
{
    public static function all(): array
    {
        return self::fetchAll("SELECT * FROM categories ORDER BY name ASC");
    }

    public static function findById(int $id): ?array
    {
        return self::fetch("SELECT * FROM categories WHERE id = ?", [$id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return self::fetch("SELECT * FROM categories WHERE slug = ?", [$slug]);
    }

    public static function create(array $data): int
    {
        $sql = "INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)";
        self::query($sql, [$data['name'], $data['slug'], $data['description'] ?? null]);
        return (int)self::lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $sql = "UPDATE categories SET name = ?, slug = ?, description = ? WHERE id = ?";
        self::query($sql, [$data['name'], $data['slug'], $data['description'] ?? null, $id]);
        return true;
    }

    public static function delete(int $id): bool
    {
        // Check if category has items
        $itemCount = (int)self::fetch("SELECT COUNT(*) as cnt FROM items WHERE category_id = ?", [$id])['cnt'];
        if ($itemCount > 0) {
            return false;
        }
        self::query("DELETE FROM categories WHERE id = ?", [$id]);
        return true;
    }
}
