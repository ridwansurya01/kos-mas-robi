<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Room;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StaffRentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $staff;
    public $tenantUser;
    public $room;
    public $setting;
    public $reminderType; // 'h-3', 'h-1', 'h-0'
    public $reminderTitle;
    public $dueDateFormatted;
    public $amount;

    public function __construct(User $staff, ?User $tenantUser, Room $room, string $reminderType, string $dueDateFormatted, float $amount)
    {
        $this->staff = $staff;
        $this->tenantUser = $tenantUser;
        $this->room = $room;
        $this->setting = Setting::first();
        $this->reminderType = $reminderType;
        $this->dueDateFormatted = $dueDateFormatted;
        $this->amount = $amount;

        $tenantName = $tenantUser ? $tenantUser->name : 'Penghuni #' . $room->number;

        $titles = [
            'h-3' => 'Pemberitahuan Staf (H-3 Hari): Jadwal Tagihan Sewa Kamar #' . $room->number . ' (' . $tenantName . ')',
            'h-1' => 'Pemberitahuan Staf (H-1 Hari): Besok Jatuh Tempo Kamar #' . $room->number . ' (' . $tenantName . ')',
            'h-0' => 'Pemberitahuan Staf (Hari H): Batas Pembayaran Hari Ini Kamar #' . $room->number . ' (' . $tenantName . ')',
        ];

        $this->reminderTitle = $titles[$reminderType] ?? ('Pemberitahuan Tagihan Sewa Kamar #' . $room->number);
    }

    public function build()
    {
        return $this->subject('[' . ($this->setting->kos_name ?? 'Kos Mas Robi') . '] ' . $this->reminderTitle)
                    ->view('emails.staff_rent_reminder');
    }
}
