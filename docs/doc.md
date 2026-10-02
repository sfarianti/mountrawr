# Dokumentasi Sistem Mountrawr

Platform Persewaan Peralatan Outdoor & Pendakian Gunung (Native PHP MVC).

---

## 1. Informasi Database Lokal

- **Nama Database pada Local**: `mountrawr`
- **Host**: `127.0.0.1` (atau `localhost`)
- **Port**: `3306`
- **Username Default**: `root`
- **Password Default**: *(kosong)*
- **Engine**: `InnoDB`
- **Charset / Collation**: `utf8mb4` / `utf8mb4_unicode_ci`

---

## 2. Arsitektur & Tech Stack

| Layer | Teknologi / Spesifikasi | Keterangan |
|---|---|---|
| **Language** | PHP ≥ 8.1 (Native) | Autoload PSR-4 tanpa Composer (`bootstrap.php`) |
| **Architecture** | Custom MVC | Router → Middleware → Controller → Model (PDO) → View |
| **Database** | MySQL 8.0 / MariaDB 10.6+ | Akses data menggunakan PDO Prepared Statements |
| **Frontend Styling** | Tailwind CSS + Plus Jakarta Sans | Modern dark-mode outdoor glassmorphism UI |
| **CLI Tool** | Native CLI (`mountrawr`) | Perintah `migrate`, `db:seed`, `serve` |

---

## 3. Skema Data & Entity Relationship Diagram (ERD)

Database **`mountrawr`** terdiri dari 8 tabel:

```mermaid
erDiagram
  users ||--o{ loans : "mengajukan"
  users ||--o{ loans : "menyetujui"
  users ||--o{ loan_returns : "mengonfirmasi"
  users ||--o{ loan_status_logs : "mengubah"
  categories ||--o{ items : "mengelompokkan"
  loans ||--|{ loan_items : "berisi"
  items ||--o{ loan_items : "dipinjam sebagai"
  loans ||--o| loan_returns : "dikembalikan lewat"
  loans ||--o{ loan_status_logs : "tercatat di"

  users {
    bigint id PK
    varchar name
    varchar email UK
    varchar password
    varchar phone
    enum role
  }
  categories {
    bigint id PK
    varchar name UK
    varchar slug UK
    text description
  }
  items {
    bigint id PK
    bigint category_id FK
    varchar name
    varchar slug UK
    int stock_total
    decimal price_per_day
    enum condition_status
    tinyint is_active
  }
  loans {
    bigint id PK
    varchar code UK
    bigint user_id FK
    date start_date
    date end_date
    enum status
    decimal total_price
  }
  loan_items {
    bigint id PK
    bigint loan_id FK
    bigint item_id FK
    int quantity
    decimal price_per_day
    decimal subtotal
  }
  loan_returns {
    bigint id PK
    bigint loan_id FK_UK
    datetime returned_at
    enum item_condition
    int late_days
    decimal penalty_amount
  }
  loan_status_logs {
    bigint id PK
    bigint loan_id FK
    varchar from_status
    varchar to_status
    bigint changed_by FK
  }
```

---

## 4. State Machine & Aturan Bisnis

Status peminjaman disimpan di kolom `loans.status` dengan transisi berikut:

```
[*] -> pending -> approved -> borrowed -> returned -> completed
           |           |
           +-> rejected +-> cancelled
           |
           +-> cancelled
```

### Rule & Constraint Utama:
1. **Pemeriksaan Stok Real-time**: `Item::availableStock(itemId, start_date, end_date, excludeLoanId)` menghitung sisa stok alat dengan mengurangi total unit fisik terhadap jumlah dipinjam pada peminjaman berstatus `approved`, `borrowed`, atau `returned` yang overlap tanggalnya.
2. **Double-Check Approval**: Saat admin menyetujui (`approve`), sistem melakukan re-check stok dalam transaksi DB untuk mencegah *race condition* jika dua pengajuan disetujui bersamaan.
3. **Mandatory Rejection Reason**: Penolakan pengajuan (`reject`) wajib menyertakan alasan penolakan.
4. **Audit Logging**: Setiap perubahan status otomatis mencatat baris baru pada `loan_status_logs` lengkap dengan ID pengguna dan waktu perubahan.

---

## 5. Cara Menjalankan & Perintah CLI

```bash
# 1. Konfigurasi Environment
cp .env.example .env

# 2. Jalankan Migration Database (database: mountrawr)
php mountrawr migrate

# 3. Jalankan Seeder Data Awal
php mountrawr db:seed

# 4. Jalankan Dev Server
php mountrawr serve
```

### Akun Bawaan Seeder:
- **Admin**: `admin@mountrawr.test` / `admin12345`
- **Peminjam**: `user@mountrawr.test` / `user12345`

---

## 6. Daftar Route Utama

| Method | Path | Controller & Method | Access Middleware |
|---|---|---|---|
| GET | `/` | `HomeController@index` | Public |
| GET/POST | `/login` | `AuthController@showLogin / login` | Guest |
| GET/POST | `/register` | `AuthController@showRegister / register` | Guest |
| POST | `/logout` | `AuthController@logout` | Auth |
| GET | `/catalog` | `CatalogController@index` | Public |
| GET | `/catalog/{slug}` | `CatalogController@show` | Public |
| GET | `/loans` | `LoanController@index` | Auth |
| GET | `/loans/history` | `LoanController@history` | Auth |
| POST | `/loans` | `LoanController@store` | Auth |
| GET | `/loans/{id}` | `LoanController@show` | Auth |
| POST | `/loans/{id}/cancel` | `LoanController@cancel` | Auth |
| POST | `/loans/{id}/return` | `LoanController@returnItem` | Auth |
| GET | `/admin/loans` | `Admin\LoanController@index` | Admin |
| GET | `/admin/loans/{id}` | `Admin\LoanController@show` | Admin |
| POST | `/admin/loans/{id}/approve` | `Admin\LoanController@approve` | Admin |
| POST | `/admin/loans/{id}/reject` | `Admin\LoanController@reject` | Admin |
| POST | `/admin/loans/{id}/borrowed` | `Admin\LoanController@markBorrowed` | Admin |
| POST | `/admin/loans/{id}/confirm-return` | `Admin\LoanController@confirmReturn` | Admin |
| GET/POST | `/admin/items` | `Admin\ItemController@index / store` | Admin |
| GET/POST | `/admin/categories` | `Admin\CategoryController@index / store` | Admin |

---
*Dokumentasi ini dibuat untuk platform Mountrawr.*
