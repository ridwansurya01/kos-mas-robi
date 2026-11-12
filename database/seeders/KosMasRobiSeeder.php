<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Finance;
use App\Models\MaintenanceTicket;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KosMasRobiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 0. Users (Owner & Tenants)
        User::updateOrCreate(
            ['email' => 'ownerkos@gmail.com'],
            [
                'name' => 'Mas Robi',
                'password' => Hash::make('owner123'),
                'role' => 'owner',
                'phone' => '6281234567890',
                'avatar' => null,
            ]
        );

        // Akun Admin Operasional (Akses Semua Fitur kecuali Laporan Keuangan)
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Operasional',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => null,
                'avatar' => null,
            ]
        );
        // 1. Settings
        Setting::updateOrCreate(
            ['id' => 1],
            [
                'kos_name' => 'Kos Mas Robi - Kampus Terpadu',
                'owner_name' => 'Mas Robi',
                'owner_phone' => '6281234567890',
                'kos_address' => 'Jl. Kaliurang KM 5, Gang Pandega Marta No. 18, Sleman, DI Yogyakarta',
                'bank_name' => 'Bank Central Asia (BCA)',
                'bank_account' => '8830192831',
                'bank_holder' => 'Robi Suryanto',
            ]
        );

        // 2. Unit Kamar & Penghuni: Bersih / Kosong secara default
        // Pengelola menambahkan unit kamar dan penghuni nyata secara dinamis melalui dashboard.
    }
}
