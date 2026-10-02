<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Category;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(): void
    {
        $items = Item::allAdmin();
        $this->render('admin/items/index', [
            'title' => 'Manajemen Alat — Admin Mountrawr',
            'items' => $items
        ]);
    }

    public function create(): void
    {
        $categories = Category::all();
        $this->render('admin/items/create', [
            'title' => 'Tambah Alat Baru — Admin Mountrawr',
            'categories' => $categories
        ]);
    }

    public function store(): void
    {
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $stockTotal = max(0, (int)($_POST['stock_total'] ?? 1));
        $pricePerDay = max(0, (float)($_POST['price_per_day'] ?? 0));
        $conditionStatus = $_POST['condition_status'] ?? 'baik';
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        set_old($_POST);

        $errors = [];
        if ($categoryId <= 0) {
            $errors[] = 'Pilih kategori yang valid.';
        }
        if (empty($name)) {
            $errors[] = 'Nama alat wajib diisi.';
        }

        // Generate slug
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));

        // Handle image upload
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploaded = $this->handleUpload($_FILES['image'], $errors);
            if ($uploaded) {
                $imagePath = $uploaded;
            }
        }

        if (!empty($errors)) {
            Session::flash('error', implode('<br>', $errors));
            redirect('/admin/items/create');
        }

        Item::create([
            'category_id' => $categoryId,
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'stock_total' => $stockTotal,
            'price_per_day' => $pricePerDay,
            'condition_status' => $conditionStatus,
            'image_path' => $imagePath,
            'is_active' => $isActive
        ]);

        clear_old();
        Session::flash('success', 'Alat pendakian baru berhasil ditambahkan.');
        redirect('/admin/items');
    }

    public function edit(string $id): void
    {
        $item = Item::findById((int)$id);
        if (!$item) {
            http_response_code(404);
            require_once BASE_PATH . '/app/Views/errors/404.php';
            exit;
        }

        $categories = Category::all();
        $this->render('admin/items/edit', [
            'title' => "Edit Alat {$item['name']} — Admin Mountrawr",
            'item' => $item,
            'categories' => $categories
        ]);
    }

    public function update(string $id): void
    {
        $itemId = (int)$id;
        $item = Item::findById($itemId);
        if (!$item) {
            http_response_code(404);
            require_once BASE_PATH . '/app/Views/errors/404.php';
            exit;
        }

        $categoryId = (int)($_POST['category_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $stockTotal = max(0, (int)($_POST['stock_total'] ?? 1));
        $pricePerDay = max(0, (float)($_POST['price_per_day'] ?? 0));
        $conditionStatus = $_POST['condition_status'] ?? 'baik';
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $errors = [];
        if ($categoryId <= 0) {
            $errors[] = 'Pilih kategori yang valid.';
        }
        if (empty($name)) {
            $errors[] = 'Nama alat wajib diisi.';
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));

        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploaded = $this->handleUpload($_FILES['image'], $errors);
            if ($uploaded) {
                $imagePath = $uploaded;
            }
        }

        if (!empty($errors)) {
            Session::flash('error', implode('<br>', $errors));
            redirect("/admin/items/{$itemId}/edit");
        }

        Item::update($itemId, [
            'category_id' => $categoryId,
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'stock_total' => $stockTotal,
            'price_per_day' => $pricePerDay,
            'condition_status' => $conditionStatus,
            'image_path' => $imagePath,
            'is_active' => $isActive
        ]);

        Session::flash('success', 'Data alat berhasil diperbarui.');
        redirect('/admin/items');
    }

    private function handleUpload(array $file, array &$errors): ?string
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if ($file['size'] > $maxSize) {
            $errors[] = 'Ukuran gambar maksimal 2 MB.';
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedTypes)) {
            $errors[] = 'Format gambar harus JPG, PNG, atau WebP.';
            return null;
        }

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        };

        $filename = 'item_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $uploadDir = BASE_PATH . '/public/uploads';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $targetPath = $uploadDir . '/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return 'uploads/' . $filename;
        }

        $errors[] = 'Gagal mengunggah file gambar.';
        return null;
    }
}
