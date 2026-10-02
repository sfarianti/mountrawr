# 04 · Kontrak Data Model — Mountrawr

Sumber kebenaran skema: `database/migrations/`. Dokumen ini adalah **kontrak**: nama tabel, tipe kolom, constraint, relasi, dan aturan bisnis. Ubah kontrak → buat migration baru + perbarui dokumen ini.

Engine `InnoDB`, charset `utf8mb4`, collation `utf8mb4_unicode_ci`. PK `BIGINT UNSIGNED AUTO_INCREMENT`.

## 1. ERD

```mermaid
erDiagram
  users ||--o{ loans : "mengajukan (user_id)"
  users ||--o{ loans : "menyetujui (approved_by)"
  users ||--o{ loan_returns : "mengonfirmasi (confirmed_by)"
  users ||--o{ loan_status_logs : "mengubah (changed_by)"
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
    bigint approved_by FK
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
    bigint confirmed_by FK
  }
  loan_status_logs {
    bigint id PK
    bigint loan_id FK
    varchar from_status
    varchar to_status
    bigint changed_by FK
  }
```

## 2. Ringkasan Relasi

| Relasi | Kardinalitas | FK | ON DELETE |
|---|---|---|---|
| categories → items | 1 : N | `items.category_id` | RESTRICT |
| users → loans (peminjam) | 1 : N | `loans.user_id` | RESTRICT |
| users → loans (penyetuju) | 1 : N | `loans.approved_by` (nullable) | SET NULL |
| loans → loan_items | 1 : N (min. 1) | `loan_items.loan_id` | CASCADE |
| items → loan_items | 1 : N | `loan_items.item_id` | RESTRICT |
| loans → loan_returns | 1 : 0..1 | `loan_returns.loan_id` (UNIQUE) | CASCADE |
| users → loan_returns | 1 : N | `loan_returns.confirmed_by` (nullable) | SET NULL |
| loans → loan_status_logs | 1 : N | `loan_status_logs.loan_id` | CASCADE |
| users → loan_status_logs | 1 : N | `loan_status_logs.changed_by` (nullable) | SET NULL |
| loans ↔ items | N : M | lewat tabel `loan_items` | — |

## 3. Kontrak Field per Tabel

### 3.1 `users`
| Field | Tipe | Null | Default | Constraint / Aturan |
|---|---|---|---|---|
| id | BIGINT UNSIGNED | – | AI | PK |
| name | VARCHAR(100) | NO | | wajib |
| email | VARCHAR(150) | NO | | UNIQUE; disimpan lowercase; format valid |
| password | VARCHAR(255) | NO | | hasil `password_hash()` — **jangan pernah** plaintext |
| phone | VARCHAR(20) | YES | NULL | nomor WhatsApp |
| role | ENUM('peminjam','admin') | NO | `peminjam` | register publik selalu `peminjam`; `admin` hanya via seeder/manual |
| created_at / updated_at | TIMESTAMP | YES | CURRENT_TIMESTAMP | updated_at auto-update |

### 3.2 `categories`
| Field | Tipe | Null | Default | Constraint / Aturan |
|---|---|---|---|---|
| id | BIGINT UNSIGNED | – | AI | PK |
| name | VARCHAR(100) | NO | | UNIQUE |
| slug | VARCHAR(120) | NO | | UNIQUE; dipakai di filter URL |
| description | TEXT | YES | NULL | |
| created_at / updated_at | TIMESTAMP | YES | CURRENT_TIMESTAMP | |

### 3.3 `items` (alat pendakian)
| Field | Tipe | Null | Default | Constraint / Aturan |
|---|---|---|---|---|
| id | BIGINT UNSIGNED | – | AI | PK |
| category_id | BIGINT UNSIGNED | NO | | FK → categories.id (RESTRICT) |
| name | VARCHAR(150) | NO | | |
| slug | VARCHAR(170) | NO | | UNIQUE; dipakai di URL detail |
| description | TEXT | YES | NULL | |
| stock_total | INT UNSIGNED | NO | 1 | jumlah unit fisik yang dimiliki |
| price_per_day | DECIMAL(12,2) | NO | 0 | harga sewa per hari per unit (0 = gratis/pinjam) |
| condition_status | ENUM('baik','perlu_perbaikan','rusak') | NO | `baik` | `rusak` tidak boleh dipinjamkan |
| image_path | VARCHAR(255) | YES | NULL | path relatif di `public/uploads/` |
| is_active | TINYINT(1) | NO | 1 | soft-hide dari katalog (jangan hapus bila ada riwayat) |
| created_at / updated_at | TIMESTAMP | YES | CURRENT_TIMESTAMP | |

