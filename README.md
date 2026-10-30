# 🏠 Kos Mas Robi - Smart Kost Management System (Laravel 11 Edition)

Aplikasi web manajemen kos-kosan berbasis **Laravel 11** dengan arsitektur MVC, database SQLite, dan tampilan **100% responsif otomatis (*fluid auto-adaptive*)** yang secara cerdas mendeteksi perangkat pengguna (Desktop, Tablet, Mobile) tanpa perlu tombol pengalih manual.

---

## 🚀 Keunggulan Versi Laravel

1. **Deteksi Perangkat Otomatis & Fleksibel (*Zero Manual Buttons*)**:
   - Bar tombol simulasi manual telah **dihapus**.
   - Tampilan kini **100% otomatis**:
     - Dibuka dari **Smartphone (HP)**: Otomatis menampilkan **Bottom App Bar** di bawah layar, layout kartu ramah sentuhan, dan menu drawer.
     - Dibuka dari **Desktop / Laptop**: Otomatis menampilkan **Sidebar navigasi tetap** di kiri dan tabel data luas multi-kolom.
     - Dibuka dari **Tablet**: Menyesuaikan grid 2-kolom seimbang secara otomatis.
   - Deteksi perangkat dilakukan ganda:
     - **Backend (Laravel)**: Mendeteksi User-Agent via `DashboardController` (`$deviceType`).
     - **Frontend**: Fluid CSS `@media` queries yang beradaptasi seketika saat orientasi layar atau ukuran browser berubah.

2. **Sistem Akun Multi-Role & UI Pelanggan**:
   - **Owner / Pengelola (Mas Robi)**: Mengelola master kamar, anak kos, buku kas keuangan, laporan, dan pengaturan kos (`/admin`).
   - **Pelanggan / Anak Kos (Tenant Portal)**: UI khusus mobile pelanggan (`/portal`) yang terinspirasi dari referensi desain mobile iOS.
   - **Akses Cepat 1-Klik**: Disediakan tombol demo login instan di halaman login (`/login`) untuk berpindah peran tanpa repot.

3. **Database SQLite Terstruktur (Eloquent ORM)**:
   - `users`: Akun login (owner & anak kos dengan relasi kamar).
   - `rooms`: Data master kamar (nomor, lantai, tipe, tarif sewa, fasilitas JSON, status).
   - `tenants`: Data anak kos (nama, no. WA, NIK, tanggal masuk, jatuh tempo bulanan, kontak darurat).
   - `finances`: Buku kas pencatatan transaksi uang masuk (sewa, token) dan beban operasional kos.
   - `maintenance_tickets`: Tiket laporan keluhan fasilitas (kerusakan keran, servis AC, kendala WiFi).
   - `settings`: Pengaturan properti kos, pemilik, kontak pengelola, dan rekening bank transfer.

---

## 🛠️ Struktur Source Code Laravel

```text
Kos Mas Robi/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php          # Login, Register, Logout & 1-Click Demo Switcher
│   │   ├── DashboardController.php     # Controller Admin / Owner & API CRUD
│   │   └── TenantPortalController.php  # Controller Portal UI Pelanggan (Anak Kos)
│   └── Models/
│       ├── User.php                    # Model akun pengguna & role
│       ├── Room.php                    # Model kamar
│       ├── Tenant.php                  # Model penghuni
│       ├── Finance.php                 # Model transaksi & kas
│       ├── MaintenanceTicket.php       # Model tiket servis
│       └── Setting.php                 # Model profil kos & rekening
├── database/
│   ├── database.sqlite                 # Database SQLite aktif
│   ├── migrations/                     # Skema tabel database (users, rooms, tenants, finances, tickets)
│   └── seeders/
│       └── KosMasRobiSeeder.php        # Data awal (akun owner, akun anak kos, 16 kamar, kas)
├── public/
│   ├── css/style.css                   # Desain responsif admin + UI mobile pelanggan
│   ├── js/app.js                       # Integrasi API Laravel, WhatsApp, & modal
│   └── assets/images/                  # Gambar kamar, eksterior, avatar, dan 3D key badge
├── resources/views/
│   ├── layouts/app.blade.php           # Layout Blade utama (CSRF, responsive meta)
│   ├── auth/                           # Halaman Login & Register
│   │   ├── login.blade.php
│   │   └── register.blade.php
│   ├── tenant/                         # Portal UI Pelanggan (Desain sesuai referensi)
│   │   └── portal.blade.php
│   └── dashboard.blade.php             # Dashboard Owner / Pengelola
└── routes/
    └── web.php                         # Route web, auth, tenant portal & API CRUD
```

---

## 💻 Cara Menjalankan Aplikasi

1. **Jalankan Server Laravel**:
   Buka terminal di folder proyek ini (`d:\Kos Mas Robi`), lalu ketik:
   ```bash
   php artisan serve
   ```
   Aplikasi akan berjalan pada alamat: **`http://127.0.0.1:8000`**

2. **Akses dari Smartphone (HP) dalam 1 Jaringan WiFi**:
   Jalankan server dengan host `0.0.0.0`:
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```
   Buka browser di HP Anda dan akses `http://[IP-Laptop-Anda]:8000`. Tampilan akan langsung otomatis menyesuaikan ke mode mobile tanpa tombol apapun!

3. **Reset Database ke Data Sampel Awal**:
   ```bash
   php artisan migrate:fresh --seed
   ```
