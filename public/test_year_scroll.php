<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $setting = new App\Models\Setting([
        'kos_name' => 'Kos Mas Robi',
        'owner_name' => 'Mas Robi',
        'owner_phone' => '6281234567890',
        'kos_address' => 'Jl. Kaliurang KM 5',
        'gmaps_url' => 'https://maps.app.goo.gl/xxx',
        'gmaps_embed' => '',
        'landing_content' => [
            'hero_title' => 'Test',
            'hero_desc' => 'Desc',
            'hero_btn_text' => 'Pesan',
            'hero_image' => 'assets/images/rooms/room_1.jpg',
            'bento_gallery' => [
                'assets/images/rooms/room_1.jpg',
                'assets/images/rooms/room_2.jpg',
                'assets/images/rooms/room_3.jpg',
                'assets/images/rooms/room_1.jpg',
            ],
            'bento_tag' => 'Tag',
            'bento_title' => 'Title',
            'bento_desc' => 'Desc'
        ],
        'location_highlights' => [],
        'payment_methods' => []
    ]);
    
    $rooms = collect([
        new App\Models\Room([
            'id' => 1,
            'number' => '101',
            'floor' => 1,
            'type' => 'Deluxe AC',
            'price' => 1500000,
            'status' => 'available',
            'image' => 'assets/images/rooms/room_1.jpg',
            'images' => ['assets/images/rooms/room_1.jpg', 'assets/images/rooms/room_2.jpg'],
            'facilities' => ['AC', 'KM Dalam']
        ])
    ]);

    $html = view('dashboard', [
        'deviceType' => 'desktop',
        'setting' => $setting,
        'rooms' => $rooms,
        'totalRooms' => 1,
        'occupiedRooms' => 0,
        'availableRooms' => 1,
        'dueRooms' => 0,
        'occupancyRate' => 0,
        'monthlyIncome' => 10000000,
        'totalExpense' => 2000000,
        'netCashflow' => 8000000,
        'dueAmount' => 0,
        'availableYears' => range(2026, 2045),
        'selectedYear' => 2026,
        'yearlyIncome' => 10000000,
        'yearlyExpense' => 2000000,
        'yearlyNet' => 8000000,
        'months' => ['Jan','Feb'],
        'chartIncome' => [1000, 2000],
        'chartExpense' => [500, 600],
        'chartNet' => [500, 1400],
        'billingList' => collect([]),
        'activeTickets' => 0,
        'roomTransfers' => collect([]),
        'pendingTransfersCount' => 0
    ])->render();

    echo "RENDER_SUCCESS: " . strlen($html) . " bytes\n";
} catch (\Throwable $e) {
    echo "RENDER_ERROR: " . $e->getMessage() . " on line " . $e->getLine() . " in " . $e->getFile() . "\n";
}