Index: `idx_items_category(category_id)`, `idx_items_active(is_active)`.

### 3.4 `loans` (header peminjaman)
| Field | Tipe | Null | Default | Constraint / Aturan |
|---|---|---|---|---|
| id | BIGINT UNSIGNED | – | AI | PK |
| code | VARCHAR(30) | NO | | UNIQUE; format `MTR-YYYYMMDD-XXXX` |
| user_id | BIGINT UNSIGNED | NO | | FK → users.id (RESTRICT) — peminjam |
| start_date | DATE | NO | | ≥ tanggal pengajuan |
| end_date | DATE | NO | | ≥ start_date; batas durasi maks. diatur aplikasi |
| status | ENUM('pending','approved','rejected','cancelled','borrowed','returned','completed') | NO | `pending` | hanya diubah via `Loan::changeStatus()` |
| total_price | DECIMAL(12,2) | NO | 0 | Σ `loan_items.subtotal` (di-snapshot saat pengajuan) |
| note | TEXT | YES | NULL | catatan peminjam |
| rejection_reason | VARCHAR(255) | YES | NULL | **wajib** bila status `rejected` |
| approved_by | BIGINT UNSIGNED | YES | NULL | FK → users.id (SET NULL); admin penyetuju/penolak |
| approved_at | DATETIME | YES | NULL | |
| borrowed_at | DATETIME | YES | NULL | saat alat diambil |
| created_at / updated_at | TIMESTAMP | YES | CURRENT_TIMESTAMP | |

Index: `idx_loans_user(user_id)`, `idx_loans_status(status)`, `idx_loans_dates(start_date,end_date)`.

### 3.5 `loan_items` (detail peminjaman, pivot loans ↔ items)
| Field | Tipe | Null | Default | Constraint / Aturan |
|---|---|---|---|---|
| id | BIGINT UNSIGNED | – | AI | PK |
| loan_id | BIGINT UNSIGNED | NO | | FK → loans.id (CASCADE) |
| item_id | BIGINT UNSIGNED | NO | | FK → items.id (RESTRICT) |
| quantity | INT UNSIGNED | NO | 1 | ≥ 1; ≤ sisa stok saat pengajuan/approve |
| price_per_day | DECIMAL(12,2) | NO | 0 | **snapshot** harga saat pengajuan (harga item bisa berubah kemudian) |
| subtotal | DECIMAL(12,2) | NO | 0 | `price_per_day × quantity × jumlah_hari` |
| created_at | TIMESTAMP | YES | CURRENT_TIMESTAMP | |

Constraint: `UNIQUE(loan_id, item_id)` — satu alat hanya satu baris per loan (gabungkan qty). Index `idx_loan_items_item(item_id)`.
`jumlah_hari = DATEDIFF(end_date, start_date) + 1` (inklusif).

### 3.6 `loan_returns` (pengembalian)
| Field | Tipe | Null | Default | Constraint / Aturan |
|---|---|---|---|---|
| id | BIGINT UNSIGNED | – | AI | PK |
| loan_id | BIGINT UNSIGNED | NO | | UNIQUE + FK → loans.id (CASCADE): maks. satu pengembalian per loan |
| returned_at | DATETIME | NO | | waktu peminjam melapor/menyerahkan |
| item_condition | ENUM('baik','rusak_ringan','rusak_berat','hilang') | NO | `baik` | diisi admin saat konfirmasi |
| late_days | INT UNSIGNED | NO | 0 | `max(0, DATE(returned_at) − end_date)` |
| penalty_amount | DECIMAL(12,2) | NO | 0 | denda keterlambatan/kerusakan (aturan di config) |
| note | TEXT | YES | NULL | |
| confirmed_by | BIGINT UNSIGNED | YES | NULL | FK → users.id (SET NULL); NULL = belum dikonfirmasi |
| confirmed_at | DATETIME | YES | NULL | |
| created_at / updated_at | TIMESTAMP | YES | CURRENT_TIMESTAMP | |

