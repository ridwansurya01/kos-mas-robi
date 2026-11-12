<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Finance;
use App\Models\MaintenanceTicket;
use App\Models\Setting;
use App\Models\RoomTransfer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TenantPortalController extends Controller
{
    /**
     * Display the UI Pelanggan inspired directly by the reference image.
     */
    public function index()
    {
        $user = Auth::user();

        // If no user is logged in, default to first tenant for demo or redirect to login
        if (!$user) {
            return redirect()->route('login');
        }

        // If user is owner, redirect to admin dashboard
        if ($user->isOwner()) {
            return redirect()->route('admin.dashboard');
        }

        // Get Room info for this tenant
        $room = $user->room;
        $tenant = $room ? ($room->activeTenant ?? Tenant::where('room_id', $room->id)->where('status', 'active')->first()) : null;
        if (!$tenant && !empty($user->room_id)) {
            $tenant = Tenant::where('room_id', $user->room_id)->where('status', 'active')->first() ?? Tenant::where('room_id', $user->room_id)->first();
        }
        if (!$tenant && !empty($user->phone)) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $user->phone);
            if (strlen($cleanPhone) >= 8) {
                $tenant = Tenant::where('phone', 'like', '%' . substr($cleanPhone, -8))->first();
            }
        }
        if (!$tenant && !empty($user->name)) {
            $tenant = Tenant::where('name', $user->name)->first();
        }

        // Customer is considered actively registered in a room if:
        // 1. $user->room_id is not empty
        // 2. $room exists and has status 'occupied' or 'due'
        // 3. The room's tenant record is not marked inactive (or has an active tenant)
        $hasActiveRoom = false;
        if (!empty($user->room_id) && $room && in_array($room->status, ['occupied', 'due'])) {
            $hasInactive = Tenant::where('room_id', $room->id)->where('status', 'inactive')->exists();
            $hasActive = Tenant::where('room_id', $room->id)->where('status', 'active')->exists();
            if ($hasActive || !$hasInactive) {
                $hasActiveRoom = true;
            }
        }

        // Sinkronisasi nominal tagihan sewa dengan Admin & Owner
        $monthlyRent = (float) (($tenant && $tenant->rent_amount > 0) ? $tenant->rent_amount : ($room->price ?? 0));

        // Hitung total cicilan masuk pada bulan berjalan untuk unit kamar ini (sinkron dengan tagihan di Admin/Owner)
        $nowMonth = now()->month;
        $nowYear = now()->year;
        $paidThisMonth = ($room && $hasActiveRoom)
            ? (float) Finance::where('room_id', $room->id)
                ->where('type', 'income')
                ->whereMonth('date', $nowMonth)
                ->whereYear('date', $nowYear)
                ->sum('amount')
            : 0;

        $remainingBill = max(0, $monthlyRent - $paidThisMonth);
        $isPartialInstallment = ($paidThisMonth > 0 && $remainingBill > 0);
        $isPaidThisMonth = ($room && $room->status === 'occupied' && ($paidThisMonth >= $monthlyRent || $room->status !== 'due'));
        $isDue = ($room && $room->status === 'due') || (!$isPaidThisMonth && $remainingBill > 0);
        $currentBillAmount = $isPaidThisMonth ? 0 : ($remainingBill > 0 ? $remainingBill : $monthlyRent);

        $setting = Setting::first() ?? new Setting();

        // Calculate next rent due
        $dueDay = $tenant?->due_day ?? 1;
        $now = Carbon::now();
        $dueDate = Carbon::create($now->year, $now->month, $dueDay);
        if ($dueDate->isPast() && $now->day > $dueDay) {
            $dueDate->addMonth();
        }

        // Check recent payment receipts for this room that have been verified and sent by Admin
        $recentPayments = ($room && $hasActiveRoom)
            ? Finance::where('room_id', $room->id)
                ->where('type', 'income')
                ->where('is_sent_to_tenant', true)
                ->orderBy('date', 'desc')
                ->get()
            : collect();

        $myTickets = ($room && $hasActiveRoom)
            ? MaintenanceTicket::where('room_id', $room->id)
                ->orderBy('report_date', 'desc')
                ->get()
            : collect();

        $myTransfers = RoomTransfer::where('user_id', $user->id)
            ->with(['currentRoom', 'targetRoom'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Greeting according to time of day
        $hour = (int) date('H');
        if ($hour < 11) {
            $greeting = 'Selamat Pagi,';
        } elseif ($hour < 15) {
            $greeting = 'Selamat Siang,';
        } elseif ($hour < 18) {
            $greeting = 'Selamat Sore,';
        } else {
            $greeting = 'Selamat Malam,';
        }

        // Catalog of all rooms with live availability
        $allRooms = Room::with('activeTenant', 'bookedByUser')->orderBy('number')->get();
        $totalRoomsCount = $allRooms->count();
        $availableRoomsCount = $allRooms->where('status', 'available')->count();
        $occupiedRoomsCount = $allRooms->whereIn('status', ['occupied', 'due'])->count();

        // Branch 1 vs Branch 2 Stats (Kamar Tersedia vs Sudah Disewa)
        $branch1Rooms = $allRooms->filter(fn($r) => ($r->location ?? 'Lokasi 1') !== 'Lokasi 2');
        $b1Total = $branch1Rooms->count();
        $b1Available = $branch1Rooms->where('status', 'available')->count();
        $b1Occupied = $branch1Rooms->whereIn('status', ['occupied', 'due'])->count();

        $branch2Rooms = $allRooms->filter(fn($r) => ($r->location ?? '') === 'Lokasi 2');
        $b2Total = $branch2Rooms->count();
        $b2Available = $branch2Rooms->where('status', 'available')->count();
        $b2Occupied = $branch2Rooms->whereIn('status', ['occupied', 'due'])->count();

        // Determine tenant's branch (Cabang 1 vs Cabang 2)
        $tenantBranchKey = 'lokasi_1';
        if ($room && ($room->location === 'Lokasi 2')) {
            $tenantBranchKey = 'lokasi_2';
        } elseif (($user->branch_preference ?? '') === 'Lokasi 2') {
            $tenantBranchKey = 'lokasi_2';
        }

        $branches = $setting->branches ?? Setting::defaultBranches();
        $tenantBranch = $branches[$tenantBranchKey] ?? ($branches['lokasi_1'] ?? null);

        $isProfileComplete = $this->isProfileComplete($user, $tenant);

        return view('tenant.portal', compact(
            'user',
            'room',
            'tenant',
            'tenantBranchKey',
            'tenantBranch',
            'branches',
            'allRooms',
            'totalRoomsCount',
            'availableRoomsCount',
            'occupiedRoomsCount',
            'b1Total',
            'b1Available',
            'b1Occupied',
            'b2Total',
            'b2Available',
            'b2Occupied',
            'setting',
            'dueDate',
            'recentPayments',
            'myTickets',
            'myTransfers',
            'greeting',
            'hasActiveRoom',
            'isProfileComplete',
            'monthlyRent',
            'paidThisMonth',
            'remainingBill',
            'isPartialInstallment',
            'isPaidThisMonth',
            'isDue',
            'currentBillAmount'
        ));
    }

    /**
     * Tenant submits payment confirmation.
     */
    public function submitPayment(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->isTenant() && !$this->isProfileComplete($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses pembayaran ditangguhkan. Silakan lengkapi formulir data diri Anda terlebih dahulu.'
            ], 403);
        }
        $isCash = str_contains(strtolower($request->input('payment_method', '')), 'tunai') 
               || str_contains(strtolower($request->input('payment_method', '')), 'cash');

        // Validation rules based on payment method
        $rules = [
            'amount' => 'required|numeric|min:1000',
            'payment_method' => 'required|string',
            'timezone' => 'nullable|string|in:WIB,WITA,WIT',
            'note' => 'nullable|string',
        ];

        $messages = [];

        if ($isCash) {
            $rules['cash_date'] = 'required|date';
            $rules['proof_file'] = 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120';
            $messages['cash_date.required'] = 'Untuk pembayaran tunai (cash), mohon tentukan tanggal rencana atau penyerahan uang tunai.';
        } else {
            // Transfer Bank / QRIS: Foto bukti/nota pembayaran WAJIB diisi & lolos validasi nota
            $rules['proof_file'] = 'required|image|mimes:jpeg,png,jpg,webp|min:10|max:5120';
            $messages['proof_file.required'] = 'Foto bukti/nota transfer wajib diunggah. Mohon lampirkan struk ATM, screenshot m-banking, atau kuitansi.';
            $messages['proof_file.image'] = 'File yang diunggah harus berupa gambar nota transaksi yang sah (JPG, PNG, atau WEBP).';
            $messages['proof_file.min'] = 'Ukuran file foto nota terlalu kecil. Mohon unggah bukti pembayaran yang jelas dan terbaca.';
        }

        $validated = $request->validate($rules, $messages);

        $room = $user->room;
        $userTimezone = in_array(strtoupper($validated['timezone'] ?? ''), ['WIB', 'WITA', 'WIT']) 
            ? strtoupper($validated['timezone']) 
            : 'WIB';

        $cashDate = $isCash ? ($validated['cash_date'] ?? now()->toDateString()) : null;
        $trxDate = $isCash ? $cashDate : now()->toDateString();

        $proofImagePath = null;
        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $proofDir = public_path('assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'proofs');
            if (!file_exists($proofDir)) {
                @mkdir($proofDir, 0777, true);
            }
            $filename = 'proof_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $targetPath = $proofDir . DIRECTORY_SEPARATOR . $filename;
            try {
                $file->move($proofDir, $filename);
            } catch (\Throwable $e) {
                copy($file->getRealPath(), $targetPath);
            }
            $proofImagePath = 'assets/images/proofs/' . $filename;
        }

        $desc = 'Pembayaran Sewa Kamar #' . ($room?->number ?? '-') . ' (' . $user->name . ')';
        if ($isCash) {
            $desc .= ' - Tunai (Cash pada ' . \Carbon\Carbon::parse($cashDate)->translatedFormat('d M Y') . ')';
        } else {
            $desc .= ' - ' . ($validated['note'] ?? $validated['payment_method']);
        }

        $finance = Finance::create([
            'transaction_code' => 'TRX-' . rand(100, 999),
            'room_id' => $room?->id,
            'date' => $trxDate,
            'type' => 'income',
            'category' => 'Sewa Kamar',
            'amount' => $validated['amount'],
            'description' => $desc,
            'payment_method' => $validated['payment_method'],
            'timezone' => $userTimezone,
            'cash_date' => $cashDate,
            'proof_image' => $proofImagePath,
        ]);

        if ($room && $room->status === 'due') {
            $room->status = 'occupied';
            $room->save();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Konfirmasi pembayaran berhasil dicatat! Terima kasih.',
                'payment' => [
                    'description' => $finance->description,
                    'transaction_code' => $finance->transaction_code,
                    'date' => \Carbon\Carbon::parse($finance->date)->format('d M Y'),
                    'payment_time' => $finance->formatted_payment_time,
                    'payment_time_full' => $finance->formatted_payment_full,
                    'timezone' => $finance->timezone,
                    'payment_method' => $finance->payment_method,
                    'amount' => $finance->amount,
                    'amount_formatted' => 'Rp ' . number_format($finance->amount, 0, ',', '.'),
                    'proof_image' => $finance->proof_image ? asset($finance->proof_image) : null,
                ]
            ]);
        }

        return back()->with('success', 'Konfirmasi pembayaran berhasil dicatat! Terima kasih.');
    }

    /**
     * Tenant submits maintenance complaint.
     */
    public function submitComplaint(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if ($user->isTenant() && !$this->isProfileComplete($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses fitur ditangguhkan. Silakan lengkapi formulir data diri Anda terlebih dahulu.'
            ], 403);
        }

        $room = $user->room;
        $hasActiveRoom = false;
        if (!empty($user->room_id) && $room && in_array($room->status, ['occupied', 'due'])) {
            $hasInactive = Tenant::where('room_id', $room->id)->where('status', 'inactive')->exists();
            $hasActive = Tenant::where('room_id', $room->id)->where('status', 'active')->exists();
            if ($hasActive || !$hasInactive) {
                $hasActiveRoom = true;
            }
        }

        if (!$hasActiveRoom) {
            $errorMsg = 'Fitur Keluhan & Servis hanya dapat digunakan oleh customer yang terdaftar aktif di kamar kos. Akun Anda belum dikaitkan ke unit kamar aktif atau masa sewa telah selesai.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg,
                ], 403);
            }
            return back()->with('error', $errorMsg);
        }

        $validated = $request->validate([
            'issue_description' => 'required|string',
            'priority' => 'required|string',
        ]);

        $ticket = MaintenanceTicket::create([
            'ticket_code' => 'TK-' . rand(100, 999),
            'room_id' => $room->id,
            'reporter_name' => $user->name,
            'issue_description' => $validated['issue_description'],
            'report_date' => now()->toDateString(),
            'priority' => $validated['priority'],
            'status' => 'pending',
        ]);

        // Kirim notifikasi email ke Gmail Admin & Owner
        app(\App\Services\EmailNotificationService::class)->sendMaintenanceRequestToStaff($ticket);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan keluhan berhasil dikirim ke Pengelola Mas Robi!',
                'ticket' => $ticket,
            ]);
        }

        return back()->with('success', 'Laporan keluhan berhasil dikirim ke Pengelola Mas Robi!');
    }

    /**
     * Tenant updates their personal profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'origin_address' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:25',
            'id_card' => 'nullable|string|max:40',
            'id_card_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'emergency_contact' => 'nullable|string|max:255',
            'avatar_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'avatar_preset' => 'nullable|string',
        ]);

        if ($request->hasFile('id_card_photo')) {
            $file = $request->file('id_card_photo');
            $idCardDir = public_path('assets/images/id_cards');
            if (!file_exists($idCardDir)) {
                mkdir($idCardDir, 0755, true);
            }
            $filename = 'id_card_' . $user->id . '_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move($idCardDir, $filename);
            $user->id_card_photo = 'assets/images/id_cards/' . $filename;
        }

        if ($request->hasFile('avatar_file')) {
            $file = $request->file('avatar_file');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images/avatars'), $filename);
            $user->avatar = 'assets/images/avatars/' . $filename;
        } elseif ($request->filled('avatar_preset')) {
            $user->avatar = $request->input('avatar_preset');
        }

        if ($request->filled('password')) {
            // Penggantian password wajib melalui alur verifikasi OTP
            if ($user->password_otp_verified_at && now()->diffInMinutes($user->password_otp_verified_at) <= 15) {
                $request->validate([
                    'password' => 'string|min:8|confirmed',
                ]);
                $user->password = Hash::make($request->password);
                $user->password_otp_verified_at = null;
            }
        }

        $user->name = $validated['name'];
        $user->birth_date = $validated['birth_date'];
        $user->origin_address = $validated['origin_address'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_profile_completed')) {
            $user->is_profile_completed = true;
        }
        $user->save();

        // Also sync name, phone, id_card & emergency_contact to active tenant record if exists
        if ($user->room_id) {
            $tenant = Tenant::where('room_id', $user->room_id)->where('status', 'active')->first();
            if ($tenant) {
                $tenant->name = $user->name;
                $tenant->phone = $user->phone;
                if ($request->filled('id_card')) {
                    $tenant->id_card = $request->input('id_card');
                }
                if ($user->id_card_photo) {
                    $tenant->id_card_photo = $user->id_card_photo;
                }
                if ($request->filled('emergency_contact')) {
                    $tenant->emergency_contact = $request->input('emergency_contact');
                }
                $tenant->save();
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profil Anda berhasil diperbarui!',
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar_url' => asset($user->avatar ?? 'assets/images/tenant_avatar.jpg'),
                    'origin_address' => $user->origin_address,
                    'birth_date' => $user->birth_date,
                ]
            ]);
        }

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }

    /**
     * Customer requests booking & custom interior for a room.
     */
    public function requestCustomInterior(Request $request, $id)
    {
        $user = Auth::user();
        if ($user && $user->isTenant() && !$this->isProfileComplete($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses fitur ditangguhkan. Silakan lengkapi formulir data diri Anda terlebih dahulu.'
            ], 403);
        }
        $room = Room::with('activeTenant')->findOrFail($id);

        // Kamar yang sudah terisi, disewa, atau dibooking oleh orang lain TIDAK BISA dipesan
        if ($room->status !== 'available' || $room->activeTenant || (!empty($room->booked_by_user_id) && $room->booked_by_user_id != $user->id)) {
            $errorMsg = 'Maaf, Kamar #' . $room->number . ' saat ini sudah terisi atau disewa oleh penghuni lain. Pemesanan hanya dapat dilakukan untuk kamar yang kosong dan tersedia.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg,
                ], 422);
            }
            return back()->with('error', $errorMsg);
        }

        $validated = $request->validate([
            'custom_interior_request' => 'required|string|max:1000',
            'custom_interior_budget' => 'nullable|numeric|min:0',
            'booked_by_name' => 'nullable|string|max:255',
            'booked_by_phone' => 'nullable|string|max:50',
        ]);

        $room->custom_interior_request = $validated['custom_interior_request'];
        $room->custom_interior_budget = $validated['custom_interior_budget'] ?? 0;
        $room->custom_interior_status = 'pending';
        $room->booked_by_user_id = $user->id;
        $room->booked_by_name = $validated['booked_by_name'] ?? $user->name;
        $room->booked_by_phone = $validated['booked_by_phone'] ?? $user->phone;
        $room->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permintaan custom interior dan pengajuan booking kamar #' . $room->number . ' berhasil dikirim ke Pengelola Mas Robi!',
                'room' => $room->fresh(),
            ]);
        }

        return back()->with('success', 'Permintaan custom interior berhasil dikirim ke pengelola!');
    }

    /**
     * Submit pengajuan pindah kamar.
     */
    public function submitRoomTransfer(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if ($user->isTenant() && !$this->isProfileComplete($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses fitur ditangguhkan. Silakan lengkapi formulir data diri Anda terlebih dahulu.'
            ], 403);
        }

        $room = $user->room;
        $hasActiveRoom = false;
        if (!empty($user->room_id) && $room && in_array($room->status, ['occupied', 'due'])) {
            $hasInactive = Tenant::where('room_id', $room->id)->where('status', 'inactive')->exists();
            $hasActive = Tenant::where('room_id', $room->id)->where('status', 'active')->exists();
            if ($hasActive || !$hasInactive) {
                $hasActiveRoom = true;
            }
        }

        if (!$hasActiveRoom) {
            $errorMsg = 'Fitur Pindah Kamar hanya dapat digunakan oleh customer yang terdaftar aktif di kamar kos. Akun Anda belum dikaitkan ke unit kamar aktif atau masa sewa telah selesai.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg,
                ], 403);
            }
            return back()->with('error', $errorMsg);
        }

        $request->validate([
            'target_room_id' => 'required|exists:rooms,id',
            'reason' => 'required|string|min:5|max:1000',
            'preferred_date' => 'nullable|date',
        ]);

        $currentRoom = $room;
        $tenant = Tenant::where('room_id', $currentRoom->id)->where('status', 'active')->first();

        if ($currentRoom && $request->target_room_id == $currentRoom->id) {
            return back()->with('error', 'Kamar tujuan tidak boleh sama dengan kamar Anda saat ini.');
        }

        $targetRoom = Room::with('activeTenant')->findOrFail($request->target_room_id);

        // Validasi: Kamar tujuan HARUS berstatus available, belum disewa siapapun, dan belum dibooking
        if ($targetRoom->status !== 'available' || $targetRoom->activeTenant || !empty($targetRoom->booked_by_user_id)) {
            $errorMsg = 'Maaf, Kamar #' . $targetRoom->number . ' saat ini sudah terisi atau sudah disewa oleh orang lain. Pindah kamar hanya dapat diajukan ke kamar kosong yang tersedia.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 422);
            }
            return back()->with('error', $errorMsg);
        }

        // Cek apakah ada pengajuan mutasi lain yang pending untuk kamar tujuan tersebut
        $pendingOtherTransfer = RoomTransfer::where('target_room_id', $targetRoom->id)
            ->where('status', 'pending')
            ->where('user_id', '!=', $user->id)
            ->exists();
        if ($pendingOtherTransfer) {
            $errorMsg = 'Kamar #' . $targetRoom->number . ' saat ini sedang dalam proses peninjauan mutasi oleh penghuni lain. Silakan pilih unit kamar kosong lainnya.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 422);
            }
            return back()->with('error', $errorMsg);
        }

        $transfer = RoomTransfer::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant?->id,
            'current_room_id' => $currentRoom->id,
            'target_room_id' => $request->target_room_id,
            'reason' => $request->reason,
            'preferred_date' => $request->preferred_date,
            'status' => 'pending',
        ]);

        // Kirim notifikasi email ke Gmail Admin & Owner
        app(\App\Services\EmailNotificationService::class)->sendRoomTransferRequestToStaff($transfer);

        return back()->with('success', 'Pengajuan pindah kamar berhasil dikirim! Pengelola akan segera meninjau permohonan Anda.');
    }

    /**
     * Kirim kode OTP 6 digit ke Gmail terdaftar pelanggan untuk Ganti Password.
     */
    public function sendPasswordOtp(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Sesi Anda telah berakhir. Silakan login kembali.'], 401);
        }

        if (empty($user->email)) {
            return response()->json(['success' => false, 'message' => 'Akun Anda belum memiliki alamat Gmail terdaftar.'], 422);
        }

        // Jeda waktu pengiriman ulang OTP: minimal 60 detik
        if ($user->password_otp_sent_at) {
            $secondsSinceLast = abs(now()->diffInSeconds($user->password_otp_sent_at));
            if ($secondsSinceLast < 60) {
                $cooldown = (int) max(1, 60 - $secondsSinceLast);
                return response()->json([
                    'success' => false,
                    'message' => "Mohon tunggu {$cooldown} detik sebelum meminta pengiriman ulang OTP.",
                    'cooldown' => $cooldown,
                ], 429);
            }
        }

        // Generate 6 digit OTP acak
        $otpCode = sprintf('%06d', random_int(100000, 999999));

        // Simpan OTP ke database: berlaku 5 menit, reset percobaan ke 0
        $user->password_otp = $otpCode;
        $user->password_otp_expires_at = now()->addMinutes(5);
        $user->password_otp_attempts = 0;
        $user->password_otp_sent_at = now();
        $user->password_otp_verified_at = null;
        $user->save();

        // Kirim email OTP ke Gmail terdaftar
        try {
            Mail::to($user->email)->send(new \App\Mail\OtpPasswordMail($user, $otpCode));
        } catch (\Exception $e) {
            Log::warning('Send OTP to tenant email failed: ' . $e->getMessage());
        }

        Log::info("Kode OTP Ganti Password untuk {$user->email}: {$otpCode}");

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP 6 digit telah dikirim ke Gmail Anda (' . $user->email . '). Silakan cek kotak masuk atau spam.',
            'email' => $user->email,
            'cooldown' => 60,
            'otp_debug' => config('mail.default') === 'log' ? $otpCode : null,
        ]);
    }

    /**
     * Verifikasi kode OTP 6 digit yang dimasukkan pelanggan.
     */
    public function verifyPasswordOtp(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Sesi Anda telah berakhir. Silakan login kembali.'], 401);
        }

        $request->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size' => 'Kode OTP harus berupa 6 digit angka.',
        ]);

        if (empty($user->password_otp)) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada permintaan OTP aktif. Silakan klik Ganti Password untuk menerima OTP baru.',
            ], 400);
        }

        // Cek kedaluwarsa (berlaku 5 menit)
        if (!$user->password_otp_expires_at || now()->isAfter($user->password_otp_expires_at)) {
            $user->password_otp = null;
            $user->save();
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP telah kedaluwarsa (melebihi 5 menit). Silakan minta kode OTP baru.',
            ], 422);
        }

        // Cek maksimal 5 percobaan
        if ($user->password_otp_attempts >= 5) {
            $user->password_otp = null;
            $user->save();
            return response()->json([
                'success' => false,
                'message' => 'Batas percobaan OTP telah terlampaui (maksimal 5 kali salah). Silakan minta kode OTP baru.',
            ], 422);
        }

        // Tambah counter percobaan
        $user->password_otp_attempts += 1;
        $user->save();

        // Validasi kecocokan kode OTP
        if (trim($request->otp) !== trim($user->password_otp)) {
            $remaining = max(0, 5 - $user->password_otp_attempts);
            if ($remaining === 0) {
                $user->password_otp = null;
                $user->save();
                return response()->json([
                    'success' => false,
                    'message' => 'Kode OTP salah. Batas maksimal 5 percobaan telah habis. Silakan minta OTP baru.',
                    'remaining' => 0,
                ], 422);
            }
            return response()->json([
                'success' => false,
                'message' => "Kode OTP salah. Sisa kesempatan: {$remaining} kali percobaan.",
                'remaining' => $remaining,
            ], 422);
        }

        // OTP valid: hanguskan OTP (hanya dapat digunakan 1 kali) dan tandai terverifikasi
        $user->password_otp = null;
        $user->password_otp_verified_at = now();
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP berhasil diverifikasi! Silakan buat password baru Anda pada formulir di bawah ini.',
        ]);
    }

    /**
     * Simpan password baru setelah OTP terverifikasi.
     */
    public function updatePasswordWithOtp(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Sesi Anda telah berakhir. Silakan login kembali.'], 401);
        }

        // Ketentuan keamanan: OTP wajib diverifikasi sebelum formulir password baru dapat diakses / disimpan
        if (!$user->password_otp_verified_at || now()->diffInMinutes($user->password_otp_verified_at) > 15) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi verifikasi OTP telah kedaluwarsa atau belum diverifikasi. Silakan ulangi proses ganti password.',
            ], 403);
        }

        // Password minimal 8 karakter dan harus dikonfirmasi
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok. Pastikan kedua kolom sama.',
        ]);

        // Simpan password baru dengan hashing
        $user->password = Hash::make($request->password);
        $user->password_otp_verified_at = null; // reset sesi verifikasi
        $user->save();

        Log::info("Password akun pelanggan {$user->email} berhasil diubah melalui verifikasi OTP.");

        return response()->json([
            'success' => true,
            'message' => 'Password Berhasil Diubah! Silakan gunakan password baru ini saat login berikutnya.',
        ]);
    }

    /**
     * Generate and send new password to tenant's registered Gmail (Legacy compatibility).
     */
    public function sendNewPasswordToGmail(Request $request)
    {
        return $this->sendPasswordOtp($request);
    }

    /**
     * Check if a tenant has completed their personal data (phone, id_card/id_card_photo, emergency_contact).
     */
    public function isProfileComplete(User $user, ?Tenant $tenant = null): bool
    {
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_profile_completed') && $user->is_profile_completed === false) {
            return false;
        }

        if (!$tenant && $user->room_id) {
            $tenant = Tenant::where('room_id', $user->room_id)->where('status', 'active')->first() ?? Tenant::where('room_id', $user->room_id)->first();
        }

        if ($tenant) {
            $phoneValid = !empty($tenant->phone) && $tenant->phone !== '-';
            $idValid = (!empty($tenant->id_card) && $tenant->id_card !== '-') || !empty($tenant->id_card_photo) || !empty($user->id_card_photo);
            $emergencyValid = !empty($tenant->emergency_contact) && $tenant->emergency_contact !== '-';
            if (!$phoneValid || !$idValid || !$emergencyValid) {
                return false;
            }
        } elseif (empty($user->phone) || $user->phone === '-') {
            return false;
        }

        return true;
    }

    /**
     * Handle submission of mandatory tenant personal data from the initial login modal.
     */
    public function completeProfile(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Sesi login telah berakhir. Silakan login kembali.'], 401);
        }

        $tenant = $user->room ? ($user->room->activeTenant ?? Tenant::where('room_id', $user->room_id)->where('status', 'active')->first()) : null;
        if (!$tenant && $user->room_id) {
            $tenant = Tenant::where('room_id', $user->room_id)->first();
        }

        $hasExistingPhoto = !empty($user->id_card_photo) || (!empty($tenant) && !empty($tenant->id_card_photo));

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|min:9|max:25',
            'id_card' => 'nullable|string|max:40',
            'id_card_photo' => ($hasExistingPhoto ? 'nullable' : 'required') . '|image|mimes:jpeg,png,jpg,webp|max:5120',
            'emergency_contact' => 'required|string|min:5|max:255',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.min' => 'Nomor WhatsApp minimal 9 digit.',
            'id_card_photo.required' => 'Wajib melampirkan foto kartu identitas (KTP / SIM / KTM / KTA).',
            'id_card_photo.image' => 'File kartu identitas harus berupa foto/gambar.',
            'id_card_photo.mimes' => 'Format foto kartu identitas harus berupa JPG, PNG, atau WEBP.',
            'id_card_photo.max' => 'Ukuran foto kartu identitas maksimal 5 MB.',
            'emergency_contact.required' => 'Kontak darurat (Ortu / Wali / Saudara) wajib diisi.',
        ]);

        $user->name = $validated['name'];
        $user->phone = $validated['phone'];

        if ($request->hasFile('id_card_photo')) {
            $file = $request->file('id_card_photo');
            $idCardDir = public_path('assets/images/id_cards');
            if (!file_exists($idCardDir)) {
                mkdir($idCardDir, 0755, true);
            }
            $filename = 'id_card_' . $user->id . '_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move($idCardDir, $filename);
            $user->id_card_photo = 'assets/images/id_cards/' . $filename;
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_profile_completed')) {
            $user->is_profile_completed = true;
        }
        $user->save();

        if ($tenant) {
            $tenant->name = $validated['name'];
            $tenant->phone = $validated['phone'];
            if (!empty($validated['id_card'])) {
                $tenant->id_card = $validated['id_card'];
            }
            if ($user->id_card_photo) {
                $tenant->id_card_photo = $user->id_card_photo;
            }
            $tenant->emergency_contact = $validated['emergency_contact'];
            $tenant->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data diri dan foto kartu identitas Anda berhasil disimpan! Seluruh fitur portal kini telah aktif.',
            'user' => [
                'name' => $user->name,
                'phone' => $user->phone,
                'id_card' => $tenant?->id_card,
                'id_card_photo' => $user->id_card_photo ? asset($user->id_card_photo) : null,
                'emergency_contact' => $tenant?->emergency_contact,
            ]
        ]);
    }
}
