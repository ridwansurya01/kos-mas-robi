<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\EmailNotificationService;

class EmailReminderController extends Controller
{
    protected EmailNotificationService $notificationService;

    public function __construct(EmailNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Scan all active tenants and dispatch due reminders to their Gmail and Staff.
     * Triggers:
     * - Penghuni: H-7, H-3, H-1, Hari H (0)
     * - Staff Admin & Owner: H-3, H-1, Hari H (0)
     */
    public function dispatchReminders(): JsonResponse
    {
        $dispatched = $this->notificationService->sendRentReminders();

        return response()->json([
            'success' => true,
            'message' => count($dispatched) . ' notifikasi pengingat sewa berhasil diproses & dikirim.',
            'dispatched_count' => count($dispatched),
            'dispatched' => $dispatched
        ]);
    }
}