### 3.7 `loan_status_logs` (audit / timeline status)
| Field | Tipe | Null | Default | Constraint / Aturan |
|---|---|---|---|---|
| id | BIGINT UNSIGNED | – | AI | PK |
| loan_id | BIGINT UNSIGNED | NO | | FK → loans.id (CASCADE) |
| from_status | VARCHAR(20) | YES | NULL | NULL untuk log pertama (pembuatan loan) |
| to_status | VARCHAR(20) | NO | | |
| changed_by | BIGINT UNSIGNED | YES | NULL | FK → users.id (SET NULL); NULL = sistem |
| note | VARCHAR(255) | YES | NULL | alasan/keterangan |
| created_at | TIMESTAMP | YES | CURRENT_TIMESTAMP | |

Index: `idx_logs_loan(loan_id, created_at)`. Log bersifat **append-only** (tidak di-update/delete oleh aplikasi).

### 3.8 `migrations` (dikelola Migrator)
`id`, `migration` (UNIQUE), `batch`, `executed_at`.

## 4. Aturan Integritas Bisnis (di luar FK)

1. **Stok tidak boleh minus**: total `quantity` loan `approved/borrowed/returned` yang overlap tanggal ≤ `items.stock_total` (dicek saat pengajuan dan **ulang saat approve** dalam transaksi).
2. **Transisi status** hanya sesuai `Loan::TRANSITIONS` (lihat `03-flow-documentation.md` §3).
3. Setiap perubahan status ⇒ tepat satu baris `loan_status_logs`, dalam transaksi yang sama.
4. `rejection_reason` wajib saat `rejected`; `approved_by/approved_at` wajib saat `approved`; `borrowed_at` wajib saat `borrowed`.
5. `loan_returns` hanya boleh ada bila status loan ∈ {`returned`, `completed`}; `confirmed_*` wajib saat `completed`.
6. `loans.total_price` = Σ `loan_items.subtotal` dan **tidak** dihitung ulang dari harga item terbaru.
7. Alat dengan `condition_status = 'rusak'` atau `is_active = 0` tidak boleh masuk loan baru.
8. User hanya boleh mengakses loan miliknya (`loans.user_id = session user`), kecuali `admin`.
9. Data master (`items`, `categories`, `users`) yang punya riwayat tidak dihapus (FK RESTRICT) — nonaktifkan lewat `is_active`.

## 5. Kontrak Payload (input → field)

**Register** `POST /register`
```json
{ "name": "string ≤100", "email": "valid email, unik", "phone": "string ≤20 | kosong",
  "password": "string ≥8", "password_confirmation": "= password" }
```

**Ajukan peminjaman** `POST /loans`
```json
{ "start_date": "YYYY-MM-DD (≥ hari ini)", "end_date": "YYYY-MM-DD (≥ start_date)",
  "items": [ { "item_id": 1, "quantity": 2 } ], "note": "string | kosong" }
```
Respon sukses: redirect ke `/loans/{id}`; status awal `pending`.

**Reject** `POST /admin/loans/{id}/reject`
```json
{ "rejection_reason": "string ≤255, wajib" }
```

**Konfirmasi pengembalian** `POST /admin/loans/{id}/confirm-return`
```json
{ "item_condition": "baik | rusak_ringan | rusak_berat | hilang", "penalty_amount": "decimal ≥0 (opsional, default dihitung)", "note": "string | kosong" }
```
(Semua POST juga membawa `_csrf`.)

## 6. Query Kunci

**Sisa stok** — `Item::availableStock(itemId, start, end, excludeLoanId?)`:
```sql
SELECT i.stock_total - COALESCE((
    SELECT SUM(li.quantity)
    FROM loan_items li JOIN loans l ON l.id = li.loan_id
    WHERE li.item_id = i.id
      AND l.status IN ('approved','borrowed','returned')
      AND l.start_date <= :end AND l.end_date >= :start
), 0) AS available
FROM items i WHERE i.id = :id;
```

**Loan terlambat**:
```sql
SELECT * FROM loans WHERE status = 'borrowed' AND end_date < CURDATE();
```

## 7. Seed Data Awal
- 1 admin: `admin@mountrawr.test` / `admin12345` (ganti segera).
- Kategori: Tenda, Carrier & Tas, Alat Masak, Sleeping Gear, Penerangan.
- 5 alat contoh dengan stok dan harga per hari.
