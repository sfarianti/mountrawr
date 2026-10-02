<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index(): void
    {
        $categories = Category::all();
        $this->render('admin/categories/index', [
            'title' => 'Manajemen Kategori — Admin Mountrawr',
            'categories' => $categories
        ]);
    }

    public function store(): void
    {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            Session::flash('error', 'Nama kategori wajib diisi.');
            redirect('/admin/categories');
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));

        Category::create([
            'name' => $name,
            'slug' => $slug,
            'description' => $description
        ]);

        Session::flash('success', 'Kategori baru berhasil ditambahkan.');
        redirect('/admin/categories');
    }

    public function update(string $id): void
    {
        $catId = (int)$id;
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            Session::flash('error', 'Nama kategori wajib diisi.');
            redirect('/admin/categories');
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));

        Category::update($catId, [
            'name' => $name,
            'slug' => $slug,
            'description' => $description
        ]);

        Session::flash('success', 'Kategori berhasil diperbarui.');
        redirect('/admin/categories');
    }

    public function delete(string $id): void
    {
        $catId = (int)$id;
        $success = Category::delete($catId);

        if ($success) {
            Session::flash('success', 'Kategori berhasil dihapus.');
        } else {
            Session::flash('error', 'Kategori tidak dapat dihapus karena masih memiliki alat pendakian yang terdaftar.');
        }

        redirect('/admin/categories');
    }
}
