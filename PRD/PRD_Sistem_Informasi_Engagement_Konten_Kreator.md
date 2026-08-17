# Product Requirement Document (PRD) & Technical Specification
## Sistem Informasi Manajemen Engagement Konten Kreator

---

## 1. Overview & Project Goals
Sistem Informasi Manajemen Engagement Konten Kreator adalah platform berbasis web (Laravel) yang dirancang untuk mengotomatisasi pencatatan, pemantauan performa konten (TikTok & Instagram), integrasi *scraping engine* (Apify API), serta perankingan performa konten menggunakan metode **Simple Additive Weighting (SAW)**.

### Tech Stack & Core Requirements:
- **Backend Framework:** Laravel 12 (PHP 8.2+)
- **Database:** MySQL
- **Scraping Engine:** Apify API (TikTok Scraper / Instagram Scraper Actors)
- **Frontend Architecture:** Modular Blade Components (Pagination, Tables, Toast, Skeleton Loader, Modal, KPI Cards)
- **Typography:** Font **Montserrat** (Google Fonts)
- **Theme & Design System:** Clean & modern design, dual-mode (Dark / Light Mode) dengan Base Color variabel (Biru, Putih, Ungu)
- **Export Formats:** PDF (DomPDF/Snappy) & Excel (Maatwebsite/Laravel-Excel)

---

## 2. Design System & Theme Tokens (CSS Variables)

Sistem menggunakan variabel CSS murni untuk mendukung tema gelap & terang serta konsistensi warna basis di seluruh modul.

```css
:root {
  --font-family-base: 'Montserrat', sans-serif;

  /* Base Brand Colors (Light Mode) */
  --brand-blue: #2563eb;
  --brand-blue-hover: #1d4ed8;
  --brand-purple: #7c3aed;
  --brand-purple-hover: #6d28d9;
  --brand-gradient: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);

  /* Neutral / Surface */
  --bg-body: #f8fafc;
  --bg-surface: #ffffff;
  --bg-card: #ffffff;
  --border-color: #e2e8f0;
  
  /* Typography */
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;

  /* Status Tokens */
  --status-success: #10b981;
  --status-warning: #f59e0b;
  --status-danger: #ef4444;
  --status-info: #0ea5e9;
}

[data-theme="dark"] {
  /* Base Brand Colors (Dark Mode) */
  --brand-blue: #3b82f6;
  --brand-blue-hover: #60a5fa;
  --brand-purple: #8b5cf6;
  --brand-purple-hover: #a78bfa;
  --brand-gradient: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);

  /* Neutral / Surface */
  --bg-body: #0f172a;
  --bg-surface: #1e293b;
  --bg-card: #1e293b;
  --border-color: #334155;

  /* Typography */
  --text-primary: #f8fafc;
  --text-secondary: #94a3b8;
  --text-muted: #64748b;
}
```

---

## 3. Database Schema & Data Dictionary

### 3.1. Tabel: `users`
Menyimpan data otentikasi dan hak peran pengguna.

| No | Nama Kolom | Tipe Data | Ukuran | Default / Key | Keterangan |
|---|---|---|---|---|---|
| 1 | `id` | BIGINT UNSIGNED | - | Primary Key (Auto Inc) | ID unik user |
| 2 | `name` | VARCHAR | 255 | NOT NULL | Nama lengkap pengguna |
| 3 | `username` | VARCHAR | 255 | UNIQUE, NOT NULL | Username unik untuk login |
| 4 | `email` | VARCHAR | 255 | UNIQUE, NOT NULL | Alamat email unik |
| 5 | `password` | VARCHAR | 255 | NOT NULL | Password terenkripsi (Hash) |
| 6 | `role` | VARCHAR | 50 | NOT NULL | `Admin Master`, `Admin`, atau `Client` |
| 7 | `status` | VARCHAR | 50 | 'Aktif' | Status pengguna: `Aktif` / `Nonaktif` |
| 8 | `created_at` | TIMESTAMP | - | NULL | Waktu dibuat |
| 9 | `updated_at` | TIMESTAMP | - | NULL | Waktu diubah |

---

### 3.2. Tabel: `campaigns`
Menyimpan data entitas campaign pemasaran.

