<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Finance;
use App\Models\MaintenanceTicket;
use App\Models\Setting;
use App\Models\RoomTransfer;
use Illuminate\Support\Facades\Artisan;

class DashboardController extends Controller
{
    /**
     * Display the main responsive dashboard.
     * Automatically detects the client's device (Mobile, Tablet, Desktop)
     * and passes it to the view.
     */
    public function index(Request $request)
    {
        $deviceType = $this->detectDevice($request->userAgent());

        $setting = Setting::first() ?? new Setting([
            'kos_name' => 'Kos Mas Robi - Kampus Terpadu',
            'owner_name' => 'Mas Robi',
            'owner_phone' => '6281234567890',
            'kos_address' => 'Jl. Kaliurang KM 5, Gang Pandega Marta No. 18, Sleman, DI Yogyakarta',
            'bank_name' => 'Bank Central Asia (BCA)',
            'bank_account' => '8830192831',
            'bank_holder' => 'Robi Suryanto',
        ]);

        $rooms = Room::with('activeTenant')->orderBy('number')->get();
        $this->ensureTwoBranchSampleData($rooms);
        $rooms = Room::with('activeTenant')->orderBy('number')->get();
        $totalRooms = $rooms->count();
        $occupiedRooms = $rooms->whereIn('status', ['occupied', 'due'])->count();
        $availableRooms = $rooms->where('status', 'available')->count();
        $dueRooms = $rooms->where('status', 'due')->count();

        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        $monthlyIncome = Finance::where('type', 'income')->sum('amount');
        $totalExpense = Finance::where('type', 'expense')->sum('amount');
        $netCashflow = $monthlyIncome - $totalExpense;
        $dueAmount = $rooms->where('status', 'due')->sum('price');

        $branches = $setting->branches;
        $roomsLoc1 = $rooms->filter(fn($r) => ($r->location ?? 'Lokasi 1') === 'Lokasi 1');
        $roomsLoc2 = $rooms->filter(fn($r) => ($r->location ?? '') === 'Lokasi 2');

        $loc1Total = $roomsLoc1->count();
        $loc1Occupied = $roomsLoc1->whereIn('status', ['occupied', 'due'])->count();
        $loc1Available = $roomsLoc1->where('status', 'available')->count();
        $loc1Due = $roomsLoc1->where('status', 'due')->count();
        $loc1OccupancyRate = $loc1Total > 0 ? round(($loc1Occupied / $loc1Total) * 100, 1) : 0;

        $loc2Total = $roomsLoc2->count();
        $loc2Occupied = $roomsLoc2->whereIn('status', ['occupied', 'due'])->count();
        $loc2Available = $roomsLoc2->where('status', 'available')->count();
        $loc2Due = $roomsLoc2->where('status', 'due')->count();
        $loc2OccupancyRate = $loc2Total > 0 ? round(($loc2Occupied / $loc2Total) * 100, 1) : 0;

        // Analisis Keuangan Berdasarkan Tahun Otomatis
        $selectedYear = (int)$request->input('year', 2026);
        if ($selectedYear < 2020) {
            $selectedYear = 2026;
        }
        $maxYear = max(2045, $selectedYear + 5);
        $availableYears = range(2024, $maxYear);

        $allYearFinances = Finance::with('room')->whereYear('date', $selectedYear)->get();
        $hasDbRecords = $allYearFinances->count() > 0;

        $yearlyIncome = $hasDbRecords ? (int)$allYearFinances->where('type', 'income')->sum('amount') : 0;
        $yearlyExpense = $hasDbRecords ? (int)$allYearFinances->where('type', 'expense')->sum('amount') : 0;
        $yearlyNet = $yearlyIncome - $yearlyExpense;

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartIncome = [];
        $chartExpense = [];
        $chartNet = [];

        for ($m = 1; $m <= 12; $m++) {
            if (!$hasDbRecords) {
                $chartIncome[] = 0;
                $chartExpense[] = 0;
                $chartNet[] = 0;
                continue;
            }
            $inc = (int)$allYearFinances->where('type', 'income')
                ->filter(fn($f) => \Carbon\Carbon::parse($f->date)->month === $m)
                ->sum('amount');
            $exp = (int)$allYearFinances->where('type', 'expense')
                ->filter(fn($f) => \Carbon\Carbon::parse($f->date)->month === $m)
                ->sum('amount');

            $chartIncome[] = $inc;
            $chartExpense[] = $exp;
            $chartNet[] = $inc - $exp;
        }

        $activeTickets = MaintenanceTicket::where('status', '!=', 'done')->count();

        $roomTransfers = RoomTransfer::with(['user', 'tenant', 'currentRoom', 'targetRoom'])
            ->orderBy('created_at', 'desc')
            ->get();
        $pendingTransfersCount = $roomTransfers->where('status', 'pending')->count();

        $allFinances = Finance::with('room')->orderBy('date', 'desc')->get();
        $allMaintenance = MaintenanceTicket::with('room')->orderBy('report_date', 'desc')->get();

        // Data Penagihan Sewa (Tagihan Terpisah)
        $billingList = $rooms->filter(function($r) {
            return in_array($r->status, ['occupied', 'due']);
        })->values();

        return view('dashboard', compact(
            'allFinances',
            'allMaintenance',
            'deviceType',
            'setting',
            'rooms',
            'totalRooms',
            'occupiedRooms',
            'availableRooms',
            'dueRooms',
            'occupancyRate',
            'monthlyIncome',
            'totalExpense',
            'netCashflow',
            'dueAmount',
            'availableYears',
            'selectedYear',
            'yearlyIncome',
            'yearlyExpense',
            'yearlyNet',
            'months',
            'chartIncome',
            'chartExpense',
            'chartNet',
            'billingList',
            'activeTickets',
            'roomTransfers',
            'pendingTransfersCount',
            'branches',
            'roomsLoc1',
            'roomsLoc2',
            'loc1Total',
            'loc1Occupied',
            'loc1Available',
            'loc1Due',
            'loc1OccupancyRate',
            'loc2Total',
            'loc2Occupied',
            'loc2Available',
            'loc2Due',
            'loc2OccupancyRate'
        ));
    }

