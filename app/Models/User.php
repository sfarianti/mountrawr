<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    public static function findById(int $id): ?array
    {
        return self::fetch("SELECT * FROM users WHERE id = ?", [$id]);
    }

    public static function findByEmail(string $email): ?array
    {
        return self::fetch("SELECT * FROM users WHERE email = ?", [strtolower(trim($email))]);
    }

    public static function create(array $data): int
    {
        $sql = "INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)";
        self::query($sql, [
            $data['name'],
            strtolower(trim($data['email'])),
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['phone'] ?? null,
            $data['role'] ?? 'peminjam'
        ]);
        return (int)self::lastInsertId();
    }

    public static function updateProfile(int $id, array $data): bool
    {
        if (!empty($data['password'])) {
            $sql = "UPDATE users SET name = ?, phone = ?, password = ? WHERE id = ?";
            self::query($sql, [$data['name'], $data['phone'] ?? null, password_hash($data['password'], PASSWORD_BCRYPT), $id]);
        } else {
            $sql = "UPDATE users SET name = ?, phone = ? WHERE id = ?";
            self::query($sql, [$data['name'], $data['phone'] ?? null, $id]);
        }
        return true;
    }
}