| No | Nama Kolom | Tipe Data | Ukuran | Default / Key | Keterangan |
|---|---|---|---|---|---|
| 1 | `id` | BIGINT UNSIGNED | - | Primary Key (Auto Inc) | ID unik campaign |
| 2 | `client_id` | BIGINT UNSIGNED | - | Foreign Key (`users.id`) | Relasi ke user pemilik (Role: Client) |
| 3 | `nama_campaign` | VARCHAR | 255 | NOT NULL | Nama/judul campaign |
| 4 | `platform` | VARCHAR | 100 | NOT NULL | Platform (`TikTok`, `Instagram`, `All`) |
| 5 | `tanggal_mulai` | DATE | - | NOT NULL | Tanggal mulai campaign |
| 6 | `tanggal_selesai` | DATE | - | NOT NULL | Tanggal selesai campaign |
| 7 | `deskripsi` | TEXT | - | NULL | Ringkasan/detail instruksi campaign |
| 8 | `status` | VARCHAR | 50 | 'Draft' | Status: `Draft`, `Aktif`, `Selesai`, `Arsip` |
| 9 | `created_at` | TIMESTAMP | - | NULL | Waktu dibuat |
| 10 | `updated_at` | TIMESTAMP | - | NULL | Waktu diubah |
| 11 | `deleted_at` | TIMESTAMP | - | NULL | Soft delete support |

---

### 3.3. Tabel: `user_campaign_access`
Tabel pivot untuk manajemen kontrol akses campaign per user (Admin / Client).

| No | Nama Kolom | Tipe Data | Ukuran | Default / Key | Keterangan |
|---|---|---|---|---|---|
| 1 | `id` | BIGINT UNSIGNED | - | Primary Key (Auto Inc) | ID akses |
| 2 | `user_id` | BIGINT UNSIGNED | - | Foreign Key (`users.id`) | Relasi user yang diberikan akses |
| 3 | `campaign_id` | BIGINT UNSIGNED | - | Foreign Key (`campaigns.id`) | Relasi campaign yang diizinkan |

---

### 3.4. Tabel: `kategori_konten`
Menyimpan master data kategori jenis konten.

| No | Nama Kolom | Tipe Data | Ukuran | Default / Key | Keterangan |
|---|---|---|---|---|---|
| 1 | `id` | BIGINT UNSIGNED | - | Primary Key (Auto Inc) | ID kategori konten |
| 2 | `nama` | VARCHAR | 100 | NOT NULL | Nama kategori (misal: *Product Review*, *Storytelling*) |

---

### 3.5. Tabel: `kategori_creator`
Menyimpan master data klasifikasi kreator.

| No | Nama Kolom | Tipe Data | Ukuran | Default / Key | Keterangan |
|---|---|---|---|---|---|
| 1 | `id` | BIGINT UNSIGNED | - | Primary Key (Auto Inc) | ID kategori creator |
| 2 | `nama` | VARCHAR | 100 | NOT NULL | Kategori creator (misal: *KOL*, *Clipper*, *Micro*, *Nano*) |

---

### 3.6. Tabel: `links`
Menyimpan link konten, hasil scraping metrik engagement, dan kalkulasi skor SAW.

| No | Nama Kolom | Tipe Data | Ukuran | Default / Key | Keterangan |
|---|---|---|---|---|---|
| 1 | `id` | BIGINT UNSIGNED | - | Primary Key (Auto Inc) | ID unik link |
| 2 | `campaign_id` | BIGINT UNSIGNED | - | Foreign Key (`campaigns.id`) | Foreign key ke campaign |
| 3 | `kategori_konten_id` | BIGINT UNSIGNED | - | Foreign Key (`kategori_konten.id`) | Foreign key ke kategori konten |
| 4 | `kategori_creator_id` | BIGINT UNSIGNED | - | Foreign Key (`kategori_creator.id`) | Foreign key ke kategori creator |
| 5 | `url` | VARCHAR | 500 | NOT NULL | URL link konten (TikTok/Instagram) |
| 6 | `username` | VARCHAR | 255 | NULL | Handle akun kreator hasil scraping |
| 7 | `platform` | VARCHAR | 50 | NULL | Platform terdeteksi (`TikTok` / `Instagram`) |
| 8 | `caption` | TEXT | - | NULL | Teks caption postingan |
| 9 | `tanggal_upload` | DATE | - | NULL | Tanggal upload konten hasil scraping |
| 10 | `views` | BIGINT | - | 0 | Metrik jumlah views |
| 11 | `likes` | BIGINT | - | 0 | Metrik jumlah suka |
| 12 | `comments` | BIGINT | - | 0 | Metrik jumlah komentar |
| 13 | `saves` | BIGINT | - | 0 | Metrik jumlah simpan / bookmark |
| 14 | `shares` | BIGINT | - | 0 | Metrik jumlah bagikan |
| 15 | `reposts` | BIGINT | - | 0 | Metrik jumlah posting ulang |
| 16 | `engagement_rate` | DECIMAL | 5,2 | 0.00 | Persentase ER (Rate %) |
| 17 | `saw_score` | DECIMAL | 8,4 | 0.0000 | Nilai skor preferensi SAW |
| 18 | `status_scraping` | VARCHAR | 50 | 'Pending' | Status: `Pending`, `Berhasil`, `Gagal` |
| 19 | `updated_at` | DATETIME | - | NULL | Waktu scraping/update terakhir |
| 20 | `created_at` | TIMESTAMP | - | NULL | Waktu dibuat |

