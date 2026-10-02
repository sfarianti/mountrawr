<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->render('auth/login', [
            'title' => 'Login — Mountrawr'
        ]);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        set_old(['email' => $email]);

        // Basic validation
        if (empty($email) || empty($password)) {
            Session::flash('error', 'Email dan password wajib diisi.');
            redirect('/login');
        }

        // Rate limiting check
        $attemptsKey = 'login_attempts_' . md5($_SERVER['REMOTE_ADDR'] . '_' . strtolower($email));
        $attempts = Session::get($attemptsKey, ['count' => 0, 'time' => time()]);

        if ($attempts['count'] >= 5 && (time() - $attempts['time']) < 900) {
            $timeLeft = ceil((900 - (time() - $attempts['time'])) / 60);
            Session::flash('error', "Terlalu banyak percobaan login gagal. Silakan coba lagi dalam {$timeLeft} menit.");
            redirect('/login');
        }

        if (Auth::attempt($email, $password)) {
            Session::remove($attemptsKey);
            clear_old();
            Session::flash('success', 'Selamat datang kembali di Mountrawr!');

            if (Auth::isAdmin()) {
                redirect('/admin/loans');
            } else {
                redirect('/catalog');
            }
        } else {
            // Increment rate limiting count
            $attempts['count']++;
            $attempts['time'] = time();
            Session::set($attemptsKey, $attempts);

            Session::flash('error', 'Email atau password yang Anda masukkan salah.');
            redirect('/login');
        }
    }

    public function showRegister(): void
    {
        $this->render('auth/register', [
            'title' => 'Daftar Akun Baru — Mountrawr'
        ]);
    }

    public function register(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        set_old([
            'name' => $name,
            'email' => $email,
            'phone' => $phone
        ]);

        $errors = [];

        if (empty($name) || strlen($name) > 100) {
            $errors[] = 'Nama wajib diisi dan maksimal 100 karakter.';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        } elseif (User::findByEmail($email)) {
            $errors[] = 'Email sudah terdaftar. Silakan gunakan email lain.';
        }

        if (strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter.';
        } elseif ($password !== $passwordConfirmation) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        }

        if (!empty($errors)) {
            Session::flash('error', implode('<br>', $errors));
            redirect('/register');
        }

        $userId = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'role' => 'peminjam'
        ]);

        $user = User::findById($userId);
        Auth::login($user);

        clear_old();
        Session::flash('success', 'Pendaftaran berhasil! Selamat bergabung di Mountrawr.');
        redirect('/catalog');
    }

    public function logout(): void
    {
        Auth::logout();
        Session::flash('success', 'Anda telah berhasil keluar.');
        redirect('/login');
    }
}
