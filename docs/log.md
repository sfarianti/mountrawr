# Log Pelaksanaan Project Mountrawr

Dokumen ini mencatat seluruh aktivitas pengerjaan project **Mountrawr (Outdoor Gear Rental Platform)** sesuai dengan petunjuk pada `instructions/02-todo-task-breakdown.md`.

---

## Ringkasan Log Pengerjaan

| Waktu Eksekusi | Fase | Fitur / Sub-task | Status | Catatan Verifikasi |
|---|---|---|---|---|
| 2026-10-02 14:35 | Fase 0 | Setup & Scaffolding (MVC, Router, PSR-4, Core, Helpers, CLI) | `[x]` Completed | Struktur `app/Core`, `bootstrap.php`, CLI `mountrawr` siap. |
| 2026-10-02 14:41 | Fase 1 | Database & Migrations (7 Tabel + Seeder + Migration Runner) | `[x]` Completed | 7 File migration dieksekusi via `php mountrawr migrate` & `db:seed`. |
| 2026-10-02 14:45 | Fase 2 | Core MVC (Router, Middleware, CSRF, Session Flash, Error Views) | `[x]` Completed | Halaman error 404, 403, 419, 500, & CSRF verification aktif. |
| 2026-10-02 14:50 | Fase 3 | Authentication (Login, Register, Logout, Rate Limiting) | `[x]` Completed | Validation, Bcrypt, Session regeneration, Rate limiter (5 attempt / 15m). |
| 2026-10-02 14:55 | Fase 4 | Katalog Alat (Search, Category Filter, Pagination 12 item/page) | `[x]` Completed | Grid responsive, badge kondisi, filter URL parameter. |
| 2026-10-02 15:00 | Fase 5 | Detail & Ketersediaan Alat (Date Check, 30-Day Matrix, Estimation) | `[x]` Completed | `Item::availableStock()` teruji dengan overlap date logic. |
| 2026-10-02 15:05 | Fase 6 | Pengajuan Peminjaman (Create Loan with Items in Transaction, Code Generator) | `[x]` Completed | Standardized transaction, snapshot price, audit log `pending`. |
| 2026-10-02 15:10 | Fase 7 | Approval Admin (Approve, Reject with Mandatory Reason, Borrowed, Dashboard Stats) | `[x]` Completed | Locking stock re-check pada approve, count pending/borrowed/due. |
| 2026-10-02 15:15 | Fase 8 | Status & Riwayat Peminjaman (Active Loans vs History, Timeline Log) | `[x]` Completed | Badges warna, overdue indicator (`borrowed` & `end_date < today`), timeline logs. |
| 2026-10-02 15:20 | Fase 9 | Pengembalian & Denda (Submit Return, Admin Confirm Return, Penalty Calc) | `[x]` Completed | Hitung late_days, penalty calculation, item condition updates. |
| 2026-10-02 15:25 | Fase 10 | Manajemen Alat & Kategori (Admin CRUD, Image Upload Safe Handler) | `[x]` Completed | CRUD Alat, MIME check upload (max 2MB), soft hide `is_active=0`. |
| 2026-10-02 15:30 | Fase 11 | QA, Security Audit & Automated Integration Testing | `[x]` Completed | Automated integration test pass 100%, syntax lint zero error. |

---

## Log Detail Per Task (Fase 0 - Fase 11)

