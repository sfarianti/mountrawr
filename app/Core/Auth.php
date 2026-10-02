<?php

namespace App\Core;

use App\Models\User;

class Auth
{
    public static function check(): bool
    {
        return Session::get('user_id') !== null;
    }

    public static function user(): ?array
    {
        $id = Session::get('user_id');
        if (!$id) {
            return null;
        }

        static $cachedUser = null;
        if ($cachedUser && $cachedUser['id'] == $id) {
            return $cachedUser;
        }

        $cachedUser = User::findById($id);
        return $cachedUser;
    }

    public static function id(): ?int
    {
        $user = self::user();
        return $user ? (int)$user['id'] : null;
    }

    public static function role(): ?string
    {
        $user = self::user();
        return $user ? $user['role'] : null;
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    public static function login(array $user): void
    {
        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('user_role', $user['role']);
    }

    public static function attempt(string $email, string $password): bool
    {
        $user = User::findByEmail(strtolower(trim($email)));
        if (!$user) {
            return false;
        }

        if (password_verify($password, $user['password'])) {
            self::login($user);
            return true;
        }

        return false;
    }

    public static function logout(): void
    {
        Session::remove('user_id');
        Session::remove('user_role');
        Session::destroy();
    }
}
