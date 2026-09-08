# Plan: Dashboard Manajemen Inventori Gudang (Laravel)

## Tujuan
Aplikasi web dashboard manajemen inventori gudang berbasis Laravel dengan tampilan ERP profesional, tanpa upload/foto produk, hanya teks, angka, ikon, dan badge status.

## Arsitektur
- **Backend**: Laravel 12 (PHP 8.3.29, MySQL 8.0.30 via Laragon, Composer 2.9)
- **Frontend**: Blade + Tailwind CSS v4 (Vite bundler), tanpa React
- **Chart**: Chart.js + chartjs-plugin-annotation
- **Data**: MySQL, diisi via Seeder dummy

## Database (3 tabel)

### `barangs`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama_barang | string | Nama barang |
| kode_sku | string (unique) | Kode/SKU |
| kategori | string | Kategori barang |
| jumlah | integer | Jumlah/stok |
| lokasi_rak | string | Lokasi rak/zona gudang |
| tanggal_masuk | date | Tanggal masuk |
| status | enum('terjual','retur','di_gudang') | Status barang |
| created_at / updated_at | timestamp | Otomatis |

### `konfigurasi_gudangs`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama | string | Label konfigurasi (misal "Kapasitas Gudang Utama") |
| kapasitas_maks | integer | Kapasitas maksimum gudang (global) |
| created_at / updated_at | timestamp | Otomatis |

### `histori_barangs`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| barang_id | bigint FK | Relasi ke barangs |
| aksi | string | 'masuk' / 'keluar' / 'ubah' / 'hapus' |
| detail | text | Rincian perubahan |
| created_at | timestamp | Tercatat otomatis |

> Histori dicatat otomatis via Eloquent Model Events (created/updated/deleted).

## Halaman & Fitur

### Layout Inti (`layouts/app.blade.php`)
- Sidebar navigasi: Dashboard, Data Barang, Kapasitas Gudang, Riwayat
- Header: judul halaman + jam real-time (JS setInterval, HH:MM:SS + tanggal) + summary cards
- Warna korporat netral: putih, abu-abu, biru navy sebagai aksen

### Summary Cards (header)
- Total SKU Barang
- Total Stok (jumlah keseluruhan)
- Jumlah Retur (badge amber)
- Jumlah Lokasi Rak terpakai
- Elemen OVERLOAD tampil merah jika total stok > kapasitas

### 1. Dashboard
- 4 card ringkasan
- Bar Chart (Chart.js): X = kategori, bar = stok aktual (biru navy), garis datar merah putus-putus = kapasitas maks global (annotation plugin)
- Jika total stok > kapasitas → bar berubah merah + banner OVERLOAD merah
- Tabel 5 barang terbaru

### 2. Data Barang (CRUD tanpa upload gambar)
- Tabel bersih: nama, SKU, kategori, jumlah, lokasi rak, tanggal masuk, status badge, aksi
- Badge status:
  - `di_gudang` → emerald (hijau)
  - `retur` → amber
  - `terjual` → abu-biru navy
- Aksi: lihat (detail), edit, hapus (konfirmasi)
- **Search** berdasarkan nama/SKU
- **Filter** server-side: status, kategori, rentang tanggal (dari–sampai), tombol reset
- Form create/edit dengan validasi Laravel di server

### 3. Kapasitas Gudang
- CRUD record `konfigurasi_gudangs` (tambah/ubah/hapus)
- Chart dashboard membaca kapasitas terbaru

### 4. Riwayat
- Tabel log perubahan barang dengan timestamp otomatis (masuk/keluar/ubah/hapus)

## Data Dummy (Seeder)
- ~25 barang lintas 5-6 kategori (Elektronik, Furniture, Makanan, ATK, Sparepart, Tekstil)
- Status beragam (terjual/retur/di_gudang)
- Lokasi rak bervariasi (misal "Rak A-01", "Zona B")
- Konfigurasi kapasitas diset agar menghasilkan kondisi normal + sebagian mendekati batas (grafik menarik)

## Langkah Eksekusi
1. `composer create-project laravel/laravel .` di working directory
2. Konfigurasi `.env` (DB `dashboard_gudang`, user `root`, password kosong) + buat database MySQL
3. `npm install` + pasang Tailwind CSS v4 (`tailwindcss`, `@tailwindcss/vite`) & Chart.js + chartjs-plugin-annotation; atur `vite.config.js`
4. Tulis migrations, models, controllers, routes
5. Tulis Blade layout + semua halaman (sidebar, header/clock, dashboard, CRUD, filter, riwayat)
6. Seeder data dummy; `php artisan migrate --seed`
7. `npm run build`
8. Jalankan `php artisan serve` (atau buka via URL Laragon `dashboard-gudang.test`) dan verifikasi semua fitur

## Catatan
- Tidak ada fitur upload/foto produk (sesuai spesifikasi)
- Kecuali disebut berbeda, bahasa UI memakai Bahasa Indonesia
- Timestamp otomatis: `created_at/updated_at` di setiap record + tabel `histori_barangs`