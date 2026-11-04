<?php

namespace App\Mail;

use App\Models\User;
use App\Models\RoomTransfer;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RoomTransferRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $recipient;
    public $transfer;
    public $setting;

    public function __construct(User $recipient, RoomTransfer $transfer)
    {
        $this->recipient = $recipient;
        $this->transfer = $transfer;
        $this->setting = Setting::first();
    }

    public function build()
    {
        $tenantName = $this->transfer->user ? $this->transfer->user->name : ($this->transfer->tenant ? $this->transfer->tenant->name : 'Penghuni');
        $fromRoom = $this->transfer->currentRoom ? 'Kamar #' . $this->transfer->currentRoom->number : 'Kamar Saat Ini';
        $toRoom = $this->transfer->targetRoom ? 'Kamar #' . $this->transfer->targetRoom->number : 'Kamar Baru';

        return $this->subject('[' . ($this->setting->kos_name ?? 'Kos Mas Robi') . '] Permohonan Pindah Kamar Baru: ' . $tenantName . ' (' . $fromRoom . ' ke ' . $toRoom . ')')
                    ->view('emails.room_transfer_request');
    }
}
