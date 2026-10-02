<?php

namespace App\Core;

class Csrf
{
    public static function token(): string
    {
        Session::start();
        $token = Session::get('_csrf_token');
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            Session::set('_csrf_token', $token);
        }
        return $token;
    }

    public static function field(): string
    {
        $token = self::token();
        return '<input type="hidden" name="_csrf" value="' . e($token) . '">';
    }

    public static function verify(?string $token): bool
    {
        Session::start();
        $stored = Session::get('_csrf_token');
        if (!$stored || !$token) {
            return false;
        }
        return hash_equals($stored, $token);
    }
}
