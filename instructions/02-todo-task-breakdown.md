# 02 · TODO & Task Breakdown — Mountrawr

Legenda: `[x]` sudah ada di scaffolding · `[ ]` belum · **P0** wajib MVP · **P1** penting · **P2** nice to have
Estimasi dalam jam kerja (kasar, 1 developer).

## Ringkasan Fase

| Fase | Fokus | Estimasi | Bergantung pada |
|---|---|---|---|
| 0 | Setup & verifikasi scaffolding | 2 j | — |
| 1 | Database & migration | 2 j | 0 |
| 2 | Core MVC | 2 j | 1 |
| 3 | Auth (Login & Register) | 4 j | 2 |
| 4 | Katalog Alat | 5 j | 3 |
| 5 | Detail & Ketersediaan | 6 j | 4 |
| 6 | Pengajuan Peminjaman | 8 j | 5 |
| 7 | Approval Admin | 6 j | 6 |
| 8 | Status & Riwayat | 5 j | 6, 7 |
| 9 | Pengembalian | 7 j | 7 |
| 10 | Manajemen Alat (admin) | 6 j | 4 |
| 11 | QA, Keamanan, Deploy | 8 j | semua |
| | **Total** | **≈ 61 j** | |

---

## Fase 0 — Setup (P0)
- [x] Struktur folder MVC, `bootstrap.php`, `.env.example`, `.gitignore`
- [ ] Install PHP ≥ 8.1 + MySQL, salin `.env`, buat database
- [ ] `git init` + commit awal
- [ ] Jalankan `php -l` pada semua file (scaffolding belum dites di mesin pembuat)

## Fase 1 — Database & Migration (P0)
- [x] Migration `users`, `categories`, `items`, `loans`, `loan_items`, `loan_returns`, `loan_status_logs`
- [x] Migrator (`migrate`, `rollback`, `fresh`, `status`)
- [x] Seeder: admin, 5 kategori, 5 alat contoh
- [ ] Jalankan `migrate` + `db:seed` dan verifikasi FK/index di MySQL
- [ ] Uji `migrate:rollback` dan `migrate:fresh`

## Fase 2 — Core MVC (P0)
- [x] Router (param `{id}`), Middleware (`guest`/`auth`/`admin`), CSRF, Session flash, Auth, base Model, base Controller
- [ ] Halaman error 404/403/419/500 yang rapi (view, bukan `echo`)
- [ ] Error handler global + log ke `storage/logs/app.log`
- [ ] Helper validasi input (required, email, date, min/max)

## Fase 3 — Login & Register (P0)
- [x] Form register (nama, email, WA, password) + validasi dasar
- [x] Form login + redirect berdasarkan role
- [x] Logout (POST + CSRF)
- [ ] Rate limit login (mis. 5 gagal / 15 menit per IP+email) — P1
- [ ] Halaman profil (ubah nama, WA, password) — P2
- [ ] Reset password via email — P2

## Fase 4 — Katalog Alat (P0)
- [x] `GET /catalog` dengan filter kategori & pencarian nama
- [ ] Pagination (12 item/halaman)
- [ ] Tampilkan gambar alat (`image_path`) + placeholder
- [ ] Badge kondisi (`baik` / `perlu_perbaikan`); alat `rusak` / `is_active=0` tidak tampil

## Fase 5 — Detail & Ketersediaan (P0)
- [x] `GET /catalog/{slug}` + cek ketersediaan per rentang tanggal (`Item::availableStock`)
- [ ] Unit test / script uji `availableStock` (lihat skenario di `03-flow-documentation.md` §4)
- [ ] Kalender ketersediaan 30 hari ke depan (hijau = tersedia, merah = penuh)
- [ ] Hitung estimasi harga di halaman detail (hari × harga × qty)

## Fase 6 — Pengajuan Peminjaman (P0)
- [ ] Form ajukan di halaman detail: tanggal mulai, tanggal kembali, qty, catatan
- [ ] Keranjang sederhana: beberapa alat dalam satu pengajuan (`loan_items`) — P1 (MVP boleh 1 alat per pengajuan)
- [ ] `Loan::createWithItems()` dalam **transaksi**:
  - validasi `start >= hari ini`, `end >= start`, durasi maks. (mis. 14 hari)
  - cek `availableStock` tiap item
  - snapshot `price_per_day`, hitung `subtotal` & `total_price`
  - generate `code` (`Loan::generateCode()`), insert `loans` + `loan_items`
  - tulis `loan_status_logs` (null → pending)
