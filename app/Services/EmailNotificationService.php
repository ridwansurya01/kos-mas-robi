<?php

namespace App\Services;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Room;
use App\Models\RoomTransfer;
use App\Models\MaintenanceTicket;
use App\Models\Finance;
use App\Models\Setting;
use App\Mail\RentDueReminderMail;
use App\Mail\StaffRentReminderMail;
use App\Mail\RoomTransferRequestMail;
use App\Mail\RoomTransferDecisionMail;
use App\Mail\MaintenanceRequestMail;
use App\Mail\MaintenanceProgressMail;
use App\Mail\WifiUpdateMail;
use App\Mail\RulesUpdateMail;
use App\Mail\MonthlyFinanceReportMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailNotificationService
{
    /**
     * Dispatch rent due reminders based on exact days:
     * - Tenant: H-7, H-3, H-1, H-0
     * - Admin & Owner: H-3, H-1, H-0 (filtered by assigned branch)
     */
    public function sendRentReminders(): array
    {
        $tenants = Tenant::with('room')->where('status', 'active')->get();
        $today = Carbon::today();
        $dispatched = [];

        // Preload Owners and Admins
        $owners = User::where('role', 'owner')->whereNotNull('email')->get();
        $admins = User::where('role', 'admin')->whereNotNull('email')->get();

        foreach ($tenants as $tenant) {
            $user = User::where('room_id', $tenant->room_id)
                        ->orWhere('phone', $tenant->phone)
                        ->orWhere('name', $tenant->name)
                        ->first();

            $room = $tenant->room ?? Room::find($tenant->room_id);
            if (!$room) {
                continue;
            }

            // Calculate next due date
            $dueDay = (int) ($tenant->due_day ?? 1);
            $dueDate = Carbon::create($today->year, $today->month, min($dueDay, 28))->startOfDay();
            if ($dueDate->isPast() && $today->day > $dueDay) {
                $dueDate->addMonth();
            }

            $diffInDays = (int) $today->diffInDays($dueDate, false);
            $reminderType = null;

            if ($diffInDays === 7) {
                $reminderType = 'h-7';
            } elseif ($diffInDays === 3) {
                $reminderType = 'h-3';
            } elseif ($diffInDays === 1) {
                $reminderType = 'h-1';
            } elseif ($diffInDays === 0) {
                $reminderType = 'h-0';
            }

            if (!$reminderType) {
                continue;
            }

            $amount = (float) ($tenant->rent_amount ?? $room->price ?? 1500000);
            $dueDateFormatted = $dueDate->format('d F Y');

            // 1. Send to Tenant (H-7, H-3, H-1, H-0)
            if ($user && !empty($user->email)) {
                try {
                    Mail::to($user->email)->send(
                        new RentDueReminderMail($user, $room, $reminderType, $dueDateFormatted, $amount)
                    );
                    $dispatched[] = [
                        'recipient_type' => 'tenant',
                        'email' => $user->email,
                        'name' => $user->name,
                        'room' => $room->number,
                        'reminder' => $reminderType,
                        'status' => 'sent',
                    ];
                    Log::info("Rent reminder [{$reminderType}] sent to tenant: {$user->email} for Room #{$room->number}");
                } catch (\Exception $e) {
                    Log::error("Failed sending rent reminder to tenant {$user->email}: " . $e->getMessage());
                }
            }

            // 2. Send to Admin & Owner on H-3, H-1, and H-0
            if (in_array($reminderType, ['h-3', 'h-1', 'h-0'])) {
                // Owner receives all
                foreach ($owners as $owner) {
                    try {
                        Mail::to($owner->email)->send(
                            new StaffRentReminderMail($owner, $user, $room, $reminderType, $dueDateFormatted, $amount)
                        );
                        $dispatched[] = [
                            'recipient_type' => 'owner',
                            'email' => $owner->email,
                            'name' => $owner->name,
                            'room' => $room->number,
                            'reminder' => $reminderType,
                            'status' => 'sent',
                        ];
                        Log::info("Staff rent reminder [{$reminderType}] sent to owner: {$owner->email} for Room #{$room->number}");
                    } catch (\Exception $e) {
                        Log::error("Failed sending staff rent reminder to owner {$owner->email}: " . $e->getMessage());
                    }
                }

                // Admins receive based on assigned branch
                foreach ($admins as $admin) {
                    $assignment = $admin->branch_assignment ?? 'all';
                    $roomLocation = $room->location ?? 'Lokasi 1';
                    $canReceive = ($assignment === 'all' || $assignment === $roomLocation);

                    if ($canReceive) {
                        try {
                            Mail::to($admin->email)->send(
                                new StaffRentReminderMail($admin, $user, $room, $reminderType, $dueDateFormatted, $amount)
                            );
                            $dispatched[] = [
                                'recipient_type' => 'admin',
                                'email' => $admin->email,
                                'name' => $admin->name,
                                'room' => $room->number,
                                'reminder' => $reminderType,
                                'status' => 'sent',
                            ];
                            Log::info("Staff rent reminder [{$reminderType}] sent to admin: {$admin->email} for Room #{$room->number}");
                        } catch (\Exception $e) {
                            Log::error("Failed sending staff rent reminder to admin {$admin->email}: " . $e->getMessage());
                        }
                    }
                }
            }
        }

        return $dispatched;
    }

    /**
     * Notify Admins and Owner when a new room transfer request is submitted.
     */
    public function sendRoomTransferRequestToStaff(RoomTransfer $transfer): void
    {
        try {
            $transfer->loadMissing(['user', 'tenant', 'currentRoom', 'targetRoom']);

            $owners = User::where('role', 'owner')->whereNotNull('email')->get();
            $admins = User::where('role', 'admin')->whereNotNull('email')->get();

            $targetLoc = $transfer->targetRoom ? $transfer->targetRoom->location : null;
            $currentLoc = $transfer->currentRoom ? $transfer->currentRoom->location : null;

            // Send to Owners
            foreach ($owners as $owner) {
                Mail::to($owner->email)->send(new RoomTransferRequestMail($owner, $transfer));
                Log::info("Room transfer request email sent to owner: {$owner->email}");
            }

            // Send to Admins
            foreach ($admins as $admin) {
                $assignment = $admin->branch_assignment ?? 'all';
                $canReceive = ($assignment === 'all' || $assignment === $targetLoc || $assignment === $currentLoc);

                if ($canReceive) {
                    Mail::to($admin->email)->send(new RoomTransferRequestMail($admin, $transfer));
                    Log::info("Room transfer request email sent to admin: {$admin->email}");
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed sending room transfer request to staff: " . $e->getMessage());
        }
    }

    /**
     * Notify Tenant when room transfer request is approved or rejected.
     */
    public function sendRoomTransferDecisionToTenant(RoomTransfer $transfer): void
    {
        try {
            $transfer->loadMissing(['user', 'tenant', 'currentRoom', 'targetRoom']);
            $tenantUser = $transfer->user;

            if (!$tenantUser && $transfer->current_room_id) {
                $tenantUser = User::where('room_id', $transfer->current_room_id)->first();
            }

            if ($tenantUser && !empty($tenantUser->email)) {
                Mail::to($tenantUser->email)->send(new RoomTransferDecisionMail($tenantUser, $transfer));
                Log::info("Room transfer decision [{$transfer->status}] sent to tenant: {$tenantUser->email}");
            }
        } catch (\Exception $e) {
            Log::error("Failed sending room transfer decision to tenant: " . $e->getMessage());
        }
    }

    /**
     * Notify Admins and Owner when a new maintenance ticket is submitted.
     */
    public function sendMaintenanceRequestToStaff(MaintenanceTicket $ticket): void
    {
        try {
            $ticket->loadMissing('room');
            $room = $ticket->room;
            $roomLocation = $room ? $room->location : 'Lokasi 1';

            $owners = User::where('role', 'owner')->whereNotNull('email')->get();
            $admins = User::where('role', 'admin')->whereNotNull('email')->get();

            // Send to Owners
            foreach ($owners as $owner) {
                Mail::to($owner->email)->send(new MaintenanceRequestMail($owner, $ticket));
                Log::info("Maintenance request email sent to owner: {$owner->email} for Ticket {$ticket->ticket_code}");
            }

            // Send to Admins
            foreach ($admins as $admin) {
                $assignment = $admin->branch_assignment ?? 'all';
                $canReceive = ($assignment === 'all' || $assignment === $roomLocation);

                if ($canReceive) {
                    Mail::to($admin->email)->send(new MaintenanceRequestMail($admin, $ticket));
                    Log::info("Maintenance request email sent to admin: {$admin->email} for Ticket {$ticket->ticket_code}");
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed sending maintenance request to staff: " . $e->getMessage());
        }
    }

    /**
     * Notify Tenant when ticket status is progress or done.
     */
    public function sendMaintenanceProgressToTenant(MaintenanceTicket $ticket): void
    {
        try {
            $ticket->loadMissing('room');

            // Find tenant user
            $tenantUser = User::where('name', $ticket->reporter_name)->first();
            if (!$tenantUser && $ticket->room_id) {
                $tenantUser = User::where('room_id', $ticket->room_id)->first();
            }

            if ($tenantUser && !empty($tenantUser->email)) {
                Mail::to($tenantUser->email)->send(new MaintenanceProgressMail($tenantUser, $ticket));
                Log::info("Maintenance progress [{$ticket->status}] sent to tenant: {$tenantUser->email} for Ticket {$ticket->ticket_code}");
            }
        } catch (\Exception $e) {
            Log::error("Failed sending maintenance progress to tenant: " . $e->getMessage());
        }
    }

    /**
     * Broadcast WiFi updates to all active tenants.
     */
    public function sendWifiUpdateToTenants(array $wifiList): void
    {
        try {
            $tenantUsers = User::where('role', 'tenant')->whereNotNull('email')->get();

            foreach ($tenantUsers as $tenant) {
                Mail::to($tenant->email)->send(new WifiUpdateMail($tenant, $wifiList));
                Log::info("WiFi update sent to tenant: {$tenant->email}");
            }
        } catch (\Exception $e) {
            Log::error("Failed sending WiFi update to tenants: " . $e->getMessage());
        }
    }

    /**
     * Broadcast kos rules update to all active tenants.
     */
    public function sendRulesUpdateToTenants(array $rules): void
    {
        try {
            $tenantUsers = User::where('role', 'tenant')->whereNotNull('email')->get();

            foreach ($tenantUsers as $tenant) {
                Mail::to($tenant->email)->send(new RulesUpdateMail($tenant, $rules));
                Log::info("Rules update sent to tenant: {$tenant->email}");
            }
        } catch (\Exception $e) {
            Log::error("Failed sending rules update to tenants: " . $e->getMessage());
        }
    }

    /**
     * Send monthly consolidated financial report to Owner.
     */
    public function sendMonthlyFinancialReportToOwner(?int $month = null, ?int $year = null): array
    {
        $targetDate = Carbon::now();
        if ($month && $year) {
            $targetDate = Carbon::create($year, $month, 1);
        } else {
            // Default to previous month if on the 1st of month, or current month
            if ($targetDate->day === 1) {
                $targetDate->subMonth();
            }
        }

        $m = $targetDate->month;
        $y = $targetDate->year;
        $periodLabel = $targetDate->translatedFormat('F Y');

        $finances = Finance::whereYear('date', $y)
                           ->whereMonth('date', $m)
                           ->get();

        $income = (float) $finances->where('type', 'income')->sum('amount');
        $expense = (float) $finances->where('type', 'expense')->sum('amount');
        $netProfit = $income - $expense;
        $totalTransactions = $finances->count();

        // Okupansi
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) . '%' : '100%';

        $reportData = [
            'month' => $m,
            'year' => $y,
            'period_label' => $periodLabel,
            'total_income' => $income,
            'total_expense' => $expense,
            'net_profit' => $netProfit,
            'total_transactions' => $totalTransactions,
            'occupancy_rate' => $occupancyRate . ' (' . $occupiedRooms . '/' . $totalRooms . ' Kamar Terisi)',
        ];

        $owners = User::where('role', 'owner')->whereNotNull('email')->get();
        $sentCount = 0;

        foreach ($owners as $owner) {
            try {
                Mail::to($owner->email)->send(new MonthlyFinanceReportMail($owner, $reportData));
                $sentCount++;
                Log::info("Monthly financial report ({$periodLabel}) sent to owner: {$owner->email}");
            } catch (\Exception $e) {
                Log::error("Failed sending monthly financial report to owner {$owner->email}: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'owners_notified' => $sentCount,
            'report_data' => $reportData,
        ];
    }
}
