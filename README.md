<div align="center">

<img src="public/favicon.svg" alt="BisnisKu Logo" width="80" height="80">

# BisnisKu — Sistem POS & Business Intelligence

**Platform manajemen bisnis lengkap berbasis web untuk toko kecil hingga menengah.**  
Dilengkapi dengan Point of Sale, analisis kecerdasan bisnis, katalog produk publik, layanan reparasi, dan integrasi pembayaran digital.

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-4.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Midtrans](https://img.shields.io/badge/Midtrans-Payment-003E84?style=for-the-badge)](https://midtrans.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

</div>

---

## 📋 Daftar Isi

- [Tentang BisnisKu](#-tentang-bisnisku)
- [Fitur Utama](#-fitur-utama)
- [Preview Tampilan](#-preview-tampilan)
- [Arsitektur Sistem](#-arsitektur-sistem)
- [Tech Stack](#-tech-stack)
- [Persyaratan](#-persyaratan)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Peran & Hak Akses](#-peran--hak-akses)
- [Business Intelligence](#-business-intelligence)
- [Integrasi Pihak Ketiga](#-integrasi-pihak-ketiga)
- [Struktur Direktori](#-struktur-direktori)
- [Lisensi](#-lisensi)

---

## 🏪 Tentang BisnisKu

**BisnisKu** adalah sistem Point of Sale (POS) berbasis web yang dirancang khusus untuk pemilik toko kecil hingga menengah. Lebih dari sekadar kasir digital, BisnisKu hadir dengan fitur **Business Intelligence** yang menggunakan algoritma **K-Means Clustering** dan **Simple Moving Average (SMA)** untuk membantu pemilik toko membuat keputusan bisnis yang lebih cerdas — mulai dari analisis pergerakan produk hingga prediksi kebutuhan restock.

### 🎯 Tujuan Proyek
- Mempermudah operasional kasir dan manajemen stok sehari-hari
- Memberikan wawasan bisnis berbasis data kepada pemilik toko non-teknis
- Menyediakan katalog produk publik yang bisa diakses pelanggan secara real-time
- Mengintegrasikan pembayaran digital (Transfer Bank & QRIS) secara seamless

---

## ✨ Fitur Utama

### 🛒 Point of Sale (POS)
- Transaksi penjualan cepat dengan antarmuka kasir yang bersih
- Dukungan berbagai metode pembayaran: **Tunai**, **Transfer Bank**, **QRIS (via Midtrans)**
- Pembayaran via **Hutang** (kredit pelanggan)
- Cetak struk transaksi (PDF & thermal-friendly)
- Riwayat transaksi lengkap dengan filter tanggal

### 📦 Manajemen Produk & Stok
- CRUD produk dengan kategori dan pemasok
- Pelacakan pergerakan stok (masuk/keluar)
- Manajemen pembelian produk (pengadaan) dengan estimasi kedatangan
- Produk sementara untuk keperluan reparasi
- Peringatan stok menipis

### 🔧 Layanan Reparasi / Servis
- Pencatatan order reparasi lengkap dengan status progres
- Manajemen sparepart yang digunakan dalam reparasi
- Upload foto kerusakan barang
- Notifikasi WhatsApp otomatis ke pelanggan saat status berubah
- Laporan analisis servis

### 💳 Manajemen Hutang & Piutang
- Pencatatan hutang pelanggan dari transaksi
- Pembayaran cicilan hutang
- Riwayat pembayaran hutang per pelanggan

### 📊 Business Intelligence
- **K-Means Clustering**: Klasifikasi produk ke kategori *High Moving*, *Medium Moving*, *Low Moving* berdasarkan kuantitas & pendapatan penjualan
- **Simple Moving Average (SMA)**: Rekomendasi jumlah restock produk berdasarkan tren penjualan historis
- Dashboard analitik dengan grafik interaktif
- Laporan pendapatan per periode

### 🌐 Katalog Produk Publik
- Halaman katalog per toko yang dapat diakses publik tanpa login
- Tampil produk **Top Seller** berdasarkan hasil K-Means Clustering
- Update data real-time sesuai stok dan transaksi terkini
- URL cantik per toko: `/store/{slug}`

### 💬 Integrasi WhatsApp
- Notifikasi otomatis via **Fonnte API** untuk update status reparasi
- Konfirmasi pembayaran ke pelanggan

### 🖨️ Laporan & Ekspor
- Ekspor laporan ke **PDF** (DomPDF) dan **Excel** (Maatwebsite Excel)
- Laporan penjualan, pembelian, hutang, dan servis
- Dashboard PDF untuk pemilik

---

## 🖼️ Preview Tampilan

> **Catatan**: BisnisKu mendukung **Light Mode** dan **Dark Mode** secara penuh.

### Dashboard Owner
Ringkasan total penjualan, tren pendapatan, produk terlaris, dan analisis pergerakan stok — semuanya dalam satu tampilan.

```
┌─────────────────────────────────────────────────────────────────┐
│  🏪 BisnisKu                              [🌙 Dark] [👤 Owner] │
├──────────────┬──────────────────────────────────────────────────┤
│  MENU        │  📊 Dashboard Owner                              │
│  ─────────── │                                                  │
│  Dashboard   │  ┌──────────┐ ┌──────────┐ ┌──────────┐         │
│  Kasir       │  │ 💰 Total │ │📦 Trx    │ │🔧 Servis │         │
│  Produk      │  │ Revenue  │ │ Hari Ini │ │ Aktif    │         │
│  Stok        │  │Rp 5.2jt  │ │  48 Trx  │ │  12 unit │         │
│  Reparasi    │  └──────────┘ └──────────┘ └──────────┘         │
│  Hutang      │                                                  │
│  Laporan     │  📈 Tren Penjualan 30 Hari                       │
│  BI Analysis │  ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓ (Chart)                    │
│  Pengaturan  │                                                  │
│              │  🏆 Produk Terlaris    📉 K-Means Cluster        │
└──────────────┴──────────────────────────────────────────────────┘
```

### Tampilan Kasir (POS)
Antarmuka kasir yang sederhana dan cepat — pilih produk, proses pembayaran, cetak struk.

```
┌─────────────────────────────────────────────────────────────────┐
│  🛒 Kasir                                                        │
├──────────────────────────┬──────────────────────────────────────┤
│  🔍 Cari Produk...        │  📋 Keranjang Belanja                │
│                          │  ─────────────────────────────────── │
│  ┌────────┐ ┌────────┐   │  Aqua 600ml    x2    Rp  6.000       │
│  │ Aqua   │ │Indomie │   │  Indomie Goreng x1   Rp  3.500       │
│  │ 600ml  │ │ Goreng │   │  ─────────────────────────────────── │
│  │Rp 3.000│ │Rp 3.500│   │  Subtotal             Rp  9.500      │
│  └────────┘ └────────┘   │                                      │
│                          │  💳 Metode Bayar:                    │
│  ┌────────┐ ┌────────┐   │  [Tunai] [Transfer] [QRIS] [Hutang]  │
│  │ Teh    │ │ Roti   │   │                                      │
│  │ Botol  │ │ Tawar  │   │  ┌──────────────────────────────┐    │
│  │Rp 4.000│ │Rp 8.000│   │  │    ✅ PROSES PEMBAYARAN      │    │
│  └────────┘ └────────┘   │  └──────────────────────────────┘    │
└──────────────────────────┴──────────────────────────────────────┘
```

### Business Intelligence — K-Means Clustering
Visualisasi pengelompokan produk berdasarkan performa penjualan.

```
┌─────────────────────────────────────────────────────────────────┐
│  📊 Analisis Pergerakan Produk (K-Means)                        │
├─────────────────────────────────────────────────────────────────┤
│  Periode: [01 Nov 2026] s/d [30 Nov 2026]  [🔄 Analisis]       │
│                                                                  │
│  🟢 High Moving (Sangat Laris)   — 12 Produk                   │
│  ┌─────────────────────────────────────────────────────┐        │
│  │ Aqua 600ml      │ Qty: 450  │ Revenue: Rp 1.350.000 │        │
│  │ Indomie Goreng  │ Qty: 380  │ Revenue: Rp 1.330.000 │        │
│  └─────────────────────────────────────────────────────┘        │
│                                                                  │
│  🟡 Medium Moving (Cukup Laris) — 25 Produk                    │
│  🔴 Low Moving (Kurang Laris)   — 18 Produk                    │
│                                                                  │
│  📦 Rekomendasi Restock (SMA 30 hari):                         │
│  Aqua 600ml: Rata-rata 15 unit/hari → Restock: 450 unit/bln   │
└─────────────────────────────────────────────────────────────────┘
```

### Katalog Publik
Halaman yang bisa dibagikan ke pelanggan untuk melihat produk tersedia.

```
┌─────────────────────────────────────────────────────────────────┐
│  🏪 Toko Sejahtera — Katalog Produk              [ID] [EN]     │
├─────────────────────────────────────────────────────────────────┤
│  🏆 Produk Top Seller                                           │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐                      │
│  │  🏅 #1   │  │  🥈 #2   │  │  🥉 #3   │                      │
│  │  Aqua    │  │  Indomie │  │  Teh Btl │                      │
│  │ Rp 3.000 │  │ Rp 3.500 │  │ Rp 4.000 │                      │
│  │ ✅ Stok  │  │ ✅ Stok  │  │ ✅ Stok  │                      │
│  └──────────┘  └──────────┘  └──────────┘                      │
│                                                                  │
│  📂 Semua Produk   🔍 [Cari produk...]                         │
│  [Semua] [Minuman] [Makanan] [Snack] [Rumah Tangga]            │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🏗️ Arsitektur Sistem

```
┌─────────────────────────────────────────────────────┐
│                   PENGGUNA                          │
│  Owner │ Kasir │ Gudang │ Teknisi │ Pelanggan Publik│
└───────────┬────────────────────────────┬────────────┘
            │                            │
            ▼                            ▼
┌───────────────────────┐    ┌───────────────────────┐
│   Internal POS App    │    │   Katalog Publik       │
│   (Auth Required)     │    │   /store/{slug}        │
└───────────┬───────────┘    └───────────┬───────────┘
            │                            │
            └──────────┬─────────────────┘
                       ▼
         ┌─────────────────────────┐
         │   Laravel Application   │
         │   (Controllers, Models) │
         └─────────────┬───────────┘
                       │
         ┌─────────────┼──────────────┐
         ▼             ▼              ▼
  ┌──────────┐  ┌──────────┐  ┌──────────────┐
  │ MySQL DB │  │ Services │  │ External APIs│
  │          │  │          │  │              │
  │ Products │  │ KMeans   │  │ Midtrans     │
  │ Trx Data │  │ SMA      │  │ Fonnte (WA)  │
  │ Debts    │  │ WhatsApp │  │ DANA QRIS    │
  └──────────┘  │ QRIS     │  └──────────────┘
                └──────────┘
```

---

## 🛠️ Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| **Backend Framework** | Laravel 13.x (PHP 8.3+) |
| **Frontend Styling** | Tailwind CSS v4 |
| **Build Tool** | Vite 8 |
| **Database** | MySQL |
| **Authentication** | Laravel Session Auth + Sanctum |
| **PDF Generation** | barryvdh/laravel-dompdf |
| **Excel Export** | Maatwebsite Excel |
| **Payment Gateway** | Midtrans (Snap & QRIS) |
| **WhatsApp API** | Fonnte |
| **Clustering Algorithm** | K-Means (implementasi PHP kustom) |
| **Forecasting** | Simple Moving Average (SMA) |
| **Queue** | Laravel Database Queue |

---

## 📋 Persyaratan

Pastikan sistem Anda memiliki:

- **PHP** `>= 8.3` dengan ekstensi: `BCMath`, `Ctype`, `cURL`, `DOM`, `Fileinfo`, `JSON`, `Mbstring`, `OpenSSL`, `PDO`, `Tokenizer`, `XML`
- **Composer** `>= 2.x`
- **Node.js** `>= 20.x` & **npm** `>= 10.x`
- **MySQL** `>= 8.0` (atau MariaDB `>= 10.6`)

---

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/username/bisnisku.git
cd bisnisku
```

### 2. Instalasi Otomatis (Direkomendasikan)

```bash
composer run setup
```

Script ini akan otomatis menjalankan:
- `composer install`
- Menyalin `.env.example` → `.env`
- Generate application key
- Menjalankan migrasi database
- `npm install` & `npm run build`

### 3. Instalasi Manual (Langkah per Langkah)

```bash
# Install dependensi PHP
composer install

# Salin file environment
cp .env.example .env

# Generate app key
php artisan key:generate

# Jalankan migrasi database
php artisan migrate

# (Opsional) Jalankan seeder data contoh
php artisan db:seed

# Install dependensi Node.js
npm install

# Build aset frontend
npm run build
```

### 4. Jalankan Aplikasi

```bash
# Mode Development (menjalankan server, queue, dan Vite sekaligus)
composer run dev
```

Atau secara terpisah:

```bash
php artisan serve          # Web server
php artisan queue:listen   # Queue worker
npm run dev                # Vite HMR
```

Akses aplikasi di: **http://localhost:8000**

---

## ⚙️ Konfigurasi

Salin `.env.example` ke `.env` lalu sesuaikan variabel berikut:

### Database

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bisnisku
DB_USERNAME=root
DB_PASSWORD=your_password
```

### WhatsApp (Fonnte)

```env
FONNTE_TOKEN=your_fonnte_api_token
```

> Dapatkan token di [fonnte.com](https://fonnte.com)

### Payment Gateway — Midtrans

```env
MIDTRANS_MERCHANT_ID=your_merchant_id
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_IS_PRODUCTION=false   # Ganti true untuk live
```

> Daftar di [midtrans.com](https://midtrans.com) dan aktifkan metode QRIS & Transfer Bank.

### QRIS via DANA

```env
DANA_MERCHANT_ID=your_dana_merchant_id
DANA_CLIENT_SECRET=your_dana_client_secret
```

### Storage & Link

```bash
# Buat symbolic link untuk storage publik
php artisan storage:link
```

---

## 👥 Peran & Hak Akses

BisnisKu memiliki sistem multi-role dengan akses yang disesuaikan per peran:

| Peran | Dashboard | POS | Produk | Stok | Reparasi | Hutang | Laporan | BI | Karyawan |
|-------|-----------|-----|--------|------|----------|--------|---------|-----|----------|
| **Owner** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Kasir** | ✅ | ✅ | 👁️ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ |
| **Gudang** | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ | 👁️ | ❌ | ❌ |
| **Teknisi** | ✅ | ❌ | 👁️ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |

> **Legend**: ✅ Akses penuh &nbsp;|&nbsp; 👁️ Hanya lihat &nbsp;|&nbsp; ❌ Tidak ada akses

---

## 📈 Business Intelligence

BisnisKu mengintegrasikan dua metode analitik untuk membantu pemilik bisnis:

### K-Means Clustering (Klasifikasi Produk)

Algoritma ini menganalisis setiap produk berdasarkan dua dimensi utama:
- **Total Kuantitas Terjual** dalam periode tertentu
- **Total Pendapatan (Revenue)** dari produk tersebut

Produk kemudian dikelompokkan ke 3 klaster:

| Klaster | Label | Keterangan |
|---------|-------|------------|
| 🟢 High | **Sangat Laris** | Produk dengan penjualan & revenue tertinggi |
| 🟡 Medium | **Cukup Laris** | Produk dengan performa penjualan menengah |
| 🔴 Low | **Kurang Laris** | Produk dengan penjualan rendah / stok mati |

Hasil clustering digunakan untuk menentukan **Top Seller** di katalog publik.

### Simple Moving Average — Rekomendasi Restock

Berdasarkan data penjualan historis (default 30 hari), sistem menghitung rata-rata penjualan harian per produk dan merekomendasikan jumlah restock yang optimal.

```
Rekomendasi Restock = Rata-rata Penjualan Harian × Periode Restock (hari)
```

---

## 🔌 Integrasi Pihak Ketiga

| Layanan | Fungsi | Konfigurasi |
|---------|--------|-------------|
| **Midtrans** | Pembayaran QRIS & Transfer Bank | `MIDTRANS_*` di `.env` |
| **Fonnte** | Notifikasi WhatsApp otomatis | `FONNTE_TOKEN` di `.env` |
| **DANA QRIS** | Pembayaran QRIS via DANA | `DANA_*` di `.env` |

### Webhook Pembayaran

Daftarkan URL webhook berikut di dashboard Midtrans:

```
POST https://yourdomain.com/api/midtrans/webhook
```

---

## 📁 Struktur Direktori

```
bisnisku/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Controller per fitur
│   │   └── Middleware/         # Auth, Locale, Tenant
│   ├── Models/                 # Eloquent Models
│   └── Services/
│       ├── KMeansService.php          # Algoritma K-Means
│       ├── SmaForecastService.php     # Algoritma SMA
│       ├── MidtransSnapService.php    # Pembayaran Midtrans
│       ├── MidtransQrisService.php    # QRIS Midtrans
│       ├── WhatsAppService.php        # Notifikasi WhatsApp
│       └── FonnteService.php          # Fonnte API Client
├── database/
│   ├── migrations/             # Skema database
│   └── seeders/                # Data awal
├── resources/
│   └── views/                  # Blade templates
│       ├── cashier/            # Halaman kasir
│       ├── dashboard/          # Dashboard per role
│       ├── catalog/            # Katalog publik
│       ├── service-repairs/    # Halaman reparasi
│       ├── debts/              # Halaman hutang
│       └── reports/            # Laporan & BI
├── docs/                       # Dokumentasi UML (PlantUML)
└── public/                     # Aset publik
```

---

## 🌐 Multi-Tenant & Multi-Store

BisnisKu mendukung **multi-store** di mana setiap pengguna dapat memiliki toko mereka sendiri dengan URL katalog yang unik:

- **Internal POS**: Diakses setelah login, data otomatis difilter per toko
- **Katalog Publik**: `https://yourdomain.com/store/{nama-toko}` — dapat dibagikan ke pelanggan

---

## 🤝 Berkontribusi

Kontribusi sangat disambut! Silakan buka _issue_ atau kirim _pull request_.

1. Fork repository ini
2. Buat branch fitur: `git checkout -b feature/fitur-baru`
3. Commit perubahan: `git commit -m 'feat: tambah fitur baru'`
4. Push ke branch: `git push origin feature/fitur-baru`
5. Buka Pull Request

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

---

<div align="center">

**BisnisKu** — Dibuat dengan ❤️ menggunakan Laravel & Tailwind CSS

</div>
