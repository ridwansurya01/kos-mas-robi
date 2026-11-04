<?php

namespace App\Mail;

use App\Models\User;
use App\Models\MaintenanceTicket;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MaintenanceRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $recipient;
    public $ticket;
    public $setting;

    public function __construct(User $recipient, MaintenanceTicket $ticket)
    {
        $this->recipient = $recipient;
        $this->ticket = $ticket;
        $this->setting = Setting::first();
    }

    public function build()
    {
        $roomNumber = $this->ticket->room ? $this->ticket->room->number : '-';
        $priorityLabel = strtoupper($this->ticket->priority ?? 'NORMAL');

        return $this->subject('[' . ($this->setting->kos_name ?? 'Kos Mas Robi') . '] Laporan Keluhan Servis Baru: ' . $this->ticket->ticket_code . ' (Kamar #' . $roomNumber . ' - ' . $priorityLabel . ')')
                    ->view('emails.maintenance_request');
    }
}