### Task 0: Setup & Scaffolding
- [x] Membuat `.env` & `.env.example` dengan konfigurasi DB `mountrawr`.
- [x] Membuat autoloader PSR-4 di `bootstrap.php` untuk namespace `App\`.
- [x] Membuat skrip CLI `mountrawr` untuk eksekusi `migrate`, `db:seed`, `serve`.
- [x] Menyiapkan struktur folder `app/Core`, `app/Controllers`, `app/Models`, `app/Views`, `routes`, `database`.

### Task 1: Database & Migration
- [x] Migration `2026_10_02_000001_create_users_table.php`
- [x] Migration `2026_10_02_000002_create_categories_table.php`
- [x] Migration `2026_10_02_000003_create_items_table.php`
- [x] Migration `2026_10_02_000004_create_loans_table.php`
- [x] Migration `2026_10_02_000005_create_loan_items_table.php`
- [x] Migration `2026_10_02_000006_create_loan_returns_table.php`
- [x] Migration `2026_10_02_000007_create_loan_status_logs_table.php`
- [x] Class `Migrator.php` mendukung DDL transaction safety, rollback, fresh, dan status.
- [x] Class `DatabaseSeeder.php` mengisi akun admin (`admin@mountrawr.test`), peminjam (`user@mountrawr.test`), 5 kategori, dan 8 alat pendakian.

### Task 2: Core Framework MVC
- [x] Class `Router.php` mendukung pemetaan URI dengan wildcard `{id}` / `{slug}`, verifikasi CSRF otomatis pada `POST`, dan pemanggilan middleware.
- [x] Class `Middleware.php` mendukung penanganan role `guest`, `auth`, `admin`.
- [x] Class `Session.php` & `Csrf.php` untuk proteksi CSRF token dan pesan flash session.
- [x] Halaman Error 404, 403, 419, dan 500 disajikan secara visual dan rapi.

### Task 3: Auth & Keamanan
- [x] Form Register (`GET/POST /register`) dengan validasi email unik, nama <= 100 karakter, password minimal 8 karakter & konfirmasi.
- [x] Form Login (`GET/POST /login`) dengan password verification (`password_verify`) dan `session_regenerate_id`.
- [x] Rate limiter login gagal (maksimal 5 kali / 15 menit per kombinasi IP + Email).
- [x] Feature logout (`POST /logout`) aman dengan CSRF token.

### Task 4 & 5: Katalog, Detail & Ketersediaan Alat
- [x] `CatalogController@index` mendukung pencarian nama/deskripsi, filter kategori, dan pagination (12 item per halaman).
- [x] `CatalogController@show` menampilkan detail alat, form cek tanggal, kalkulator ketersediaan stok `Item::availableStock()`, estimasi total biaya sewa, serta Kalender Ketersediaan 30 Hari Ke Depan (Indikator Hijau/Merah).

### Task 6: Pengajuan Peminjaman
- [x] `Loan::createWithItems` dijalankan dalam transaksi DB:
  - Validasi tanggal `start_date >= today` dan `end_date >= start_date` (durasi maks 14 hari).
  - Pengecekan sisa stok real-time per item.
  - Snapshot harga `price_per_day` & penghitungan `subtotal` dan `total_price`.
  - Generator kode unik `MTR-YYYYMMDD-XXXX`.
  - Pencatatan log audit awal status `null` -> `pending`.

### Task 7: Approval Admin
- [x] Dashboard Admin `/admin/loans` menyajikan statistik cepat: jumlah pending, sedang dipinjam, dan jatuh tempo hari ini.
- [x] Aksi **Approve** re-check ketersediaan stok dengan `excludeLoanId` dalam transaksi.
- [x] Aksi **Reject** mewajibkan pengisian `rejection_reason`.
- [x] Aksi **Tandai Dipinjam** (status -> `borrowed`).

### Task 8 & 9: Pengembalian, Riwayat & Timeline
- [x] Pengajuan laporan pengembalian oleh peminjam (`POST /loans/{id}/return`).
- [x] Konfirmasi pengembalian oleh admin (`POST /admin/loans/{id}/confirm-return`) dengan input kondisi fisik barang (`baik`, `rusak_ringan`, `rusak_berat`, `hilang`), kalkulasi denda keterlambatan (`late_days * total_biaya_harian`), dan perubahan status -> `completed`.
- [x] Timeline audit transparan pada setiap detail peminjaman dari `loan_status_logs`.

### Task 10: Manajemen Alat & Kategori (Admin)
- [x] CRUD Kategori (`/admin/categories`).
- [x] CRUD Alat Pendakian (`/admin/items`) dilengkapi upload gambar aman (validasi MIME, limit 2MB, nama unik acak).
- [x] Proteksi hapus kategori/alat yang memiliki riwayat dengan fitur penonaktifan (`is_active = 0`).

### Task 11: Integration Testing & Verification
- [x] Pengetesan sintaks PHP pada seluruh 54 file (`php -l`) -> **0 Error**.
- [x] Eksekusi Automated Integration Test (`test_flow.php`) -> **100% Passed**.

---
*Log diperbarui secara otomatis pada 2026-10-02.*
