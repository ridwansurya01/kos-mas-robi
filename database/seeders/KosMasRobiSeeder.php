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
            ['email' => 'owner@kosmasrobi.com'],
            [
                'name' => 'Mas Robi',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'phone' => '6281234567890',
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

        // 2. Rooms Data
        $roomsData = [
            // Lantai 1
            ['number' => '101', 'floor' => 1, 'type' => 'VIP AC (KM Dalam)', 'price' => 1650000, 'status' => 'occupied', 'facilities' => ['AC', 'KM Dalam', 'Springbed', 'Meja', 'Lemari', 'Water Heater'], 'tenant' => 'Rian Pratama', 'phone' => '6281234567891', 'due_day' => 5],
            ['number' => '102', 'floor' => 1, 'type' => 'Deluxe AC', 'price' => 1500000, 'status' => 'occupied', 'facilities' => ['AC', 'KM Dalam', 'Springbed', 'Meja', 'Lemari'], 'tenant' => 'Dimas Satria', 'phone' => '6281398765432', 'due_day' => 1],
            ['number' => '103', 'floor' => 1, 'type' => 'Deluxe AC', 'price' => 1500000, 'status' => 'occupied', 'facilities' => ['AC', 'KM Dalam', 'Springbed', 'Meja', 'Lemari'], 'tenant' => 'Fajar Nugroho', 'phone' => '6285712345678', 'due_day' => 10],
            ['number' => '104', 'floor' => 1, 'type' => 'Standard Fan (KM Luar)', 'price' => 950000, 'status' => 'due', 'facilities' => ['Kipas Angin', 'Springbed', 'Lemari'], 'tenant' => 'Bayu Wicaksono', 'phone' => '6287812345679', 'due_day' => 28],
            ['number' => '105', 'floor' => 1, 'type' => 'Deluxe AC', 'price' => 1500000, 'status' => 'available', 'facilities' => ['AC', 'KM Dalam', 'Springbed', 'Meja', 'Lemari'], 'tenant' => null, 'phone' => null, 'due_day' => 1],
            ['number' => '106', 'floor' => 1, 'type' => 'Deluxe AC', 'price' => 1500000, 'status' => 'occupied', 'facilities' => ['AC', 'KM Dalam', 'Springbed', 'Meja'], 'tenant' => 'Aldo Pratama', 'phone' => '6282112345670', 'due_day' => 3],
            ['number' => '107', 'floor' => 1, 'type' => 'VIP AC (KM Dalam)', 'price' => 1650000, 'status' => 'occupied', 'facilities' => ['AC', 'KM Dalam', 'Springbed', 'Meja', 'Water Heater'], 'tenant' => 'Gilang Ramadhan', 'phone' => '6281987654321', 'due_day' => 15],
            ['number' => '108', 'floor' => 1, 'type' => 'Deluxe AC', 'price' => 1500000, 'status' => 'repair', 'facilities' => ['AC', 'KM Dalam', 'Springbed'], 'tenant' => null, 'phone' => null, 'due_day' => 1],

            // Lantai 2
            ['number' => '201', 'floor' => 2, 'type' => 'VIP AC (KM Dalam)', 'price' => 1700000, 'status' => 'due', 'facilities' => ['AC', 'KM Dalam', 'Balkon', 'Springbed', 'Meja Belajar'], 'tenant' => 'Bagus Triadi', 'phone' => '6281356789012', 'due_day' => 1],
            ['number' => '202', 'floor' => 2, 'type' => 'Deluxe AC', 'price' => 1550000, 'status' => 'occupied', 'facilities' => ['AC', 'KM Dalam', 'Springbed', 'Lemari'], 'tenant' => 'Kevin Sanjaya', 'phone' => '6281298761234', 'due_day' => 7],
            ['number' => '203', 'floor' => 2, 'type' => 'Deluxe AC', 'price' => 1550000, 'status' => 'occupied', 'facilities' => ['AC', 'KM Dalam', 'Springbed', 'Meja'], 'tenant' => 'Yoga Prasetya', 'phone' => '6285812348765', 'due_day' => 12],
            ['number' => '204', 'floor' => 2, 'type' => 'Deluxe AC', 'price' => 1550000, 'status' => 'due', 'facilities' => ['AC', 'KM Dalam', 'Springbed', 'Meja'], 'tenant' => 'Sarah Maulida', 'phone' => '6287712344321', 'due_day' => 2],
            ['number' => '205', 'floor' => 2, 'type' => 'VIP AC (KM Dalam)', 'price' => 1700000, 'status' => 'available', 'facilities' => ['AC', 'KM Dalam', 'Balkon', 'Springbed'], 'tenant' => null, 'phone' => null, 'due_day' => 1],
            ['number' => '206', 'floor' => 2, 'type' => 'Deluxe AC', 'price' => 1550000, 'status' => 'occupied', 'facilities' => ['AC', 'KM Dalam', 'Springbed', 'Lemari'], 'tenant' => 'Aditya Saputra', 'phone' => '6281312347890', 'due_day' => 20],
            ['number' => '207', 'floor' => 2, 'type' => 'Deluxe AC', 'price' => 1550000, 'status' => 'occupied', 'facilities' => ['AC', 'KM Dalam', 'Springbed', 'Meja'], 'tenant' => 'Rizky Akbar', 'phone' => '6282212345678', 'due_day' => 18],
            ['number' => '208', 'floor' => 2, 'type' => 'VIP AC (KM Dalam)', 'price' => 1700000, 'status' => 'occupied', 'facilities' => ['AC', 'KM Dalam', 'Balkon', 'Springbed', 'Water Heater'], 'tenant' => 'Dionisius Rama', 'phone' => '6289612345678', 'due_day' => 25],
        ];

        foreach ($roomsData as $r) {
            $room = Room::updateOrCreate(
                ['number' => $r['number']],
                [
                    'floor' => $r['floor'],
                    'type' => $r['type'],
                    'price' => $r['price'],
                    'status' => $r['status'],
                    'facilities' => $r['facilities'],
                ]
            );

            if ($r['tenant']) {
                Tenant::updateOrCreate(
                    ['room_id' => $room->id],
                    [
                        'name' => $r['tenant'],
                        'phone' => $r['phone'],
                        'start_date' => '2026-01-01',
                        'due_day' => $r['due_day'],
                        'rent_amount' => $r['price'],
                        'emergency_contact' => 'Orang Tua (' . $r['tenant'] . ')',
                        'status' => 'active',
                    ]
                );

                // Create User login account for tenant
                $emailUsername = strtolower(explode(' ', trim($r['tenant']))[0]);
                User::updateOrCreate(
                    ['email' => $emailUsername . '@gmail.com'],
                    [
                        'name' => $r['tenant'],
                        'password' => Hash::make('password123'),
                        'role' => 'tenant',
                        'room_id' => $room->id,
                        'phone' => $r['phone'],
                        'avatar' => 'assets/images/tenant_avatar.jpg',
                    ]
                );
            }
        }

        // 3. Finances Seed
        $finances = [
            ['code' => 'TRX-101', 'date' => '2026-10-27', 'type' => 'income', 'category' => 'Sewa Kamar', 'amount' => 1500000, 'desc' => 'Pembayaran Sewa Kamar 102 (Dimas Satria) - Periode Nov 2026', 'method' => 'Transfer BCA', 'room' => '102'],
            ['code' => 'TRX-102', 'date' => '2026-10-25', 'type' => 'income', 'category' => 'Sewa Kamar', 'amount' => 1700000, 'desc' => 'Pembayaran Sewa Kamar 208 (Dionisius) - Periode Nov 2026', 'method' => 'Transfer BCA', 'room' => '208'],
            ['code' => 'TRX-103', 'date' => '2026-10-22', 'type' => 'expense', 'category' => 'Internet WiFi', 'amount' => 550000, 'desc' => 'Tagihan Indihome & Biznet Kos 100 Mbps', 'method' => 'Transfer QRIS', 'room' => null],
            ['code' => 'TRX-104', 'date' => '2026-10-20', 'type' => 'income', 'category' => 'Sewa Kamar', 'amount' => 1550000, 'desc' => 'Pembayaran Sewa Kamar 206 (Aditya) - Periode Nov 2026', 'method' => 'Transfer BCA', 'room' => '206'],
            ['code' => 'TRX-105', 'date' => '2026-10-18', 'type' => 'expense', 'category' => 'Listrik & Token', 'amount' => 750000, 'desc' => 'Pengisian Token Listrik Induk Fasilitas Bersama', 'method' => 'Transfer Mandiri', 'room' => null],
            ['code' => 'TRX-106', 'date' => '2026-10-15', 'type' => 'expense', 'category' => 'Kebersihan', 'amount' => 350000, 'desc' => 'Iuran Sampah RT & Kebersihan Halaman Luar', 'method' => 'Tunai (Cash)', 'room' => null],
            ['code' => 'TRX-107', 'date' => '2026-10-12', 'type' => 'income', 'category' => 'Sewa Kamar', 'amount' => 1650000, 'desc' => 'Pembayaran Sewa Kamar 107 (Gilang) - Periode Nov 2026', 'method' => 'Transfer BCA', 'room' => '107'],
        ];

        foreach ($finances as $f) {
            $roomId = null;
            if ($f['room']) {
                $foundRoom = Room::where('number', $f['room'])->first();
                $roomId = $foundRoom?->id;
            }

            Finance::updateOrCreate(
                ['transaction_code' => $f['code']],
                [
                    'room_id' => $roomId,
                    'date' => $f['date'],
                    'type' => $f['type'],
                    'category' => $f['category'],
                    'amount' => $f['amount'],
                    'description' => $f['desc'],
                    'payment_method' => $f['method'],
                ]
            );
        }

        // 4. Maintenance Tickets Seed
        $tickets = [
            ['code' => 'TK-108', 'room' => '108', 'reporter' => 'Pengelola', 'issue' => 'AC kurang dingin & perlu cuci AC berkala serta cat ulang dinding', 'date' => '2026-10-26', 'priority' => 'Mendesak', 'status' => 'progress'],
            ['code' => 'TK-202', 'room' => '202', 'reporter' => 'Kevin Sanjaya', 'issue' => 'Keran wastafel kamar mandi kendor dan sedikit menetes', 'date' => '2026-10-24', 'priority' => 'Normal', 'status' => 'done'],
            ['code' => 'TK-104', 'room' => '104', 'reporter' => 'Bayu Wicaksono', 'issue' => 'Stopkontak samping meja belajar sedikit goyang', 'date' => '2026-10-28', 'priority' => 'Normal', 'status' => 'pending'],
        ];

        foreach ($tickets as $t) {
            $r = Room::where('number', $t['room'])->first();
            if ($r) {
                MaintenanceTicket::updateOrCreate(
                    ['ticket_code' => $t['code']],
                    [
                        'room_id' => $r->id,
                        'reporter_name' => $t['reporter'],
                        'issue_description' => $t['issue'],
                        'report_date' => $t['date'],
                        'priority' => $t['priority'],
                        'status' => $t['status'],
                    ]
                );
            }
        }
    }
}
