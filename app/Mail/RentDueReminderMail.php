<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Room;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RentDueReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $room;
    public $setting;
    public $reminderType; // 'h-14', 'h-7', 'h-3', 'h-1', 'h-0'
    public $reminderTitle;
    public $dueDateFormatted;
    public $amount;

    public function __construct(User $user, Room $room, string $reminderType, string $dueDateFormatted, float $amount)
    {
        $this->user = $user;
        $this->room = $room;
        $this->setting = Setting::first();
        $this->reminderType = $reminderType;
        $this->dueDateFormatted = $dueDateFormatted;
        $this->amount = $amount;

        $titles = [
            'h-14' => 'Pengingat Sewa (H-2 Minggu): Persiapan Tagihan Kamar #' . $room->number,
            'h-7'  => 'Pengingat Sewa (H-1 Minggu): Jatuh Tempo Pembayaran Kamar #' . $room->number,
            'h-3'  => 'PENTING (H-3 Hari): 3 Hari Menuju Jatuh Tempo Sewa Kamar #' . $room->number,
            'h-1'  => 'PENTING (H-1 Hari): Besok Jatuh Tempo Pembayaran Sewa Kamar #' . $room->number,
            'h-0'  => 'HARI INI (Hari H): Batas Pembayaran Sewa Kamar #' . $room->number,
        ];

        $this->reminderTitle = $titles[$reminderType] ?? ('Pengingat Pembayaran Sewa Kamar #' . $room->number);
    }

    public function build()
    {
        return $this->subject('🔔 [' . ($this->setting->kos_name ?? 'Kos Mas Robi') . '] ' . $this->reminderTitle)
                    ->view('emails.rent_reminder');
    }
}