- [ ] `POST /loans` + flash sukses + redirect ke detail
- [ ] Batalkan pengajuan (`POST /loans/{id}/cancel`, hanya `pending`/`approved`)

## Fase 7 — Approval Admin (P0)
- [x] `GET /admin/loans` (filter status) & detail (tampilan dasar)
- [ ] Tombol **Approve**: transaksi + `SELECT ... FOR UPDATE`, re-check `availableStock(excludeLoanId)`; isi `approved_by`, `approved_at`
- [ ] Tombol **Reject** dengan `rejection_reason` wajib
- [ ] Tombol **Tandai Dipinjam** (approved → borrowed, isi `borrowed_at`)
- [ ] Peringatan jika approval menyebabkan stok bentrok dengan pengajuan lain
- [ ] Dashboard ringkas: jumlah pending, sedang dipinjam, jatuh tempo hari ini — P1

## Fase 8 — Status & Riwayat (P0)
- [x] `GET /loans` (aktif) dan `GET /loans/history` (selesai/ditolak/dibatalkan)
- [x] `GET /loans/{id}` dengan timeline `loan_status_logs`
- [ ] Badge warna per status + penanda **terlambat** (`end_date < today` & status `borrowed`)
- [ ] Auto-refresh status (polling AJAX tiap 15–30 dtk) — "real-time" versi sederhana, P1
- [ ] Notifikasi WhatsApp/email saat status berubah — P2

## Fase 9 — Pengembalian (P0)
- [ ] `POST /loans/{id}/return`: user lapor kembali → insert `loan_returns` + status `returned`
- [ ] `POST /admin/loans/{id}/confirm-return`: admin cek kondisi (`item_condition`), hitung `late_days` & `penalty_amount`, isi `confirmed_by/at`, status `completed`
- [ ] Aturan denda (mis. 100% harga harian per hari terlambat) — dikonfigurasi di `config/`
- [ ] Jika alat rusak/hilang: catat di `note`, update `items.condition_status` / kurangi `stock_total` — P1

## Fase 10 — Manajemen Alat (Admin) (P1)
- [ ] CRUD kategori
- [ ] CRUD alat (nama, kategori, deskripsi, stok, harga/hari, kondisi, gambar, aktif)
- [ ] Upload gambar aman (cek MIME, batas 2 MB, nama acak)
- [ ] Larang hapus alat/kategori yang sudah punya riwayat (pakai `is_active=0`)

## Fase 11 — QA, Keamanan, Deploy (P0)
- [ ] Uji semua transisi status (valid & invalid) sesuai state machine
- [ ] Uji race condition: dua approval bersamaan untuk stok terakhir
- [ ] Review: XSS (`e()`), SQL injection, CSRF, otorisasi (IDOR pada `/loans/{id}`)
- [ ] Ganti Tailwind CDN → build CSS
- [ ] Konfigurasi produksi (`APP_DEBUG=false`, HTTPS, cookie `secure`, password admin baru)
- [ ] Backup DB terjadwal + dokumentasi deploy

---

## Urutan Pengerjaan yang Disarankan (Sprint)

| Sprint | Isi | Hasil yang bisa didemokan |
|---|---|---|
| 1 | Fase 0–3 | Register/login berjalan, DB terbentuk via migration |
| 2 | Fase 4–5 | Katalog + cek ketersediaan tanggal |
| 3 | Fase 6–7 | Ajukan → disetujui/ditolak admin |
| 4 | Fase 8–9 | Status, pengembalian, riwayat (**MVP lengkap**) |
| 5 | Fase 10–11 | Kelola alat, hardening, deploy |

## Definition of Done (per task)
1. Berfungsi sesuai alur di `03-flow-documentation.md`.
2. Input divalidasi; output di-escape; form POST memakai CSRF.
3. Perubahan status hanya lewat `Loan::changeStatus()` (log tercatat).
4. Perubahan skema lewat migration baru, bukan edit tabel manual.
5. Sudah dicoba manual untuk skenario sukses dan gagal.
