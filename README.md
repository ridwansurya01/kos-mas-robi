# 🏠 Kos Mas Robi - Smart Kost Management System & Luxury Living

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Database](https://img.shields.io/badge/Database-SQLite%20%7C%20MySQL-003B57?style=for-the-badge&logo=sqlite&logoColor=white)](https://sqlite.org)
[![Chart.js](https://img.shields.io/badge/Analytics-Chart.js-FF6384?style=for-the-badge&logo=chartdotjs&logoColor=white)](https://www.chartjs.org)
[![UI Design](https://img.shields.io/badge/Design-Bento%20%26%20Glassmorphism-0a5661?style=for-the-badge)](/)

Aplikasi web manajemen hunian kost terintegrasi berbasis **Laravel 11** yang menggabungkan **Landing Page Pemasaran Eksklusif**, **Dashboard Manajemen Pengelola (Admin/Owner)** dengan visualisasi kurva keuangan interaktif, serta **Portal Mandiri Penghuni (Tenant Portal)** yang responsif otomatis di berbagai perangkat (Desktop, Tablet, dan Mobile).

---

## ✨ Fitur Unggulan Sistem

### 1. 🌟 Luxury Landing Page (`/`)
* **Desain Eksklusif (Oceanic Teal & Bento Architecture)**: Memadukan gradient laut dalam, tipografi modern, dan efek fade visual arsitektur malam hari yang menyatu tanpa batas (*seamless gradient blend*).
* **Floating Search Card (Double-Decker)**: Filter pencarian kamar real-time berdasarkan tipe kamar (VIP, Deluxe, Standard), pemilihan lantai (Lantai 1 & 2), pencarian fasilitas, dan status ketersediaan unit.
* **Koleksi Kamar Siap Huni**: Kartu kamar dengan galeri foto, spesifikasi fasilitas (AC, Springbed, KM Dalam, WiFi), harga sewa transparan, dan tombol pemesanan instan langsung terhubung ke WhatsApp pengelola.
* **Bento Grid Fasilitas Kost**: Showcase keunggulan hunian (Internet Fiber 100 Mbps, Smart Access & CCTV 24 Jam, Dapur & Parkir Bersama).
* **Peta Interaktif & 4 Highlight Akses Lokasi**: Integrasi Google Maps responsif dan 4 poin akses strategis (kampus, pusat kuliner, rumah sakit, keamanan) yang dapat dikustomisasi pengelola kapan saja.

### 2. 📊 Admin & Owner Dashboard (`/admin`)
* **Header Modern Glassmorphism**: Dilengkapi breadcrumb dinamis, input pencarian cepat dengan badge shortcut keyboard `<kbd>Ctrl K</kbd>`, serta slider Day/Night Mode (*Dark Mode*).
* **KPI Metric Cards**: Ringkasan persentase tingkat okupansi, unit siap huni, estimasi pendapatan bulanan, dan jumlah tagihan yang perlu konfirmasi (terisolasi rapi pada tab Ikhtisar).
* **Laporan Keuangan & Analisis Kurva Interaktif (Chart.js)**:
  * Kurva pendapatan bulanan s/d 1 tahun penuh (*Revenue Projection Curve*).
  * Buku kas arus kas masuk (sewa, token) vs pengeluaran operasional (listrik, WiFi, kebersihan).
  * Analisis laba bersih (*Net Profit*) dan rekap riwayat transaksi terverifikasi.
* **Manajemen Operasional Kos Lengkap**:
  * **Master Kamar**: Tambah/edit unit, set tarif sewa, fasilitas, lantai, dan status huni.
  * **Data Penghuni**: Kelola profil anak kos, nomor WhatsApp, NIK, tanggal masuk, dan kontak darurat.
  * **Tagihan & Pembayaran**: Pantau tagihan jatuh tempo, konfirmasi bukti pembayaran, dan cetak invoice.
  * **Pindah Kamar & Tiket Keluhan**: Riwayat mutasi unit serta pelacakan servis/kerusakan fasilitas kos.
  * **Pengaturan Properti**: Konfigurasi profil kos, rekening bank pengelola, serta koordinat/embed Google Maps.

### 3. 📱 Tenant Portal / UI Mobile Pelanggan (`/portal`)
* **Mobile-First Experience (iOS Inspired)**: Tampilan khusus yang ramah sentuhan, praktis, dan intuitif bagi anak kos.
* **Informasi Kamar & Status Sewa**: Detail nomor kamar, tanggal jatuh tempo pembayaran sewa, dan riwayat tagihan.
* **Bayar Sewa Digital**: Instruksi transfer bank (BCA, Mandiri, dll.), QRIS instan, dan formulir upload foto bukti pembayaran.
* **Pelaporan Masalah & Servis Mandiri**: Formulir pengajuan keluhan fasilitas (AC kurang dingin, WiFi lambat, keran bocor) lengkap dengan pelacakan status penanganan teknisi.
* **Akses Bantuan 1-Klik**: Tombol darurat dan hotline WhatsApp langsung ke pengelola kos.

### 4. 🔐 Multi-Role Authentication & Demo Switcher
* Sistem autentikasi berbasis role aman (`owner` & `tenant`) dengan proteksi CSRF dan bcrypt hashing.
* Tombol **1-Klik Login Demo** di halaman login (`/login`) untuk kemudahan eksplorasi tanpa perlu mengetik kredensial.

---

## 🛠️ Tech Stack & Arsitektur

| Komponen | Teknologi yang Digunakan |
|---|---|
| **Framework Backend** | [Laravel 11.x](https://laravel.com) (PHP 8.2+) |
| **Arsitektur** | Model-View-Controller (MVC) + Eloquent ORM |
| **Database** | SQLite (Default, portable, zero-configuration) / Support MySQL & PostgreSQL |
| **Frontend & UI** | Blade Templating, Vanilla JavaScript ES6+, Modern CSS3 Custom Properties |
| **Data Visualization** | [Chart.js 4.x](https://www.chartjs.org) (Responsive curves, dynamic tooltip, monetary formatting) |
| **Tipografi** | [Outfit](https://fonts.google.com/specimen/Outfit) & [Plus Jakarta Sans](https://fonts.google.com/specimen/Plus+Jakarta+Sans) |
| **Ikonografi** | Lucide Icons & Feather SVG Vectors |

---

## 🚀 Panduan Instalasi & Menjalankan Proyek

### Prasyarat
* PHP >= 8.2 dengan ekstensi `pdo_sqlite`, `curl`, `mbstring`, `fileinfo`.
* [Composer](https://getcomposer.org/) terinstal di sistem.

### Langkah-langkah

1. **Clone Repository**:
   ```bash
   git clone https://github.com/ridwansurya01/kos-mas-robi.git
   cd kos-mas-robi
   ```

2. **Instal Dependensi Composer**:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment**:
   Salin file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi Database & Seeding Data Awal**:
   ```bash
   touch database/database.sqlite
   php artisan migrate --seed
   ```

5. **Jalankan Development Server**:
   ```bash
   php artisan serve
   ```
   Akses aplikasi di browser pada: **`http://127.0.0.1:8000`**

6. *(Opsional)* **Akses dari HP dalam Jaringan WiFi yang Sama**:
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```
   Buka browser di HP dan ketik: `http://[IP-Lokal-Komputer-Anda]:8000`

---

## 🔑 Akun Demo Bawaan (Default Credentials)

| Peran (Role) | Email | Password | Hak Akses |
|---|---|---|---|
| **Owner / Pengelola** | `owner@kosmasrobi.com` | `password123` | Akses penuh dashboard `/admin`, keuangan, kamar, dan pengaturan |
| **Penghuni (Anak Kos)** | `rian@kosmasrobi.com` | `password123` | Akses `/portal`, info kamar 101, bayar sewa, lapor keluhan |

> 💡 **Tip:** Anda juga dapat menggunakan tombol **Demo Login 1-Klik** langsung di halaman login tanpa perlu mengetik manual.

---

## 📂 Struktur Direktori Utama

```text
Kos Mas Robi/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php          # Otentikasi login, logout, & switcher demo
│   │   ├── DashboardController.php     # Controller Admin / Owner & analitika kurva kas
│   │   └── TenantPortalController.php  # Controller Portal UI Mandiri Anak Kos
│   └── Models/
│       ├── User.php                    # Model akun pengguna & role
│       ├── Room.php                    # Master kamar kos & fasilitas
│       ├── Tenant.php                  # Data anak kos & relasi kamar
│       ├── Finance.php                 # Buku kas keuangan & transaksi
│       ├── MaintenanceTicket.php       # Tiket keluhan & perbaikan fasilitas
│       └── Setting.php                 # Profil kos, nomor WA, rekening & Google Maps
├── database/
│   ├── database.sqlite                 # Database SQLite aktif
│   ├── migrations/                     # Skema tabel database
│   └── seeders/
│       └── KosMasRobiSeeder.php        # Seeder kamar, pengguna, dan data awal
├── public/
│   ├── css/
│   │   ├── landing.css                 # Gaya visual Landing Page (Oceanic & Bento)
│   │   ├── style.css                   # Gaya visual Dashboard Admin + Dark mode
│   │   └── portal.css                  # Gaya visual Tenant Portal mobile
│   ├── js/
│   │   └── app.js                      # Interaksi UI admin, Chart.js, & modal handler
│   └── assets/images/                  # Aset foto arsitektur eksterior & interior kamar
├── resources/views/
│   ├── landing.blade.php               # Halaman depan / Landing Page utama
│   ├── dashboard.blade.php             # Dashboard Admin / Pengelola kos
│   ├── auth/
│   │   ├── login.blade.php             # Form login & demo switcher
│   │   └── register.blade.php          # Form pendaftaran akun baru
│   └── tenant/
│       └── portal.blade.php            # Portal mandiri penghuni kos
└── routes/
    └── web.php                         # Route web, auth, API CRUD & tenant portal
```

---

## 📄 Lisensi
Proyek ini dikembangkan di bawah lisensi open-source [MIT License](LICENSE).

---
© {{ date('Y') }} **Kos Mas Robi**. Dikembangkan dengan dedikasi untuk kenyamanan hunian dan efisiensi manajemen properti modern.
