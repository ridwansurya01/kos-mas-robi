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
use App\Models\User;
use App\Models\SurveyRequest;
use Illuminate\Support\Facades\Hash;
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
        if (!cache()->has('kos_two_branch_seeded')) {
            $this->ensureTwoBranchSampleData($rooms);
            cache()->put('kos_two_branch_seeded', true, now()->addDays(7));
        }

        $totalRooms = $rooms->count();
        $occupiedRooms = $rooms->whereIn('status', ['occupied', 'due'])->count();
        $availableRooms = $rooms->where('status', 'available')->count();
        $dueRooms = $rooms->where('status', 'due')->count();

        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        // Optimized single-load queries to minimize server TTFB
        $allFinances = Finance::with('room')->orderBy('date', 'desc')->get();
        $allMaintenance = MaintenanceTicket::with('room')->orderBy('report_date', 'desc')->get();
        $allSurveys = SurveyRequest::orderBy('created_at', 'desc')->get();

        $monthlyIncome = (float) $allFinances->where('type', 'income')->sum('amount');
        $totalExpense = (float) $allFinances->where('type', 'expense')->sum('amount');
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

        // Analisis Keuangan Berdasarkan Tahun Otomatis (Range Panjang 2020 s/d 2070+)
        $selectedYear = (int)$request->input('year', 2026);
        if ($selectedYear < 2020) {
            $selectedYear = 2026;
        }
        $minYear = 2020;
        $maxYear = max(2070, $selectedYear + 10);
        $availableYears = range($minYear, $maxYear);

        $allYearFinances = $allFinances->filter(function($f) use ($selectedYear) {
            if (empty($f->date)) return false;
            return \Carbon\Carbon::parse($f->date)->year === $selectedYear;
        });
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

        $activeTickets = $allMaintenance->where('status', '!=', 'done')->count();

        $roomTransfers = RoomTransfer::with(['user', 'tenant', 'currentRoom', 'targetRoom'])
            ->orderBy('created_at', 'desc')
            ->get();
        $pendingTransfersCount = $roomTransfers->where('status', 'pending')->count();

        // Data Penagihan Sewa (Tagihan Terpisah)
        $billingList = $rooms->filter(function($r) {
            return in_array($r->status, ['occupied', 'due']);
        })->values();

        // Data Akun Admin Operasional (Khusus Manajemen Owner)
        $allAdmins = User::where('role', 'admin')->orderBy('created_at', 'desc')->get();
        $totalAdminsCount = $allAdmins->count();

        // Data Penghuni Kos Aktif (Terbaru di paling atas)
        $allTenants = Tenant::with('room')->where('status', 'active')->orderBy('id', 'desc')->get();

        return view('dashboard', compact(
            'allTenants',
            'allAdmins',
            'totalAdminsCount',
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
            'allSurveys',
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
        $tenants = Tenant::with('room')->where('status', 'active')->orderBy('id', 'desc')->get();

        return response()->json([
            'settings' => $setting,
            'rooms' => $rooms,
            'tenants' => $tenants,
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
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
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
     * Delete a single room from system.
     */
    public function deleteRoom($id): JsonResponse
    {
        $room = Room::where('id', $id)->orWhere('number', $id)->first();
        if (!$room) {
            return response()->json(['success' => false, 'message' => 'Unit kamar tidak ditemukan.'], 404);
        }

        if ($room->activeTenant) {
            return response()->json([
                'success' => false,
                'message' => 'Kamar #' . $room->number . ' masih dihuni oleh ' . $room->activeTenant->name . '. Silakan proses checkout penghuni terlebih dahulu sebelum menghapus kamar.'
            ], 422);
        }

        $roomNumber = $room->number;
        $roomFloor = (int)$room->floor;

        // Nullify foreign key references safely
        \App\Models\Tenant::where('room_id', $room->id)->update(['room_id' => null]);
        \App\Models\Finance::where('room_id', $room->id)->update(['room_id' => null]);
        \App\Models\MaintenanceTicket::where('room_id', $room->id)->update(['room_id' => null]);
        \App\Models\RoomTransfer::where('current_room_id', $room->id)->update(['current_room_id' => null]);
        \App\Models\RoomTransfer::where('target_room_id', $room->id)->update(['target_room_id' => null]);
        \App\Models\User::where('room_id', $room->id)->update(['room_id' => null]);

        $room->delete();

        $remainingOnFloor = Room::where('floor', $roomFloor)->count();

        return response()->json([
            'success' => true,
            'message' => 'Kamar #' . $roomNumber . ' berhasil dihapus dari sistem kos.',
            'room_number' => $roomNumber,
            'floor' => $roomFloor,
            'remaining_on_floor' => $remainingOnFloor
        ]);
    }

    /**
     * Delete an entire floor and all rooms located on it.
     */
    public function deleteFloor(Request $request, $floor): JsonResponse
    {
        $floorNum = (int)$floor;
        if ($floorNum <= 0) {
            return response()->json(['success' => false, 'message' => 'Nomor tingkat lantai tidak valid.'], 422);
        }

        $rooms = Room::where('floor', $floorNum)->get();

        $activeTenants = [];
        foreach ($rooms as $room) {
            if ($room->activeTenant) {
                $activeTenants[] = '#' . $room->number . ' (' . $room->activeTenant->name . ')';
            }
        }

        if (!empty($activeTenants)) {
            return response()->json([
                'success' => false,
                'message' => 'Lantai ' . $floorNum . ' tidak dapat dihapus karena masih ada penghuni aktif di kamar: ' . implode(', ', $activeTenants) . '. Silakan checkout penghuni terlebih dahulu.'
            ], 422);
        }

        $deletedCount = 0;
        foreach ($rooms as $room) {
            \App\Models\Tenant::where('room_id', $room->id)->update(['room_id' => null]);
            \App\Models\Finance::where('room_id', $room->id)->update(['room_id' => null]);
            \App\Models\MaintenanceTicket::where('room_id', $room->id)->update(['room_id' => null]);
            \App\Models\RoomTransfer::where('current_room_id', $room->id)->update(['current_room_id' => null]);
            \App\Models\RoomTransfer::where('target_room_id', $room->id)->update(['target_room_id' => null]);
            \App\Models\User::where('room_id', $room->id)->update(['room_id' => null]);
            $room->delete();
            $deletedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => 'Lantai ' . $floorNum . ' beserta ' . $deletedCount . ' unit kamar di dalamnya berhasil dihapus.',
            'floor' => $floorNum,
            'deleted_rooms_count' => $deletedCount
        ]);
    }

    /**
     * Store new tenant, assign to room, and create login account.
     */
    public function saveTenant(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string',
            'password' => 'nullable|string|min:6',
            'id_card' => 'nullable|string',
            'room_id' => 'required|exists:rooms,id',
            'start_date' => 'required|date',
            'due_day' => 'required|integer|min:1|max:31',
            'rent_amount' => 'required|numeric',
            'emergency_contact' => 'nullable|string',
        ]);

        $tenantPhone = !empty($validated['phone']) ? trim($validated['phone']) : '-';

        $tenantData = [
            'name' => $validated['name'],
            'phone' => $tenantPhone,
            'id_card' => $validated['id_card'] ?? null,
            'room_id' => $validated['room_id'],
            'start_date' => $validated['start_date'],
            'due_day' => $validated['due_day'],
            'rent_amount' => $validated['rent_amount'],
            'emergency_contact' => $validated['emergency_contact'] ?? null,
            'status' => 'active',
        ];

        $tenant = Tenant::create($tenantData);

        $room = Room::find($validated['room_id']);
        if ($room) {
            $room->status = 'occupied';
            $room->save();
        }

        // Create or update User login account for this tenant
        $plainPassword = !empty($validated['password']) ? trim($validated['password']) : ('kost' . rand(1000, 9999));
        $user = User::where('email', $validated['email'])->first();

        if ($user) {
            $user->name = $validated['name'];
            if (!empty($validated['phone']) && $validated['phone'] !== '-') {
                $user->phone = $validated['phone'];
            }
            $user->room_id = $validated['room_id'];
            $user->role = 'tenant';
            if (!empty($validated['password'])) {
                $user->password = Hash::make($plainPassword);
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_profile_completed')) {
                $user->is_profile_completed = false;
            }
            $user->save();
        } else {
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => (!empty($validated['phone']) && $validated['phone'] !== '-') ? $validated['phone'] : null,
                'password' => Hash::make($plainPassword),
                'role' => 'tenant',
                'room_id' => $validated['room_id'],
                'branch_preference' => $room?->location ?? 'Lokasi 1',
            ];
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_profile_completed')) {
                $userData['is_profile_completed'] = false;
            }
            $user = User::create($userData);
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

        $setting = Setting::first();
        $loginUrl = route('login');
        $roomInfo = 'Kamar #' . ($room?->number ?? '-') . ' (' . ($room?->location ?? 'Lokasi 1') . ')';

        return response()->json([
            'success' => true,
            'message' => 'Penghuni baru dan akun login berhasil didaftarkan!',
            'tenant' => $tenant->load('room'),
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'plain_password' => $plainPassword,
                'room_info' => $roomInfo,
                'login_url' => $loginUrl,
            ]
        ]);
    }

    /**
     * Process checkout for tenant.
     */
    public function checkoutTenant(Request $request, $roomId): JsonResponse
    {
        $room = Room::where('number', $roomId)->orWhere('id', $roomId)->firstOrFail();

        Tenant::where('room_id', $room->id)->where('status', 'active')->update(['status' => 'inactive']);

        // Disassociate any registered user account linked to this room
        User::where('room_id', $room->id)->update(['room_id' => null]);

        $room->status = 'available';
        $room->booked_by_user_id = null;
        $room->booked_by_name = null;
        $room->booked_by_phone = null;
        $room->save();

        return response()->json(['success' => true, 'message' => 'Kamar berhasil dikosongkan']);
    }

    /**
     * Update payment due day for a tenant or room.
     */
    public function updateTenantDueDay(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'due_day' => 'required|integer|min:1|max:31',
        ]);

        $dueDay = (int) $validated['due_day'];

        // 1. Try finding by Tenant ID directly
        $tenant = Tenant::find($id);

        // 2. If not found, try finding by Room ID or Room number
        if (!$tenant) {
            $room = Room::where('id', $id)->orWhere('number', $id)->first();
            if ($room) {
                $tenant = $room->activeTenant;
                if (!$tenant) {
                    $tenant = $room->tenants()->where('status', 'active')->first();
                }
            }
        }

        if ($tenant) {
            $tenant->due_day = $dueDay;
            $tenant->save();

            return response()->json([
                'success' => true,
                'message' => "Tanggal jatuh tempo sewa untuk {$tenant->name} berhasil diubah ke tanggal {$dueDay} setiap bulan.",
                'tenant' => $tenant,
                'due_day' => $dueDay,
            ]);
        }

        // 3. Fallback: If room exists without active tenant record yet, create an active tenant record
        $room = Room::where('id', $id)->orWhere('number', $id)->first();
        if ($room) {
            $tenantName = $room->booked_by_name ?: ($room->tenant ?: ('Penghuni Kamar #' . $room->number));
            $tenant = Tenant::create([
                'name' => $tenantName,
                'phone' => $room->booked_by_phone ?: ($room->phone ?: '-'),
                'room_id' => $room->id,
                'start_date' => now()->toDateString(),
                'due_day' => $dueDay,
                'rent_amount' => $room->price,
                'status' => 'active',
            ]);

            return response()->json([
                'success' => true,
                'message' => "Tanggal jatuh tempo sewa untuk {$tenant->name} berhasil diatur ke tanggal {$dueDay} setiap bulan.",
                'tenant' => $tenant,
                'due_day' => $dueDay,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Data penghuni atau kamar tidak ditemukan.',
        ], 404);
    }

    /**
     * Get single tenant details for editing.
     */
    public function getTenant($id): JsonResponse
    {
        $tenant = Tenant::with('room')->find($id);
        if (!$tenant) {
            $room = Room::where('id', $id)->orWhere('number', $id)->first();
            if ($room) {
                $tenant = $room->activeTenant ?: $room->tenants()->where('status', 'active')->first();
                if (!$tenant) {
                    $tenantName = $room->booked_by_name ?: ($room->tenant ?: ('Penghuni Kamar #' . $room->number));
                    $tenant = Tenant::create([
                        'room_id' => $room->id,
                        'name' => $tenantName,
                        'phone' => $room->booked_by_phone ?: ($room->phone ?: '081234567890'),
                        'start_date' => now()->toDateString(),
                        'due_day' => $room->due_day ?? 1,
                        'rent_amount' => $room->price ?? 1500000,
                        'status' => 'active',
                    ]);
                }
            }
        }

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Data penghuni tidak ditemukan.',
            ], 404);
        }

        // Find linked user login
        $user = null;
        if ($tenant->room_id) {
            $user = User::where('room_id', $tenant->room_id)->where('role', 'tenant')->first();
        }
        if (!$user && $tenant->phone) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $tenant->phone);
            if (strlen($cleanPhone) >= 8) {
                $user = User::where('phone', 'like', '%' . substr($cleanPhone, -8))->where('role', 'tenant')->first();
            }
        }

        return response()->json([
            'success' => true,
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'phone' => $tenant->phone,
                'id_card' => $tenant->id_card,
                'id_card_photo' => $tenant->id_card_photo ? asset($tenant->id_card_photo) : ($user?->id_card_photo ? asset($user->id_card_photo) : null),
                'room_id' => $tenant->room_id,
                'room_number' => $tenant->room?->number ?? '',
                'start_date' => $tenant->start_date ? $tenant->start_date->format('Y-m-d') : '',
                'due_day' => (int) ($tenant->due_day ?? 1),
                'rent_amount' => (float) ($tenant->rent_amount ?? ($tenant->room?->price ?? 0)),
                'emergency_contact' => $tenant->emergency_contact,
                'status' => $tenant->status ?? 'active',
                'user_email' => $user?->email ?? '',
            ]
        ]);
    }

    /**
     * Update tenant data.
     */
    public function updateTenant(Request $request, $id): JsonResponse
    {
        $tenant = Tenant::with('room')->find($id);
        if (!$tenant) {
            $room = Room::where('id', $id)->orWhere('number', $id)->first();
            if ($room) {
                $tenant = $room->activeTenant ?: $room->tenants()->where('status', 'active')->first();
            }
        }

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Data penghuni tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'id_card' => 'nullable|string|max:50',
            'room_id' => 'required|exists:rooms,id',
            'start_date' => 'required|date',
            'due_day' => 'required|integer|min:1|max:31',
            'rent_amount' => 'required|numeric|min:0',
            'emergency_contact' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        $oldRoomId = $tenant->room_id;
        $newRoomId = (int) $validated['room_id'];

        $tenant->name = $validated['name'];
        $tenant->phone = $validated['phone'];
        $tenant->id_card = $validated['id_card'] ?? null;
        $tenant->room_id = $newRoomId;
        $tenant->start_date = $validated['start_date'];
        $tenant->due_day = (int) $validated['due_day'];
        $tenant->rent_amount = $validated['rent_amount'];
        $tenant->emergency_contact = $validated['emergency_contact'] ?? null;
        $tenant->save();

        // Handle room assignment if changed
        if ($oldRoomId != $newRoomId) {
            if ($oldRoomId) {
                $oldRoom = Room::find($oldRoomId);
                if ($oldRoom) {
                    $hasOther = Tenant::where('room_id', $oldRoomId)->where('status', 'active')->where('id', '!=', $tenant->id)->exists();
                    if (!$hasOther) {
                        $oldRoom->status = 'available';
                        $oldRoom->save();
                    }
                }
            }
            $newRoom = Room::find($newRoomId);
            if ($newRoom) {
                $newRoom->status = 'occupied';
                $newRoom->save();
            }
        } else {
            $curRoom = Room::find($newRoomId);
            if ($curRoom && $curRoom->status === 'available') {
                $curRoom->status = 'occupied';
                $curRoom->save();
            }
        }

        // Update associated user login if present
        $user = null;
        if (!empty($validated['email'])) {
            $user = User::where('email', $validated['email'])->first();
        }
        if (!$user && $oldRoomId) {
            $user = User::where('room_id', $oldRoomId)->where('role', 'tenant')->first();
        }

        if ($user) {
            $user->name = $validated['name'];
            $user->phone = $validated['phone'];
            $user->room_id = $newRoomId;
            if (!empty($validated['email'])) {
                $user->email = $validated['email'];
            }
            if (!empty($validated['password'])) {
                $user->password = Hash::make(trim($validated['password']));
            }
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => "Data penghuni {$tenant->name} berhasil diperbarui!",
            'tenant' => $tenant->fresh(['room']),
            'user' => $user ? ['id' => $user->id, 'email' => $user->email] : null,
        ]);
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
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
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

            // Kirim notifikasi email ke Gmail Penghuni saat status telah dilaksanakan atau telah selesai
            if (in_array($status, ['progress', 'done'])) {
                app(\App\Services\EmailNotificationService::class)->sendMaintenanceProgressToTenant($ticket);
            }

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
        if (!auth()->check() || !auth()->user()->isStaffOrOwner()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Anda tidak memiliki akses ke pengaturan kos.'
            ], 403);
        }

        if (!auth()->user()->isOwner()) {
            if ($request->hasAny(['kos_name', 'owner_name', 'owner_phone', 'bank_name', 'bank_account', 'bank_holder'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak: Hanya Role Owner yang berhak mengubah informasi profil utama pemilik dan rekening bank.'
                ], 403);
            }
        }

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
            'survey_form_template' => 'nullable|string',
            'branch1_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'branch2_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $setting = Setting::first() ?? new Setting();
        $existingBranches = $setting->branches ?? [];

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

                // Handle Branch 1 Profile Image
                if ($request->hasFile('branch1_image')) {
                    $file1 = $request->file('branch1_image');
                    $name1 = 'branch_1_' . time() . '.' . $file1->getClientOriginalExtension();
                    $targetDir = public_path('assets/images/branches');
                    if (!file_exists($targetDir)) {
                        mkdir($targetDir, 0755, true);
                    }
                    $file1->move($targetDir, $name1);
                    if (!isset($branches['lokasi_1'])) {
                        $branches['lokasi_1'] = [];
                    }
                    $branches['lokasi_1']['image'] = 'assets/images/branches/' . $name1;
                } elseif ($request->filled('b1_image_url')) {
                    if (!isset($branches['lokasi_1'])) {
                        $branches['lokasi_1'] = [];
                    }
                    $branches['lokasi_1']['image'] = $request->input('b1_image_url');
                } elseif (empty($branches['lokasi_1']['image']) && !empty($existingBranches['lokasi_1']['image'])) {
                    $branches['lokasi_1']['image'] = $existingBranches['lokasi_1']['image'];
                }

                // Handle Branch 2 Profile Image
                if ($request->hasFile('branch2_image')) {
                    $file2 = $request->file('branch2_image');
                    $name2 = 'branch_2_' . time() . '.' . $file2->getClientOriginalExtension();
                    $targetDir = public_path('assets/images/branches');
                    if (!file_exists($targetDir)) {
                        mkdir($targetDir, 0755, true);
                    }
                    $file2->move($targetDir, $name2);
                    if (!isset($branches['lokasi_2'])) {
                        $branches['lokasi_2'] = [];
                    }
                    $branches['lokasi_2']['image'] = 'assets/images/branches/' . $name2;
                } elseif ($request->filled('b2_image_url')) {
                    if (!isset($branches['lokasi_2'])) {
                        $branches['lokasi_2'] = [];
                    }
                    $branches['lokasi_2']['image'] = $request->input('b2_image_url');
                } elseif (empty($branches['lokasi_2']['image']) && !empty($existingBranches['lokasi_2']['image'])) {
                    $branches['lokasi_2']['image'] = $existingBranches['lokasi_2']['image'];
                }

                // Handle is_active visibility toggle
                if ($request->has('b1_is_active')) {
                    $branches['lokasi_1']['is_active'] = filter_var($request->input('b1_is_active'), FILTER_VALIDATE_BOOLEAN);
                } elseif (isset($branches['lokasi_1']['is_active'])) {
                    $branches['lokasi_1']['is_active'] = filter_var($branches['lokasi_1']['is_active'], FILTER_VALIDATE_BOOLEAN);
                }

                if ($request->has('b2_is_active')) {
                    $branches['lokasi_2']['is_active'] = filter_var($request->input('b2_is_active'), FILTER_VALIDATE_BOOLEAN);
                } elseif (isset($branches['lokasi_2']['is_active'])) {
                    $branches['lokasi_2']['is_active'] = filter_var($branches['lokasi_2']['is_active'], FILTER_VALIDATE_BOOLEAN);
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

        // Broadcast update WiFi ke seluruh Penghuni jika ada perubahan wifi_list
        if ($request->has('wifi_list') && !empty($validated['wifi_list'])) {
            app(\App\Services\EmailNotificationService::class)->sendWifiUpdateToTenants($validated['wifi_list']);
        }

        // Broadcast update tata tertib ke seluruh Penghuni jika ada perubahan kos_rules
        if ($request->has('kos_rules') && !empty($existingLc['kos_rules'])) {
            app(\App\Services\EmailNotificationService::class)->sendRulesUpdateToTenants($existingLc['kos_rules']);
        }

        return response()->json(['success' => true, 'setting' => $setting]);
    }

    /**
     * Dedicated API to update all Landing Page content & uploaded photos.
     */
    public function saveLandingPage(Request $request): JsonResponse
    {
        $request->validate([
            'hero_image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'gallery_file_0' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'gallery_file_1' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'gallery_file_2' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'gallery_file_3' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $setting = Setting::first() ?? new Setting();
        $landing = $setting->landing_content ?? [];

        // Text fields
        $fields = [
            'hero_title', 'hero_desc', 'hero_btn_text', 'hero_btn_link',
            'bento_tag', 'bento_title', 'bento_desc',
            'bento_card_tag', 'bento_card_title', 'bento_card_desc', 'bento_card_btn',
            'founder_name', 'founder_role', 'founder_avatar', 'founder_quote',
            'footer_desc', 'survey_form_template'
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

        if ($request->has('survey_form_template')) {
            $setting->survey_form_template = $request->input('survey_form_template');
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
     * Save customized Login Page branding, headline, features, and notice contents.
     */
    public function saveLoginPage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'brand_title' => 'nullable|string|max:120',
            'headline' => 'nullable|string|max:255',
            'desc' => 'nullable|string|max:500',
            'feature_1' => 'nullable|string|max:150',
            'feature_2' => 'nullable|string|max:150',
            'feature_3' => 'nullable|string|max:150',
            'notice_title' => 'nullable|string|max:120',
            'notice_text' => 'nullable|string|max:500',
            'notice_btn_text' => 'nullable|string|max:100',
            'help_phone' => 'nullable|string|max:30',
            'wa_help_message' => 'nullable|string|max:500',
        ]);

        $setting = Setting::first() ?? new Setting();
        $landing = $setting->landing_content ?? [];
        if (!is_array($landing)) {
            $landing = [];
        }

        $features = [];
        if (!empty($validated['feature_1'])) $features[] = trim($validated['feature_1']);
        if (!empty($validated['feature_2'])) $features[] = trim($validated['feature_2']);
        if (!empty($validated['feature_3'])) $features[] = trim($validated['feature_3']);

        if (empty($features)) {
            $features = [
                'Akses Portal Layanan Penghuni 24 Jam',
                'Konfirmasi Pembayaran & Kuitansi Digital',
                'Smart Key Mandiri & CCTV Seluruh Lorong'
            ];
        }

        $loginPageData = [
            'brand_title' => !empty($validated['brand_title']) ? trim($validated['brand_title']) : ($setting->kos_name ?? 'SMART LIVING'),
            'headline' => !empty($validated['headline']) ? trim($validated['headline']) : "Smart Living &\nKost Eksklusif",
            'desc' => !empty($validated['desc']) ? trim($validated['desc']) : 'Sistem manajemen hunian modern, nyaman, full furnished, dan aman 24 jam di Sleman, DI Yogyakarta.',
            'features' => $features,
            'notice_title' => !empty($validated['notice_title']) ? trim($validated['notice_title']) : 'Belum Memiliki Akun?',
            'notice_text' => !empty($validated['notice_text']) ? trim($validated['notice_text']) : 'Pembuatan akun baru hanya dilakukan langsung oleh Admin saat penempatan kamar.',
            'notice_btn_text' => !empty($validated['notice_btn_text']) ? trim($validated['notice_btn_text']) : 'Hubungi Admin via WhatsApp',
            'help_phone' => !empty($validated['help_phone']) ? trim($validated['help_phone']) : ($setting->owner_phone ?? '6281234567890'),
            'wa_help_message' => !empty($validated['wa_help_message']) ? trim($validated['wa_help_message']) : ("Halo Admin " . ($setting->kos_name ?? 'Kos') . ", saya penghuni baru dan ingin dibuatkan akun login portal anak kos.")
        ];

        $landing['login_page'] = $loginPageData;
        $setting->landing_content = $landing;
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan tampilan Login Page berhasil disimpan!',
            'login_content' => $loginPageData
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
                if ($transfer->targetRoom && $transfer->targetRoom->status !== 'available') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal menyetujui mutasi: Kamar tujuan #' . $transfer->targetRoom->number . ' saat ini berstatus ' . $transfer->targetRoom->status . ' (sedang tidak kosong/tersedia).',
                    ], 422);
                }
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

        // Kirim notifikasi email ke Gmail Penghuni perihal status disetujui atau ditolak
        app(\App\Services\EmailNotificationService::class)->sendRoomTransferDecisionToTenant($transfer);

        return response()->json([
            'success' => true,
            'message' => 'Status pengajuan pindah kamar berhasil diubah menjadi ' . ($transfer->status === 'approved' ? 'DISETUJUI' : 'DITOLAK'),
            'transfer' => $transfer->fresh(['user', 'tenant', 'currentRoom', 'targetRoom']),
        ]);
    }

    /**
     * Get live notifications data for AJAX polling (Admin & Owner Header).
     */
    public function getLiveNotifications(?Request $request = null): JsonResponse
    {
        $notifPendingTransfers = RoomTransfer::with(['user', 'tenant', 'currentRoom', 'targetRoom'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $notifActiveTickets = MaintenanceTicket::with('room')
            ->whereIn('status', ['pending', 'progress'])
            ->orderBy('report_date', 'desc')
            ->get();

        $notifDueRooms = Room::with('activeTenant')
            ->where('status', 'due')
            ->orderBy('number')
            ->get();

        $notifSurveys = SurveyRequest::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalCount = $notifPendingTransfers->count() + $notifActiveTickets->count() + $notifDueRooms->count() + $notifSurveys->count();

        return response()->json([
            'success' => true,
            'totalCount' => $totalCount,
            'counts' => [
                'surveys' => $notifSurveys->count(),
                'transfers' => $notifPendingTransfers->count(),
                'complaints' => $notifActiveTickets->count(),
                'billings' => $notifDueRooms->count(),
            ],
            'surveys' => $notifSurveys->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'phone' => $s->phone ?? '-',
                    'job' => $s->job ?? 'Calon Penghuni',
                    'branch' => $s->branch ?? 'Lokasi 1',
                    'room' => $s->room ?? 'Semua Tipe',
                    'survey_date' => $s->survey_date ? $s->survey_date->format('d M Y') : 'Segera',
                    'survey_time' => $s->survey_time ?? '14:00 WIB',
                    'survey_method' => $s->survey_method ?? 'Datang Langsung',
                    'status' => $s->status,
                    'message' => $s->message,
                    'time_ago' => $s->created_at ? $s->created_at->diffForHumans(null, true) : 'Baru',
                ];
            }),
            'transfers' => $notifPendingTransfers->take(5)->map(function ($t) {
                return [
                    'id' => $t->id,
                    'userName' => $t->user?->name ?? 'Penghuni',
                    'currentRoom' => $t->currentRoom?->number ?? '-',
                    'targetRoom' => $t->targetRoom?->number ?? '-',
                    'reason' => \Illuminate\Support\Str::limit($t->reason ?? 'Permohonan mutasi', 45),
                    'time_ago' => $t->created_at ? $t->created_at->diffForHumans(null, true) : 'Baru',
                ];
            }),
            'complaints' => $notifActiveTickets->take(5)->map(function ($tk) {
                return [
                    'id' => $tk->id,
                    'roomNumber' => $tk->room?->number ?? ($tk->room_number ?? '-'),
                    'description' => \Illuminate\Support\Str::limit($tk->description, 55),
                    'reportedBy' => $tk->tenant?->name ?? ($tk->reported_by ?? 'Penghuni'),
                    'time_ago' => !empty($tk->report_date) ? \Carbon\Carbon::parse($tk->report_date)->diffForHumans(null, true) : 'Aktif',
                ];
            }),
            'billings' => $notifDueRooms->take(5)->map(function ($r) {
                return [
                    'id' => $r->id,
                    'number' => $r->number,
                    'tenantName' => $r->activeTenant?->name ?? 'Penyewa',
                    'price' => number_format($r->price ?? 0, 0, ',', '.'),
                ];
            }),
        ]);
    }

    /**
     * Update status of incoming location survey request.
     */
    public function updateSurveyStatus(Request $request, $id): JsonResponse
    {
        $survey = SurveyRequest::findOrFail($id);
        $status = $request->input('status', 'confirmed');
        if (in_array($status, ['pending', 'confirmed', 'completed', 'cancelled'])) {
            $survey->status = $status;
        }
        $survey->is_read = true;
        if ($request->filled('admin_notes')) {
            $survey->admin_notes = $request->input('admin_notes');
        }
        $survey->save();

        return response()->json([
            'success' => true,
            'message' => 'Status survei berhasil diperbarui.',
            'survey' => $survey,
        ]);
    }

    /**
     * Delete location survey request.
     */
    public function deleteSurvey(Request $request, $id): JsonResponse
    {
        $survey = SurveyRequest::findOrFail($id);
        $survey->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data survei lokasi berhasil dihapus.',
        ]);
    }

    /**
     * Add survey schedule to Calendar or generate Calendar Event Link.
     * Prepared for external Google Calendar API / CalDAV integration.
     */
    public function addToCalendar(Request $request, $id): JsonResponse
    {
        $survey = SurveyRequest::findOrFail($id);

        $setting = Setting::first();
        $kosName = $setting->kos_name ?? 'Kos Mas Robi';
        $kosAddress = $setting->kos_address ?? ($setting->address ?? '');

        // Format tanggal & jam
        $dateStr = $survey->survey_date ? $survey->survey_date->format('Y-m-d') : now()->addDay()->format('Y-m-d');
        $timeStr = $survey->survey_time ?: '14:00';

        try {
            $startDateTime = \Carbon\Carbon::parse($dateStr . ' ' . $timeStr);
        } catch (\Exception $e) {
            $startDateTime = now()->addDay()->setTime(14, 0);
        }
        $endDateTime = (clone $startDateTime)->addHour();

        $eventTitle = "Survei Kamar " . ($survey->room ?: 'Unit') . " - " . $survey->name . " (" . $kosName . ")";
        $location = ($survey->branch ?: 'Kos Mas Robi') . ($kosAddress ? (', ' . $kosAddress) : '');
        $description = "Jadwal Survei Lokasi Kamar Kos\n"
            . "Nama Calon Penghuni: " . $survey->name . "\n"
            . "Pekerjaan / Status: " . ($survey->job ?: '-') . "\n"
            . "No. WhatsApp: " . ($survey->phone ?: '-') . "\n"
            . "Cabang: " . ($survey->branch ?: '-') . "\n"
            . "Unit Kamar: " . ($survey->room ?: '-') . "\n"
            . "Metode Kunjungan: " . ($survey->survey_method ?: 'Datang Langsung') . "\n"
            . "Catatan Calon Penghuni: " . ($survey->message ?: '-') . "\n\n"
            . "Dikelola melalui Sistem Manajemen " . $kosName;

        // =========================================================================
        // TEMPAT MEMASANGKAN API KALENDER ANDA (GOOGLE CALENDAR API / MICROSOFT GRAPH / LAINNYA)
        // =========================================================================
        // Catatan Pengembang:
        // Anda dapat menambahkan pemanggilan API Kalender eksternal di blok ini:
        // Contoh:
        // if (config('services.google.calendar_enabled')) {
        //     // Panggil Google Calendar API Service di sini
        // }
        // =========================================================================

        // Web Google Calendar URL Generator (Out-of-the-box working fallback)
        $utcStart = (clone $startDateTime)->setTimezone('UTC')->format('Ymd\THis\Z');
        $utcEnd = (clone $endDateTime)->setTimezone('UTC')->format('Ymd\THis\Z');

        $googleCalendarUrl = "https://calendar.google.com/calendar/render?" . http_build_query([
            'action' => 'TEMPLATE',
            'text' => $eventTitle,
            'dates' => $utcStart . '/' . $utcEnd,
            'details' => $description,
            'location' => $location,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal survei siap ditambahkan ke kalender.',
            'event' => [
                'id' => $survey->id,
                'title' => $eventTitle,
                'start' => $startDateTime->toIso8601String(),
                'end' => $endDateTime->toIso8601String(),
                'location' => $location,
                'description' => $description,
            ],
            'calendar_url' => $googleCalendarUrl,
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

    /**
     * Simpan / Perbarui Akun Staf Admin (Khusus Role Pengelola: Owner & Admin)
     */
    public function saveAdmin(Request $request): JsonResponse
    {
        if (!auth()->check() || !auth()->user()->isStaffOrOwner()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Hanya Pengelola yang berhak mengelola akun Admin.'
            ], 403);
        }

        $adminId = $request->input('id');

        $validated = $request->validate([
            'id' => 'nullable|integer|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'branch_assignment' => 'nullable|string|in:all,Lokasi 1,Lokasi 2',
            'password' => $adminId ? 'nullable|string|min:6' : 'required|string|min:6',
        ], [
            'name.required' => 'Nama lengkap admin wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi untuk akun baru.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        // Cek duplikasi email pada pengguna lain
        $existing = User::where('email', $validated['email'])
            ->when($adminId, fn($q) => $q->where('id', '!=', $adminId))
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat email sudah digunakan oleh pengguna lain.'
            ], 422);
        }

        $branchAssignment = $validated['branch_assignment'] ?? 'all';

        if ($adminId) {
            $admin = User::where('id', $adminId)->where('role', 'admin')->first();
            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun admin tidak ditemukan atau bukan bertipe admin.'
                ], 404);
            }

            $admin->name = $validated['name'];
            $admin->email = $validated['email'];
            $admin->phone = $validated['phone'] ?? null;
            $admin->branch_assignment = $branchAssignment;
            if (!empty($validated['password'])) {
                $admin->password = Hash::make($validated['password']);
            }
            $admin->save();
            $msg = 'Akun staf Admin berhasil diperbarui.';
        } else {
            $admin = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'branch_assignment' => $branchAssignment,
                'password' => Hash::make($validated['password']),
                'role' => 'admin',
            ]);
            $msg = 'Akun staf Admin baru berhasil ditambahkan.';
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'phone' => $admin->phone,
                'role' => $admin->role,
                'branch_assignment' => $admin->branch_assignment ?? 'all',
                'created_at' => $admin->created_at ? $admin->created_at->format('d M Y') : '-',
            ]
        ]);
    }

    /**
     * Ganti Penugasan Cabang Kos Admin Langsung dari Tabel (Khusus Role Pengelola: Owner & Admin)
     */
    public function updateAdminBranch(Request $request, $id): JsonResponse
    {
        if (!auth()->check() || !auth()->user()->isStaffOrOwner()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Hanya Pengelola yang berhak mengganti penugasan cabang Admin.'
            ], 403);
        }

        $validated = $request->validate([
            'branch_assignment' => 'required|string|in:all,Lokasi 1,Lokasi 2',
        ]);

        $admin = User::where('id', $id)->where('role', 'admin')->first();
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Akun staf admin tidak ditemukan.'
            ], 404);
        }

        $admin->branch_assignment = $validated['branch_assignment'];
        $admin->save();

        $setting = Setting::first();
        $b1Name = $setting->branches['lokasi_1']['name'] ?? 'Kos Pertama';
        $b2Name = $setting->branches['lokasi_2']['name'] ?? 'Kos Kedua';

        $branchLabel = match ($admin->branch_assignment) {
            'Lokasi 1' => 'Kos Pertama (' . $b1Name . ')',
            'Lokasi 2' => 'Kos Kedua (' . $b2Name . ')',
            default => 'Kedua Kos / Semua Cabang (Kos 1 & 2)',
        };

        return response()->json([
            'success' => true,
            'message' => 'Penugasan cabang Admin "' . $admin->name . '" berhasil dialihkan ke: ' . $branchLabel,
            'branch_assignment' => $admin->branch_assignment,
            'branch_label' => $branchLabel,
        ]);
    }

    /**
     * Hapus Akun Staf Admin (Khusus Role Pengelola: Owner & Admin)
     */
    public function deleteAdmin(Request $request, $id): JsonResponse
    {
        if (!auth()->check() || !auth()->user()->isStaffOrOwner()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Hanya Pengelola yang berhak mengelola akun Admin.'
            ], 403);
        }

        if ((int)$id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus akun Anda sendiri yang sedang aktif.'
            ], 422);
        }

        $admin = User::where('id', $id)->where('role', 'admin')->first();
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Akun admin tidak ditemukan.'
            ], 404);
        }

        $adminName = $admin->name;
        $admin->delete();

        return response()->json([
            'success' => true,
            'message' => "Akun staf Admin '{$adminName}' berhasil dihapus."
        ]);
    }

    /**
     * Simpan Pengaturan Integrasi Gmail SMTP
     */
    public function saveEmailSettings(Request $request): JsonResponse
    {
        if (!auth()->check() || !auth()->user()->isOwner()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Hanya Owner yang berhak mengubah pengaturan Gmail dan notifikasi sistem.'
            ], 403);
        }

        $validated = $request->validate([
            'mail_username' => 'required|email',
            'mail_password' => 'nullable|string',
            'mail_from_name' => 'nullable|string|max:100',
            'mail_from_address' => 'nullable|email',
            'is_enabled' => 'nullable|boolean',
        ]);

        $setting = Setting::first() ?? new Setting();
        $currentSettings = $setting->email_settings ?? [];

        // Bersihkan password jika ada spasi (Google App Passwords memiliki spasi tiap 4 digit)
        $rawPassword = $validated['mail_password'] ?? null;
        if (!empty($rawPassword)) {
            $rawPassword = str_replace(' ', '', $rawPassword);
        } else {
            $rawPassword = $currentSettings['mail_password'] ?? '';
        }

        $newSettings = [
            'mail_mailer' => 'smtp',
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => 587,
            'mail_encryption' => 'tls',
            'mail_username' => trim($validated['mail_username']),
            'mail_password' => $rawPassword,
            'mail_from_address' => trim($validated['mail_from_address'] ?? $validated['mail_username']),
            'mail_from_name' => trim($validated['mail_from_name'] ?? ($setting->kos_name ?? 'Kos Mas Robi')),
            'is_enabled' => $request->boolean('is_enabled', true),
        ];

        $setting->email_settings = $newSettings;
        $setting->save();

        // Update .env secara langsung agar seragam di semua environment
        $this->syncMailEnv($newSettings);

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan akun Gmail berhasil disimpan.',
            'email_settings' => [
                'mail_username' => $newSettings['mail_username'],
                'mail_from_address' => $newSettings['mail_from_address'],
                'mail_from_name' => $newSettings['mail_from_name'],
                'has_password' => !empty($newSettings['mail_password']),
                'is_enabled' => $newSettings['is_enabled'],
            ]
        ]);
    }

    /**
     * Kirim email uji coba untuk memverifikasi koneksi SMTP Gmail
     */
    public function testSendEmail(Request $request): JsonResponse
    {
        $request->validate([
            'target_email' => 'required|email',
            'mail_username' => 'nullable|email',
            'mail_password' => 'nullable|string',
        ]);

        $setting = Setting::first();
        $stored = $setting ? ($setting->email_settings ?? []) : [];

        $username = trim($request->input('mail_username') ?: ($stored['mail_username'] ?? ''));
        $rawPwd = $request->input('mail_password');
        if (!empty($rawPwd)) {
            $password = str_replace(' ', '', $rawPwd);
        } else {
            $password = $stored['mail_password'] ?? '';
        }

        if (empty($username) || empty($password)) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat Gmail dan Sandi Aplikasi (App Password) wajib diisi terlebih dahulu.'
            ], 422);
        }

        $fromAddress = $stored['mail_from_address'] ?? $username;
        $fromName = $stored['mail_from_name'] ?? ($setting->kos_name ?? 'Kos Mas Robi');

        // Terapkan konfigurasi sementara untuk pengujian
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => 'smtp.gmail.com',
            'mail.mailers.smtp.port' => 587,
            'mail.mailers.smtp.encryption' => 'tls',
            'mail.mailers.smtp.username' => $username,
            'mail.mailers.smtp.password' => $password,
            'mail.from.address' => $fromAddress,
            'mail.from.name' => $fromName,
        ]);

        try {
            Mail::purge('smtp');
            Mail::to($request->target_email)->send(new \App\Mail\TestConnectionMail($username, $request->target_email));

            return response()->json([
                'success' => true,
                'message' => "Email uji coba berhasil dikirim ke {$request->target_email}. Silakan periksa kotak masuk atau folder spam Gmail Anda."
            ]);
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            Log::error('Gmail SMTP Test Error: ' . $msg);

            $helpHint = 'Pastikan Anda menggunakan "Sandi Aplikasi" (App Password) 16 karakter dari Google Account (bukan password login biasa Gmail), dan pastikan Verifikasi 2 Langkah (2FA) di akun Google Anda telah aktif.';

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungkan ke Gmail SMTP: ' . $msg,
                'hint' => $helpHint
            ], 500);
        }
    }

    /**
     * Sinkronkan konfigurasi ke file .env
     */
    protected function syncMailEnv(array $settings): void
    {
        try {
            $envPath = base_path('.env');
            if (!file_exists($envPath) || !is_writable($envPath)) {
                return;
            }

            $content = file_get_contents($envPath);

            $pairs = [
                'MAIL_MAILER' => 'smtp',
                'MAIL_HOST' => 'smtp.gmail.com',
                'MAIL_PORT' => '587',
                'MAIL_USERNAME' => $settings['mail_username'] ?? '',
                'MAIL_PASSWORD' => $settings['mail_password'] ?? '',
                'MAIL_ENCRYPTION' => 'tls',
                'MAIL_FROM_ADDRESS' => '"' . ($settings['mail_from_address'] ?? $settings['mail_username']) . '"',
                'MAIL_FROM_NAME' => '"' . ($settings['mail_from_name'] ?? 'Kos Mas Robi') . '"',
            ];

            foreach ($pairs as $key => $val) {
                if (preg_match("/^{$key}=.*/m", $content)) {
                    $content = preg_replace("/^{$key}=.*/m", "{$key}={$val}", $content);
                } else {
                    $content .= "\n{$key}={$val}";
                }
            }

            file_put_contents($envPath, $content);
        } catch (\Throwable $e) {
            Log::warning('Gagal menyinkronkan file .env: ' . $e->getMessage());
        }
    }
}
