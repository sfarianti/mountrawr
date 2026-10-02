<?php

namespace App\Core;

class Middleware
{
    public static function handle(array $middlewares): bool
    {
        foreach ($middlewares as $mw) {
            if ($mw === 'guest') {
                if (Auth::check()) {
                    if (Auth::isAdmin()) {
                        redirect('/admin/loans');
                    } else {
                        redirect('/catalog');
                    }
                    return false;
                }
            } elseif ($mw === 'auth') {
                if (!Auth::check()) {
                    Session::flash('error', 'Silakan login terlebih dahulu.');
                    redirect('/login');
                    return false;
                }
            } elseif ($mw === 'admin') {
                if (!Auth::check()) {
                    Session::flash('error', 'Silakan login terlebih dahulu.');
                    redirect('/login');
                    return false;
                }
                if (!Auth::isAdmin()) {
                    http_response_code(403);
                    require_once BASE_PATH . '/app/Views/errors/403.php';
                    exit;
                }
            }
        }
        return true;
    }
}
