<?php

namespace App\Mail;

use App\Models\User;
use App\Models\MaintenanceTicket;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MaintenanceProgressMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tenantUser;
    public $ticket;
    public $setting;

    public function __construct(User $tenantUser, MaintenanceTicket $ticket)
    {
        $this->tenantUser = $tenantUser;
        $this->ticket = $ticket;
        $this->setting = Setting::first();
    }

    public function build()
    {
        $statusText = ($this->ticket->status === 'done') ? 'Telah Selesai Ditangani' : 'Telah Dilaksanakan / Sedang Dikerjakan';
        $roomNumber = $this->ticket->room ? $this->ticket->room->number : '-';

        return $this->subject('[' . ($this->setting->kos_name ?? 'Kos Mas Robi') . '] Update Keluhan Servis ' . $this->ticket->ticket_code . ' (Kamar #' . $roomNumber . '): ' . $statusText)
                    ->view('emails.maintenance_progress');
    }
}
