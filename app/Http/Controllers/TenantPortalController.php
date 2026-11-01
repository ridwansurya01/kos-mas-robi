<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $room = $user->room ?? Room::where('number', '102')->first();
        $tenant = Tenant::where('room_id', $room?->id)->where('status', 'active')->first();

        $setting = Setting::first() ?? new Setting();

        // Calculate next rent due
        $dueDay = $tenant?->due_day ?? 1;
        $now = Carbon::now();
        $dueDate = Carbon::create($now->year, $now->month, $dueDay);
        if ($dueDate->isPast() && $now->day > $dueDay) {
            $dueDate->addMonth();
        }

        // Check recent payment for this room
        $recentPayments = Finance::where('room_id', $room?->id)
            ->where('type', 'income')
            ->orderBy('date', 'desc')
            ->get();

        $myTickets = MaintenanceTicket::where('room_id', $room?->id)
            ->orderBy('report_date', 'desc')
            ->get();

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

        return view('tenant.portal', compact(
            'user',
            'room',
            'tenant',
            'allRooms',
            'totalRoomsCount',
            'availableRoomsCount',
            'occupiedRoomsCount',
            'setting',
            'dueDate',
            'recentPayments',
            'myTickets',
            'myTransfers',
            'greeting'
        ));
    }

    /**
     * Tenant submits payment confirmation.
     */
    public function submitPayment(Request $request)
    {
        $user = Auth::user();
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

        $room = $user->room ?? Room::where('number', '102')->first();
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
        $validated = $request->validate([
            'issue_description' => 'required|string',
            'priority' => 'required|string',
        ]);

        $room = $user->room ?? Room::where('number', '102')->first();

        $ticket = MaintenanceTicket::create([
            'ticket_code' => 'TK-' . rand(100, 999),
            'room_id' => $room->id,
            'reporter_name' => $user->name,
            'issue_description' => $validated['issue_description'],
            'report_date' => now()->toDateString(),
            'priority' => $validated['priority'],
            'status' => 'pending',
        ]);

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
            'avatar_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'avatar_preset' => 'nullable|string',
        ]);

        if ($request->hasFile('avatar_file')) {
            $file = $request->file('avatar_file');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images/avatars'), $filename);
            $user->avatar = 'assets/images/avatars/' . $filename;
        } elseif ($request->filled('avatar_preset')) {
            $user->avatar = $request->input('avatar_preset');
        }

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->name = $validated['name'];
        $user->birth_date = $validated['birth_date'];
        $user->origin_address = $validated['origin_address'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->save();

        // Also sync name & phone to active tenant record if exists
        if ($user->room_id) {
            $tenant = Tenant::where('room_id', $user->room_id)->where('status', 'active')->first();
            if ($tenant) {
                $tenant->name = $user->name;
                $tenant->phone = $user->phone;
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
        $room = Room::findOrFail($id);

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

        $request->validate([
            'target_room_id' => 'required|exists:rooms,id',
            'reason' => 'required|string|min:5|max:1000',
            'preferred_date' => 'nullable|date',
        ]);

        $currentRoom = $user->room ?? Room::where('number', '102')->first();
        $tenant = Tenant::where('room_id', $currentRoom?->id)->where('status', 'active')->first();

        if ($currentRoom && $request->target_room_id == $currentRoom->id) {
            return back()->with('error', 'Kamar tujuan tidak boleh sama dengan kamar Anda saat ini.');
        }

        RoomTransfer::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant?->id,
            'current_room_id' => $currentRoom?->id,
            'target_room_id' => $request->target_room_id,
            'reason' => $request->reason,
            'preferred_date' => $request->preferred_date,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Pengajuan pindah kamar berhasil dikirim! Pengelola akan segera meninjau permohonan Anda.');
    }

    /**
     * Generate and send new password to tenant's registered Gmail.
     */
    public function sendNewPasswordToGmail(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Sesi Anda telah berakhir. Silakan login kembali.'], 401);
        }

        if (empty($user->email)) {
            return response()->json(['success' => false, 'message' => 'Akun Anda belum memiliki alamat email yang terdaftar.'], 422);
        }

        $newPassword = 'Kmr' . rand(1000, 9999) . '@' . Str::random(2);
        $user->password = Hash::make($newPassword);
        $user->save();

        try {
            Mail::to($user->email)->send(new \App\Mail\NewPasswordMail($user, $newPassword, 'profile'));
        } catch (\Exception $e) {
            Log::warning('Send new password to tenant email failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Password baru berhasil dibuat dan dikirim ke Gmail (' . $user->email . ')! Silakan cek inbox/spam.',
            'new_password_preview' => $newPassword,
        ]);
    }
}