    /**
     * Helper to detect device from User-Agent string.
     */
    private function detectDevice(?string $userAgent): string
    {
        if (empty($userAgent)) {
            return 'desktop';
        }

        $ua = strtolower($userAgent);

        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $ua)) {
            return 'tablet';
        }

        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $ua)) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * Ensure both Kos locations (Lokasi 1 & Lokasi 2) have rich, realistic sample data
     * for Maintenance Tickets, Room Transfers, and Finances so neither Kos branch is empty.
     */
    private function ensureTwoBranchSampleData($rooms): void
    {
        try {
            $loc1Rooms = $rooms->filter(fn($r) => ($r->location ?? 'Lokasi 1') === 'Lokasi 1')->values();
            $loc2Rooms = $rooms->filter(fn($r) => ($r->location ?? '') === 'Lokasi 2')->values();
            $loc2RoomIds = $loc2Rooms->pluck('id')->all();

            // 1. Ensure Maintenance Tickets exist for Lokasi 2 (and Lokasi 1)
            if (!empty($loc2RoomIds)) {
                $loc2TicketsCount = MaintenanceTicket::whereIn('room_id', $loc2RoomIds)->count();
                if ($loc2TicketsCount === 0) {
                    $r202 = $loc2Rooms->firstWhere('number', '202') ?? $loc2Rooms->first();
                    $r205 = $loc2Rooms->firstWhere('number', '205') ?? ($loc2Rooms->get(1) ?? $r202);
                    $r207 = $loc2Rooms->firstWhere('number', '207') ?? ($loc2Rooms->get(2) ?? $r202);

                    if ($r202) {
                        MaintenanceTicket::create([
                            'ticket_code' => 'MNT-202',
                            'room_id' => $r202->id,
                            'reporter_name' => $r202->activeTenant->name ?? 'Nadia Putri (Cabang 2)',
                            'issue_description' => '[Cabang 2 Ringroad] Indikator baterai Smart Digital Door Lock mulai lemah, mohon penggantian baterai Alkaline.',
                            'report_date' => now()->subDays(1)->toDateString(),
                            'priority' => 'Mendesak',
                            'status' => 'pending',
                        ]);
                    }
                    if ($r205) {
                        MaintenanceTicket::create([
                            'ticket_code' => 'MNT-205',
                            'room_id' => $r205->id,
                            'reporter_name' => $r205->activeTenant->name ?? 'Rizky Pratama (Cabang 2)',
                            'issue_description' => '[Cabang 2 Ringroad] Lampu LED Warm White di area Balkon Pribadi sedikit berkedip saat malam hari.',
                            'report_date' => now()->subDays(2)->toDateString(),
                            'priority' => 'Normal',
                            'status' => 'progress',
                        ]);
                    }
                    if ($r207) {
                        MaintenanceTicket::create([
                            'ticket_code' => 'MNT-207',
                            'room_id' => $r207->id,
                            'reporter_name' => $r207->activeTenant->name ?? 'Kevin Wijaya (Cabang 2)',
                            'issue_description' => '[Cabang 2 Ringroad] Pembersihan berkala filter AC Inverter & sinkronisasi koneksi WiFi Smart Android TV.',
                            'report_date' => now()->subDays(4)->toDateString(),
                            'priority' => 'Rendah',
                            'status' => 'done',
                        ]);
                    }
                }
            }

            // 2. Ensure Room Transfers exist for both Lokasi 1 and Lokasi 2
            if (RoomTransfer::count() < 3 && $loc1Rooms->count() >= 2 && $loc2Rooms->count() >= 2) {
                $r104 = $loc1Rooms->firstWhere('number', '104') ?? $loc1Rooms->first();
                $r101 = $loc1Rooms->firstWhere('number', '101') ?? $loc1Rooms->last();
                $r106 = $loc1Rooms->firstWhere('number', '106') ?? $loc1Rooms->get(1);

                $r203 = $loc2Rooms->firstWhere('number', '203') ?? $loc2Rooms->first();
                $r206 = $loc2Rooms->firstWhere('number', '206') ?? $loc2Rooms->last();
                $r204 = $loc2Rooms->firstWhere('number', '204') ?? $loc2Rooms->get(1);

                if (RoomTransfer::where('current_room_id', $r104->id)->count() === 0) {
                    RoomTransfer::create([
                        'tenant_id' => $r104->activeTenant->id ?? null,
                        'current_room_id' => $r104->id,
                        'target_room_id' => $r101->id,
                        'reason' => '[Internal Cabang 1 - Kampus Utama] Ingin pindah ke kamar VIP AC Lantai 1 agar lebih dekat area parkir motor dan gerbang utama.',
                        'preferred_date' => now()->addDays(5)->toDateString(),
                        'status' => 'pending',
                        'admin_note' => 'Menunggu verifikasi ketersediaan unit #101 akhir pekan ini.',
                    ]);
                }

                if (RoomTransfer::where('current_room_id', $r203->id)->count() === 0) {
                    RoomTransfer::create([
                        'tenant_id' => $r203->activeTenant->id ?? null,
                        'current_room_id' => $r203->id,
                        'target_room_id' => $r206->id,
                        'reason' => '[Internal Cabang 2 - Ringroad Exclusive] Upgrade ke unit Executive Suite Balkon Lantai 2 yang langsung menghadap Co-Working Space & taman.',
                        'preferred_date' => now()->addDays(3)->toDateString(),
                        'status' => 'pending',
                        'admin_note' => 'Kamar #206 siap huni, jadwal serah terima kartu Smart Lock hari Jumat.',
                    ]);
                }

                if ($r106 && $r204 && RoomTransfer::where('current_room_id', $r106->id)->count() === 0) {
                    RoomTransfer::create([
                        'tenant_id' => $r106->activeTenant->id ?? null,
                        'current_room_id' => $r106->id,
                        'target_room_id' => $r204->id,
                        'reason' => '[Pindah Lintas Cabang: Kos 1 → Kos 2] Pindah lokasi riset & magang ke kawasan Kaliurang KM 12 sehingga mengajukan pindah ke Cabang 2 Ringroad Exclusive.',
                        'preferred_date' => now()->subDays(2)->toDateString(),
                        'status' => 'approved',
                        'admin_note' => 'Disetujui Mas Robi. Penyesuaian deposit & akses Smart Lock Cabang 2 selesai.',
                    ]);
                }
            }

            // 3. Ensure Finances exist for Lokasi 2 (in addition to Lokasi 1)
            if (!empty($loc2RoomIds)) {
                $loc2FinCount = Finance::whereIn('room_id', $loc2RoomIds)->count();
                if ($loc2FinCount === 0) {
                    $r201 = $loc2Rooms->firstWhere('number', '201') ?? $loc2Rooms->first();
                    $r202 = $loc2Rooms->firstWhere('number', '202') ?? ($loc2Rooms->get(1) ?? $r201);
                    $r203 = $loc2Rooms->firstWhere('number', '203') ?? ($loc2Rooms->get(2) ?? $r201);
                    $r205 = $loc2Rooms->firstWhere('number', '205') ?? ($loc2Rooms->get(3) ?? $r201);

                    if ($r201) {
                        Finance::create([
                            'transaction_code' => 'KMR2-INC-201',
                            'room_id' => $r201->id,
                            'date' => now()->subDays(1)->toDateString(),
                            'type' => 'income',
                            'category' => 'Sewa Kamar (Cabang 2)',
                            'amount' => $r201->price ?: 2150000,
                            'description' => 'Pembayaran Sewa Kamar #201 (Executive Suite Balkon - Lokasi 2 Ringroad)',
                            'payment_method' => 'Transfer BCA',
                            'timezone' => 'WIB',
                        ]);
                    }
                    if ($r202) {
                        Finance::create([
                            'transaction_code' => 'KMR2-INC-202',
                            'room_id' => $r202->id,
                            'date' => now()->subDays(2)->toDateString(),
                            'type' => 'income',
                            'category' => 'Sewa Kamar (Cabang 2)',
                            'amount' => $r202->price ?: 2150000,
                            'description' => 'Pembayaran Sewa Kamar #202 (Executive Suite Balkon - Lokasi 2 Ringroad)',
                            'payment_method' => 'Transfer QRIS',
                            'timezone' => 'WIB',
                        ]);
                    }
                    if ($r203) {
                        Finance::create([
                            'transaction_code' => 'KMR2-INC-203',
                            'room_id' => $r203->id,
                            'date' => now()->subDays(3)->toDateString(),
                            'type' => 'income',
                            'category' => 'Sewa Kamar (Cabang 2)',
                            'amount' => $r203->price ?: 1950000,
                            'description' => 'Pembayaran Sewa Kamar #203 (Superior AC Plus - Lokasi 2 Ringroad)',
                            'payment_method' => 'Transfer Mandiri',
                            'timezone' => 'WIB',
                        ]);
                    }
                    if ($r205) {
                        Finance::create([
                            'transaction_code' => 'KMR2-EXP-WF2',
                            'room_id' => $r205->id,
                            'date' => now()->subDays(4)->toDateString(),
                            'type' => 'expense',
                            'category' => 'Internet & Smart Lock (Cabang 2)',
                            'amount' => 850000,
                            'description' => 'Langganan WiFi Dedicated 300 Mbps & Cloud Smart Lock - Lokasi 2 Ringroad Exclusive',
                            'payment_method' => 'Transfer BCA',
                            'timezone' => 'WIB',
                        ]);
                        Finance::create([
                            'transaction_code' => 'KMR2-EXP-CW2',
                            'room_id' => $r205->id,
                            'date' => now()->subDays(5)->toDateString(),
                            'type' => 'expense',
                            'category' => 'Kebersihan & Co-Working (Cabang 2)',
                            'amount' => 950000,
                            'description' => 'Housekeeping Mingguan, Kopi Pantry & Perawatan Mini Gym - Lokasi 2 Ringroad Exclusive',
                            'payment_method' => 'Tunai / Cash',
                            'timezone' => 'WIB',
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silently ignore if table not migrated yet
        }
    }

    /**
     * Return all data in JSON for reactive frontend syncing.
     */
    public function getJsonData(): JsonResponse
    {
        $setting = Setting::first();
        $rooms = Room::with('activeTenant')->orderBy('number')->get();
        $this->ensureTwoBranchSampleData($rooms);
        $finances = Finance::with('room')->orderBy('date', 'desc')->get();
        $maintenance = MaintenanceTicket::with('room')->orderBy('report_date', 'desc')->get();
        $roomTransfers = RoomTransfer::with(['user', 'tenant', 'currentRoom', 'targetRoom'])->orderBy('created_at', 'desc')->get();

        return response()->json([
            'settings' => $setting,
            'rooms' => $rooms,
            'finances' => $finances,
            'maintenance' => $maintenance,
            'room_transfers' => $roomTransfers,
        ]);
    }

    /**
     * Upload room photo for admin.
     */
    public function uploadRoomPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $file = $request->file('photo');
            $filename = 'room_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destPath = public_path('uploads/rooms');
            if (!file_exists($destPath)) {
                mkdir($destPath, 0755, true);
            }
            $file->move($destPath, $filename);
            return response()->json([
                'success' => true,
                'path' => 'uploads/rooms/' . $filename,
                'full_url' => asset('uploads/rooms/' . $filename),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal mengunggah foto'], 400);
    }

    /**
     * Store or update a room.
     */
    public function saveRoom(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'number' => 'required|string',
            'floor' => 'required|integer',
            'type' => 'required|string',
            'location' => 'nullable|string',
            'price' => 'required|numeric',
            'facilities' => 'nullable|array',
            'status' => 'required|in:available,occupied,due,repair',
            'image' => 'nullable|string',
            'images' => 'nullable|array',
            'custom_interior_request' => 'nullable|string',
            'custom_interior_budget' => 'nullable|numeric|min:0',
            'custom_interior_status' => 'nullable|string|in:none,pending,approved,in_progress,completed,rejected',
            'custom_interior_admin_notes' => 'nullable|string',
        ]);

        if (empty($validated['location'])) {
            $validated['location'] = 'Lokasi 1';
        }

        // Support array of up to 4 slide images
        if ($request->has('images') && is_array($request->input('images'))) {
            $imgs = array_values(array_filter($request->input('images')));
            if (!empty($imgs)) {
                $validated['images'] = array_slice($imgs, 0, 4); // max 4
                $validated['image'] = $imgs[0];
            }
        } elseif ($request->filled('image')) {
            $validated['image'] = $request->input('image');
        }

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'room_' . time() . '_' . rand(10, 99) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images/rooms'), $filename);
            $validated['image'] = 'assets/images/rooms/' . $filename;
            if (isset($validated['images']) && is_array($validated['images'])) {
                $validated['images'][0] = $validated['image'];
            } else {
                $validated['images'] = [$validated['image']];
            }
        }

        if (!empty($validated['id'])) {
            $room = Room::findOrFail($validated['id']);
            $room->update($validated);
        } else {
            if (empty($validated['image'])) {
                $validated['image'] = 'assets/images/rooms/room_1.jpg';
            }
            if (empty($validated['images'])) {
                $validated['images'] = [
                    'assets/images/rooms/room_1.jpg',
                    'assets/images/rooms/room_2.jpg',
                    'assets/images/rooms/room_3.jpg',
                ];
            }
            $room = Room::create($validated);
        }

        return response()->json(['success' => true, 'room' => $room]);
    }

    /**
     * Quick status update for a room.
     */
    public function updateRoomStatus(Request $request, $id): JsonResponse
    {
        $room = Room::where('number', $id)->orWhere('id', $id)->firstOrFail();
        $status = $request->input('status');

        if (in_array($status, ['available', 'occupied', 'due', 'repair'])) {
            $room->status = $status;
            $room->save();
            return response()->json(['success' => true, 'room' => $room]);
        }

        return response()->json(['success' => false, 'message' => 'Status tidak valid'], 422);
    }

    /**
     * Admin updates custom interior request, budget, status, notes.
     */
    public function updateCustomInterior(Request $request, $id): JsonResponse
    {
        $room = Room::where('number', $id)->orWhere('id', $id)->firstOrFail();

        $validated = $request->validate([
            'custom_interior_budget' => 'nullable|numeric|min:0',
            'custom_interior_status' => 'required|in:none,pending,approved,in_progress,completed,rejected',
            'custom_interior_admin_notes' => 'nullable|string|max:1000',
            'custom_interior_request' => 'nullable|string|max:1000',
        ]);

        $room->custom_interior_budget = $validated['custom_interior_budget'] ?? $room->custom_interior_budget;
        $room->custom_interior_status = $validated['custom_interior_status'];
        if (isset($validated['custom_interior_admin_notes'])) {
            $room->custom_interior_admin_notes = $validated['custom_interior_admin_notes'];
        }
        if (isset($validated['custom_interior_request'])) {
            $room->custom_interior_request = $validated['custom_interior_request'];
        }
        $room->save();

        return response()->json([
            'success' => true,
            'message' => 'Data custom interior kamar #' . $room->number . ' berhasil diperbarui!',
            'room' => $room->fresh()
        ]);
    }

    /**
     * Store new tenant and assign to room.
     */
    public function saveTenant(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'id_card' => 'nullable|string',
            'room_id' => 'required|exists:rooms,id',
            'start_date' => 'required|date',
            'due_day' => 'required|integer|min:1|max:31',
            'rent_amount' => 'required|numeric',
            'emergency_contact' => 'nullable|string',
        ]);

        $tenant = Tenant::create(array_merge($validated, ['status' => 'active']));

        $room = Room::find($validated['room_id']);
        if ($room) {
            $room->status = 'occupied';
            $room->save();
        }

        // Record initial rent payment
        Finance::create([
            'transaction_code' => 'TRX-' . rand(100, 999),
            'room_id' => $room?->id,
            'date' => $validated['start_date'],
            'type' => 'income',
            'category' => 'Sewa Pertama / Masuk',
            'amount' => $validated['rent_amount'],
            'description' => 'Pembayaran Sewa Masuk Kamar #' . $room?->number . ' (' . $validated['name'] . ')',
            'payment_method' => 'Transfer BCA',
        ]);

        return response()->json(['success' => true, 'tenant' => $tenant]);
    }

    /**
     * Process checkout for tenant.
     */
    public function checkoutTenant(Request $request, $roomId): JsonResponse
    {
        $room = Room::where('number', $roomId)->orWhere('id', $roomId)->firstOrFail();

        Tenant::where('room_id', $room->id)->where('status', 'active')->update(['status' => 'inactive']);

        $room->status = 'available';
        $room->save();

        return response()->json(['success' => true, 'message' => 'Kamar berhasil dikosongkan']);
    }

    /**
     * Record a new finance transaction.
     */
    public function saveFinance(Request $request): JsonResponse
    {
        $roomId = $request->input('room_id');
        if (!$roomId && $request->filled('room_number')) {
            $rFound = Room::where('number', $request->input('room_number'))->first();
            if ($rFound) {
                $roomId = $rFound->id;
            }
        }

        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'date' => 'required|date',
            'description' => 'required|string',
            'amount' => 'required|numeric',
            'payment_method' => 'required|string',
            'timezone' => 'nullable|string|in:WIB,WITA,WIT',
        ]);

        $proofImagePath = null;
        if ($request->hasFile('proof_image')) {
            $file = $request->file('proof_image');
            $proofDir = public_path('assets/images/proofs');
            if (!file_exists($proofDir)) {
                mkdir($proofDir, 0755, true);
            }
            $filename = 'proof_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move($proofDir, $filename);
            $proofImagePath = 'assets/images/proofs/' . $filename;
        }

        $sendToTenant = filter_var($request->input('send_to_tenant', true), FILTER_VALIDATE_BOOLEAN);
        $code = 'TRX-' . rand(100, 999);
        $category = $validated['type'] === 'income' ? 'Sewa Kamar' : 'Operasional Kos';

        $finance = Finance::create([
            'transaction_code' => $code,
            'room_id' => $roomId,
            'date' => $validated['date'],
            'type' => $validated['type'],
            'category' => $category,
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'payment_method' => $validated['payment_method'],
            'timezone' => $validated['timezone'] ?? 'WIB',
            'proof_image' => $proofImagePath,
            'is_sent_to_tenant' => ($validated['type'] === 'income' && $sendToTenant),
            'sent_at' => ($validated['type'] === 'income' && $sendToTenant) ? now() : null,
        ]);

        if (!empty($roomId) && $validated['type'] === 'income') {
            $room = Room::find($roomId);
            if ($room && $room->status === 'due') {
                $room->status = 'occupied';
                $room->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => $finance->is_sent_to_tenant
                ? 'Pembayaran berhasil dicatat dan kuitansi digital langsung dikirim ke pelanggan!'
                : 'Transaksi berhasil dicatat ke Buku Kas!',
            'finance' => $finance,
        ]);
    }

    /**
     * Send receipt directly to tenant.
     */
    public function sendReceiptToTenant(Request $request, $id): JsonResponse
    {
        $finance = Finance::findOrFail($id);
        $finance->is_sent_to_tenant = true;
        $finance->sent_at = now();
        $finance->save();

        if ($finance->room_id && $finance->type === 'income') {
            $room = Room::find($finance->room_id);
            if ($room && $room->status === 'due') {
                $room->status = 'occupied';
                $room->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Kuitansi pembayaran digital berhasil dikirim langsung ke Portal Pelanggan!',
            'finance' => $finance,
        ]);
    }

    /**
     * Create maintenance ticket.
     */
    public function saveMaintenance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'priority' => 'required|string',
            'issue_description' => 'required|string',
        ]);

        $room = Room::with('activeTenant')->find($validated['room_id']);
        $reporter = $room?->activeTenant?->name ?? 'Pengelola';

        $ticket = MaintenanceTicket::create([
            'ticket_code' => 'TK-' . rand(100, 999),
            'room_id' => $room->id,
            'reporter_name' => $reporter,
            'issue_description' => $validated['issue_description'],
            'report_date' => now()->toDateString(),
            'priority' => $validated['priority'],
            'status' => 'pending',
        ]);

        return response()->json(['success' => true, 'ticket' => $ticket]);
    }

    /**
     * Update ticket status.
     */
    public function updateMaintenanceStatus(Request $request, $id): JsonResponse
    {
        $ticket = MaintenanceTicket::where('ticket_code', $id)->orWhere('id', $id)->firstOrFail();
        $status = $request->input('status');

        if (in_array($status, ['pending', 'progress', 'done'])) {
            $ticket->status = $status;
            $ticket->save();
            return response()->json(['success' => true, 'ticket' => $ticket]);
        }

        return response()->json(['success' => false, 'message' => 'Status tidak valid'], 422);
    }

    /**
     * Resolves ANY Google Maps input (browser link, share link, iframe embed, or address)
     * into a guaranteed working embed URL and navigation link.
     * No manual coordinates needed from user.
     */
    private function resolveGmapsEmbed(?string $raw, ?string $address): array
    {
        $raw = trim($raw ?? '');
        $addressFallback = trim($address ?: 'Jl. Kaliurang KM 5, Gang Pandega Marta No. 18, Sleman, DI Yogyakarta');

        if (empty($raw)) {
            return [
                'url' => 'https://maps.google.com/?q=' . urlencode($addressFallback),
                'embed' => 'https://maps.google.com/maps?q=' . urlencode($addressFallback) . '&t=&z=16&ie=UTF8&iwloc=&output=embed'
            ];
        }

        // 1. If iframe code, extract src attribute
        if (str_contains($raw, '<iframe')) {
            if (preg_match('/src=["\']([^"\']+)["\']/', $raw, $m)) {
                $raw = trim($m[1]);
            }
        }

        $navUrl = $raw;

        // 2. Already an official embed URL
        if (str_contains($raw, 'google.com/maps/embed') || (str_contains($raw, 'maps.google.com') && str_contains($raw, 'output=embed'))) {
            return [
                'url' => $navUrl,
                'embed' => $raw
            ];
        }

        // 3. Resolve shortlinks (maps.app.goo.gl or goo.gl/maps) via cURL to get full URL
        $targetUrl = $raw;
        if (str_contains($raw, 'maps.app.goo.gl') || str_contains($raw, 'goo.gl/maps')) {
            try {
                $ch = curl_init($raw);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 6);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
                curl_exec($ch);
                $effective = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                curl_close($ch);
                if (!empty($effective)) {
                    $targetUrl = $effective;
                }
            } catch (\Throwable $e) {}
        }

        // Priority A: Check place name (/place/Place+Name) in Google Maps URL
        $placeName = null;
        if (preg_match('/place\/([^\/@?#]+)/', $targetUrl, $pm)) {
            $candidate = urldecode(str_replace('+', ' ', $pm[1]));
            if (!empty($candidate) && strtolower($candidate) !== 'dropped pin') {
                $placeName = $candidate;
            }
        }

        // Priority B: Check exact pin coordinates (!3d<lat>!4d<lng>)
        $exactLat = null;
        $exactLng = null;
        if (preg_match('/!3d(-?\d{1,3}\.\d+)!4d(-?\d{1,3}\.\d+)/', $targetUrl, $m)) {
            $exactLat = (float) $m[1];
            $exactLng = (float) $m[2];
        }

        // Priority C: Check q parameter (e.g. ?q=-8.123,113.123 or ?q=Place+Name)
        if (str_contains($targetUrl, 'q=')) {
            $qs = parse_url($targetUrl, PHP_URL_QUERY) ?? '';
            parse_str($qs, $params);
            if (!empty($params['q'])) {
                $qVal = trim($params['q']);
                if (preg_match('/^(-?\d{1,3}\.\d+),\s*(-?\d{1,3}\.\d+)$/', $qVal, $qm)) {
                    $exactLat = (float) $qm[1];
                    $exactLng = (float) $qm[2];
                } elseif (empty($placeName)) {
                    $placeName = $qVal;
                }
            }
        }

        // Priority D: Viewport coordinates (@lat,lng) ONLY IF neither place name nor exact pin coordinates exist!
        if (empty($placeName) && $exactLat === null) {
            if (preg_match('/@(-?\d{1,3}\.\d{4,}),\s*(-?\d{1,3}\.\d{4,})/', $targetUrl, $vm)) {
                $exactLat = (float) $vm[1];
                $exactLng = (float) $vm[2];
            }
        }

        // If place name exists, search embed shows building, campus, marker pin, and rating
        if (!empty($placeName)) {
            return [
                'url' => $navUrl,
                'embed' => 'https://maps.google.com/maps?q=' . urlencode($placeName) . '&t=&z=16&ie=UTF8&iwloc=&output=embed'
            ];
        }

        // If exact coordinates exist
        if ($exactLat !== null && $exactLng !== null) {
            return [
                'url' => $navUrl,
                'embed' => 'https://maps.google.com/maps?q=' . $exactLat . ',' . $exactLng . '&t=&z=16&ie=UTF8&iwloc=&output=embed'
            ];
        }

        // Plain text or address
        if (!str_starts_with($raw, 'http')) {
            return [
                'url' => 'https://maps.google.com/?q=' . urlencode($raw),
                'embed' => 'https://maps.google.com/maps?q=' . urlencode($raw) . '&t=&z=16&ie=UTF8&iwloc=&output=embed'
            ];
        }

        // Fallback to address
        return [
            'url' => $navUrl,
            'embed' => 'https://maps.google.com/maps?q=' . urlencode($addressFallback) . '&t=&z=16&ie=UTF8&iwloc=&output=embed'
        ];
    }

    /**
     * Save Kos Settings.
     */
    public function saveSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kos_name' => 'nullable|string',
            'owner_name' => 'nullable|string',
            'owner_phone' => 'nullable|string',
            'kos_address' => 'nullable|string',
            'gmaps_url' => 'nullable|string',
            'gmaps_embed' => 'nullable|string',
            'location_highlights' => 'nullable',
            'branches' => 'nullable',
            'landing_content' => 'nullable',
            'bank_name' => 'nullable|string',
            'bank_account' => 'nullable|string',
            'bank_holder' => 'nullable|string',
            'payment_methods' => 'nullable',
            'wifi_list' => 'nullable',
        ]);

        if ($request->has('branches')) {
            $branches = $request->input('branches');
            if (is_string($branches)) {
                $branches = json_decode($branches, true);
            }
            if (is_array($branches)) {
                foreach ($branches as $k => $b) {
                    $mapIn = trim($b['gmaps_url'] ?? $b['gmaps_embed'] ?? '');
                    $addr = $b['address'] ?? null;
                    if (!empty($mapIn)) {
                        $res = $this->resolveGmapsEmbed($mapIn, $addr);
                        $branches[$k]['gmaps_embed'] = $res['embed'];
                        $branches[$k]['gmaps_url'] = $res['url'];
                    } elseif (!empty($addr)) {
                        $branches[$k]['gmaps_url'] = 'https://maps.google.com/?q=' . urlencode($addr);
                        $branches[$k]['gmaps_embed'] = 'https://maps.google.com/maps?q=' . urlencode($addr) . '&t=&z=16&ie=UTF8&iwloc=&output=embed';
                    }
                }
                $validated['branches'] = $branches;
            }
        }

        if ($request->has('payment_methods')) {
            $pm = $request->input('payment_methods');
            if (is_string($pm)) {
                $pm = json_decode($pm, true);
            }
            $validated['payment_methods'] = $pm;
        }

        if ($request->has('wifi_list')) {
            $wf = $request->input('wifi_list');
            if (is_string($wf)) {
                $wf = json_decode($wf, true);
            }
            $validated['wifi_list'] = $wf;
        }

        if ($request->has('location_highlights')) {
            $lh = $request->input('location_highlights');
            if (is_string($lh)) {
                $lh = json_decode($lh, true);
            }
            $validated['location_highlights'] = $lh;
        }

        if ($request->has('landing_content')) {
            $lc = $request->input('landing_content');
            if (is_string($lc)) {
                $lc = json_decode($lc, true);
            }
            $validated['landing_content'] = $lc;
        }

        // Auto-resolve Google Maps link into synchronized URL & Embed
        $mapInput = trim($validated['gmaps_url'] ?? $validated['gmaps_embed'] ?? '');
        $kosAddr = $validated['kos_address'] ?? null;

        if (!empty($mapInput)) {
            $parsedMap = $this->resolveGmapsEmbed($mapInput, $kosAddr);
            $validated['gmaps_embed'] = $parsedMap['embed'];
            $validated['gmaps_url'] = $parsedMap['url'];
        } elseif (!empty($kosAddr)) {
            $validated['gmaps_url'] = 'https://maps.google.com/?q=' . urlencode($kosAddr);
            $validated['gmaps_embed'] = 'https://maps.google.com/maps?q=' . urlencode($kosAddr) . '&t=&z=16&ie=UTF8&iwloc=&output=embed';
        }

        if (empty($validated['gmaps_url']) && !empty($validated['kos_address'])) {
            $validated['gmaps_url'] = 'https://maps.google.com/?q=' . urlencode($validated['kos_address']);
        }

        $setting = Setting::first() ?? new Setting();

        if ($request->has('kos_rules')) {
            $kr = $request->input('kos_rules');
            if (is_string($kr)) {
                $kr = json_decode($kr, true);
            }
            if (is_array($kr)) {
                $existingLc = $validated['landing_content'] ?? ($setting->landing_content ?? []);
                $existingLc['kos_rules'] = array_values(array_filter(array_map('trim', $kr), fn($r) => $r !== ''));
                $validated['landing_content'] = $existingLc;
            }
        }

        $setting->fill($validated);
        $setting->save();

        return response()->json(['success' => true, 'setting' => $setting]);
    }

    /**
     * Dedicated API to update all Landing Page content & uploaded photos.
     */
    public function saveLandingPage(Request $request): JsonResponse
    {
        $setting = Setting::first() ?? new Setting();
        $landing = $setting->landing_content ?? [];

        // Text fields
        $fields = [
            'hero_title', 'hero_desc', 'hero_btn_text', 'hero_btn_link',
            'bento_tag', 'bento_title', 'bento_desc',
            'bento_card_tag', 'bento_card_title', 'bento_card_desc', 'bento_card_btn',
            'founder_name', 'founder_role', 'founder_avatar', 'founder_quote',
            'footer_desc'
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $landing[$field] = $request->input($field);
            }
        }

        // Bento features array
        if ($request->has('bento_features')) {
            $bf = $request->input('bento_features');
            if (is_string($bf)) {
                $bf = json_decode($bf, true);
            }
            if (is_array($bf)) {
                $landing['bento_features'] = $bf;
            }
        }

        // Handle Hero Image File Upload
        if ($request->hasFile('hero_image_file')) {
            $file = $request->file('hero_image_file');
            $filename = 'hero_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images/landing'), $filename);
            $landing['hero_image'] = 'assets/images/landing/' . $filename;
        } elseif ($request->filled('hero_image_url')) {
            $landing['hero_image'] = $request->input('hero_image_url');
        }

        // Handle Bento Gallery Photos (up to 4 photos)
        if (!isset($landing['bento_gallery']) || !is_array($landing['bento_gallery'])) {
            $landing['bento_gallery'] = [
                'assets/images/hero_scandinavian_room.jpg',
                'assets/images/rooms/room_2.jpg',
                'assets/images/rooms/room_3.jpg',
                'assets/images/kos_exterior.jpg'
            ];
        }

        for ($i = 0; $i < 4; $i++) {
            if ($request->hasFile("gallery_file_{$i}")) {
                $file = $request->file("gallery_file_{$i}");
                $filename = "gallery_{$i}_" . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/images/landing'), $filename);
                $landing['bento_gallery'][$i] = 'assets/images/landing/' . $filename;
            } elseif ($request->filled("gallery_url_{$i}")) {
                $landing['bento_gallery'][$i] = $request->input("gallery_url_{$i}");
            }
        }

        $setting->landing_content = $landing;
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => 'Tampilan Landing Page berhasil diperbarui!',
            'landing_content' => $landing
        ]);
    }

    /**
     * Export all data as JSON file download.
     */
    public function exportData()
    {
        $data = [
            'settings' => Setting::first(),
            'rooms' => Room::with('activeTenant')->get(),
            'finances' => Finance::all(),
            'maintenance' => MaintenanceTicket::all(),
            'exported_at' => now()->toIso8601String(),
        ];

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="kos_mas_robi_backup_' . date('Y-m-d') . '.json"',
        ]);
    }

    /**
     * Reset database back to default seed.
     */
    public function resetData(): JsonResponse
    {
        Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
        return response()->json(['success' => true, 'message' => 'Data berhasil dikembalikan ke sampel default']);
    }

    /**
     * Review/Approve/Reject room transfer request.
     */
    public function updateRoomTransferStatus(Request $request, $id): JsonResponse
    {
        $transfer = RoomTransfer::with(['user', 'tenant', 'currentRoom', 'targetRoom'])->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string|max:500',
        ]);

        $transfer->status = $validated['status'];
        if (isset($validated['admin_note'])) {
            $transfer->admin_note = $validated['admin_note'];
        }

        if ($validated['status'] === 'approved') {
            if ($transfer->target_room_id) {
                if ($transfer->currentRoom) {
                    $transfer->currentRoom->status = 'available';
                    $transfer->currentRoom->save();
                }
                if ($transfer->targetRoom) {
                    $transfer->targetRoom->status = 'occupied';
                    $transfer->targetRoom->save();
                }
                if ($transfer->tenant) {
                    $transfer->tenant->room_id = $transfer->target_room_id;
                    $transfer->tenant->save();
                }
                if ($transfer->user) {
                    $transfer->user->room_id = $transfer->target_room_id;
                    $transfer->user->save();
                }
            }
        }

        $transfer->save();

        return response()->json([
            'success' => true,
            'message' => 'Status pengajuan pindah kamar berhasil diubah menjadi ' . ($transfer->status === 'approved' ? 'DISETUJUI' : 'DITOLAK'),
            'transfer' => $transfer->fresh(['user', 'tenant', 'currentRoom', 'targetRoom']),
        ]);
    }

    /**
     * Get financial statistics and chart data for a specific year (AJAX).
     */
    public function getFinanceStats(Request $request): JsonResponse
    {
        $year = (int)$request->input('year', 2026);
        if ($year < 2000) {
            $year = 2026;
        }
        $location = $request->input('location', 'all');

        $finances = Finance::with('room')->whereYear('date', $year)->orderBy('date', 'desc')->get();
        if (!empty($location) && $location !== 'all') {
            $finances = $finances->filter(function ($f) use ($location) {
                $rLoc = $f->room?->location ?? '';
                if (!$rLoc) {
                    $desc = strtolower(($f->description ?? '') . ' ' . ($f->category ?? '') . ' ' . ($f->transaction_code ?? ''));
                    if (str_contains($desc, 'cabang 2') || str_contains($desc, 'lokasi 2') || str_contains($desc, 'ringroad') || str_contains($desc, 'kmr2') || preg_match('/#20[1-8]\b/', $desc)) {
                        $rLoc = 'Lokasi 2';
                    } else {
                        $rLoc = 'Lokasi 1';
                    }
                }
                return $rLoc === $location;
            })->values();
        }

        $hasDb = $finances->count() > 0;
        $income = $hasDb ? (int)$finances->where('type', 'income')->sum('amount') : 0;
        $expense = $hasDb ? (int)$finances->where('type', 'expense')->sum('amount') : 0;
        $net = $income - $expense;

        $chartIncome = [];
        $chartExpense = [];
        $chartNet = [];

        for ($m = 1; $m <= 12; $m++) {
            if (!$hasDb) {
                $chartIncome[] = 0;
                $chartExpense[] = 0;
                $chartNet[] = 0;
                continue;
            }
            $inc = (int)$finances->where('type', 'income')
                ->filter(fn($f) => \Carbon\Carbon::parse($f->date)->month === $m)
                ->sum('amount');
            $exp = (int)$finances->where('type', 'expense')
                ->filter(fn($f) => \Carbon\Carbon::parse($f->date)->month === $m)
                ->sum('amount');

            $chartIncome[] = $inc;
            $chartExpense[] = $exp;
            $chartNet[] = $inc - $exp;
        }

        return response()->json([
            'success' => true,
            'year' => $year,
            'hasData' => $hasDb,
            'count' => $finances->count(),
            'income' => $income,
            'expense' => $expense,
            'net' => $net,
            'avgMonthly' => $hasDb ? (int)round(($income - $expense) / 12) : 0,
            'chartMonths' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'chartIncome' => $chartIncome,
            'chartExpense' => $chartExpense,
            'chartNet' => $chartNet,
            'finances' => $finances
        ]);
    }
}
