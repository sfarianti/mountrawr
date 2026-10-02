<?php

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Csrf::token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return Csrf::field();
    }
}

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string
    {
        $appUrl = rtrim(getenv('APP_URL') ?: 'http://localhost:8000', '/');
        $path = ltrim($path, '/');
        return $path !== '' ? "{$appUrl}/{$path}" : $appUrl;
    }
}

if (!function_exists('rupiah')) {
    function rupiah(float|int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}

if (!function_exists('set_flash')) {
    function set_flash(string $key, mixed $message): void
    {
        Session::flash($key, $message);
    }
}

if (!function_exists('flash')) {
    function flash(string $key): mixed
    {
        return Session::flash($key);
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        $oldInputs = Session::get('_old_input', []);
        return $oldInputs[$key] ?? $default;
    }
}

if (!function_exists('set_old')) {
    function set_old(array $inputs): void
    {
        Session::set('_old_input', $inputs);
    }
}

if (!function_exists('clear_old')) {
    function clear_old(): void
    {
        Session::remove('_old_input');
    }
}

if (!function_exists('auth')) {
    function auth(): ?array
    {
        return Auth::user();
    }
}