---

## 4. System Architecture & Flow Processes

### 4.1. End-to-End Scraping & SAW Computation Flow
```
[Admin / Admin Master]
        │
        ▼ (Pilih Mode: Single / Bulk / CSV)
[Input Link Konten]
        │
        ▼
[LinkController::store / processCSV]
        │ ── (Validasi Format URL, Duplikasi, Assigned Access)
        ▼
[Database: Simpan Links (status_scraping = 'Pending')]
        │
        ▼
[Dispatch Queue Job: ProcessApifyScraping]
        │
        ▼
[Apify Actor Engine API Request]
        │
        ├──► [Gagal Respons / Limit API] ──► Update status_scraping = 'Gagal'
        │
        └──► [Sukses Respons (HTTP 200)] ──► Parse Payload (views, likes, comments, saves, shares, reposts)
                                                 │
                                                 ▼
                                     [SAW Calculation Service]
                                     - Ambil semua record link di Campaign tsb
                                     - Hitung Max Kriteria (Benefit)
                                     - Normalisasi & Perkalian Bobot
                                     - Update saw_score & status_scraping = 'Berhasil'
                                                 │
                                                 ▼
                                     [Trigger Realtime/Toast & Refresh Dashboard Data]
```

### 4.2. Algoritma & Bobot SAW (*Simple Additive Weighting*)
Semua kriteria engagement bersifat **Benefit** (semakin tinggi nilainya semakin baik):
- **Shares:** Bobot $W_1 = 0.35$ (35%)
- **Comments:** Bobot $W_2 = 0.25$ (25%)
- **Likes:** Bobot $W_3 = 0.20$ (20%)
- **Views:** Bobot $W_4 = 0.10$ (10%)
- **Saves:** Bobot $W_5 = 0.10$ (10%)

#### Tahap 1: Normalisasi Matriks ($R$)
$$r_{ij} = rac{x_{ij}}{\max_i(x_{ij})}$$

#### Tahap 2: Nilai Preferensi ($V_i$)
$$V_i = (W_1 	imes r_{i,	ext{shares}}) + (W_2 	imes r_{i,	ext{comments}}) + (W_3 	imes r_{i,	ext{likes}}) + (W_4 	imes r_{i,	ext{views}}) + (W_5 	imes r_{i,	ext{saves}})$$

---

## 5. Modular Components Checklist

1. **`x-pagination`**: Komponen pagination modular yang mendukung *theme switching*, query string preservation, dan responsif di mobile.
2. **`x-toast`**: Global notification toast (Success, Error, Warning, Info) dengan timer auto-dismiss dan icon status.
3. **`x-skeleton`**: Komponen skeleton loader animasi shimmer saat data di-fetch secara AJAX / refresh berlangsung.
4. **`x-modal`**: Reusable modal dialog wrapper untuk popup Form Input Link, CRUD Kategori, dan Modal Assign Campaign Access.
5. **`x-kpi-card`**: Kartu statistik metric dengan aksen warna basis (`var(--brand-gradient)`), persentase tren, dan ikon representatif.
6. **`x-data-table`**: Komponen table wrapper dengan dukungan filter platform, pencarian, sortable columns, dan empty state.
7. **`x-theme-toggle`**: Tombol toggle Dark/Light Mode dengan persistensi local storage dan otomatis inject atribut `data-theme`.

---

## 6. Role-Based Access Control (RBAC) Matrix

| Modul / Fitur | Admin Master | Admin | Client |
|---|:---:|:---:|:---:|
| **Autentikasi** (Login, Logout, Profil) | ✅ | ✅ | ✅ |
| **Kelola User** (CRUD Akun, Set Role & Status) | ✅ | ❌ | ❌ |
| **Kelola Kategori Konten** (CRUD Master) | ✅ | ❌ | ❌ |
| **Kelola Kategori Creator** (CRUD Master) | ✅ | ❌ | ❌ |
| **Kelola Campaign** (CRUD, Ganti Status, Arsip) | ✅ | ❌ | ❌ |
| **Kelola Hak Akses Campaign** (Assign User ke Campaign) | ✅ | ❌ | ❌ |
| **Input Link Konten** (Single, Bulk, Upload CSV) | ✅ | ✅ (Assigned) | ❌ |
| **Refresh Data Scraping** (Trigger Apify & Hitung SAW) | ✅ | ✅ (Assigned) | ❌ |
| **Dashboard Campaign** (KPI, Chart Engagement, Breakdown) | ✅ (Semua) | ✅ (Assigned) | ✅ (Assigned) |
| **Top Content SAW Ranking & Detail Konten** | ✅ | ✅ | ✅ |
| **Export Laporan** (PDF & Excel) | ✅ | ✅ | ✅ |
