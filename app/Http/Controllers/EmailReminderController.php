<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Room;
use App\Mail\RentDueReminderMail;
use App\Mail\WelcomeTenantMail;

class EmailReminderController extends Controller
{
    /**
     * Scan all active tenants and dispatch due reminders to their Gmail.
     * Triggers: H-14 (2 minggu), H-7 (1 minggu), H-3, H-1, Hari H (0).
     */
    public function dispatchReminders(): JsonResponse
    {
        $tenants = Tenant::with('room')->where('status', 'active')->get();
        $today = Carbon::today();
        $dispatched = [];

        foreach ($tenants as $tenant) {
            $user = User::where('room_id', $tenant->room_id)
                        ->orWhere('phone', $tenant->phone)
                        ->orWhere('name', $tenant->name)
                        ->first();

            if (!$user || empty($user->email)) {
                continue;
            }

            $room = $tenant->room ?? Room::find($tenant->room_id);
            if (!$room) {
                continue;
            }

            // Calculate next due date
            $dueDay = $tenant->due_day ?? 1;
            $dueDate = Carbon::create($today->year, $today->month, $dueDay)->startOfDay();
            if ($dueDate->isPast() && $today->day > $dueDay) {
                $dueDate->addMonth();
            }

            $diffInDays = (int) $today->diffInDays($dueDate, false);

            $reminderType = null;
            if ($diffInDays === 14) {
                $reminderType = 'h-14';
            } elseif ($diffInDays === 7) {
                $reminderType = 'h-7';
            } elseif ($diffInDays === 3) {
                $reminderType = 'h-3';
            } elseif ($diffInDays === 1) {
                $reminderType = 'h-1';
            } elseif ($diffInDays === 0) {
                $reminderType = 'h-0';
            }

            if ($reminderType) {
                try {
                    Mail::to($user->email)->send(
                        new RentDueReminderMail(
                            $user,
                            $room,
                            $reminderType,
                            $dueDate->format('d F Y'),
                            (float) ($tenant->rent_amount ?? $room->price ?? 1500000)
                        )
                    );

                    $dispatched[] = [
                        'user' => $user->name,
                        'email' => $user->email,
                        'room' => $room->number,
                        'reminder' => $reminderType,
                        'due_date' => $dueDate->format('d M Y'),
                        'diff_days' => $diffInDays
                    ];

                    Log::info("Rent reminder [{$reminderType}] sent to {$user->email} for Room #{$room->number}");
                } catch (\Exception $e) {
                    Log::error("Failed to send rent reminder to {$user->email}: " . $e->getMessage());
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($dispatched) . ' pengingat sewa berhasil diproses & dikirim ke Gmail pelanggan.',
            'dispatched_count' => count($dispatched),
            'dispatched' => $dispatched
        ]);
    }

    /**
     * Test sending a specific reminder (h-14, h-7, h-3, h-1, h-0) to a user's Gmail.
     */
    public function sendTestReminder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'reminder_type' => 'required|in:h-14,h-7,h-3,h-1,h-0',
            'room_number' => 'nullable|string',
        ]);

        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            $user = new User([
                'name' => 'Penghuni Kos Mas Robi',
                'email' => $validated['email'],
                'phone' => '081234567890'
            ]);
        }

        $room = null;
        if (!empty($validated['room_number'])) {
            $room = Room::where('number', $validated['room_number'])->first();
        }
        if (!$room) {
            $room = Room::first() ?? new Room(['number' => '102', 'type' => 'Deluxe AC', 'price' => 1500000]);
        }

        $now = Carbon::now();
        $titles = [
            'h-14' => $now->copy()->addDays(14)->format('d F Y'),
            'h-7'  => $now->copy()->addDays(7)->format('d F Y'),
            'h-3'  => $now->copy()->addDays(3)->format('d F Y'),
            'h-1'  => $now->copy()->addDays(1)->format('d F Y'),
            'h-0'  => $now->format('d F Y'),
        ];
        $dueDate = $titles[$validated['reminder_type']];

        try {
            Mail::to($user->email)->send(
                new RentDueReminderMail(
                    $user,
                    $room,
                    $validated['reminder_type'],
                    $dueDate,
                    (float) ($room->price ?? 1500000)
                )
            );

            return response()->json([
                'success' => true,
                'message' => "Pengingat [{$validated['reminder_type']}] berhasil dikirim ke {$user->email}!",
                'target' => $user->email,
                'type' => $validated['reminder_type'],
                'due_date' => $dueDate
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage()
            ], 500);
        }
    }
}

