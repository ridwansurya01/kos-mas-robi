<?php

namespace App\Mail;

use App\Models\User;
use App\Models\RoomTransfer;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RoomTransferDecisionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tenantUser;
    public $transfer;
    public $setting;

    public function __construct(User $tenantUser, RoomTransfer $transfer)
    {
        $this->tenantUser = $tenantUser;
        $this->transfer = $transfer;
        $this->setting = Setting::first();
    }

    public function build()
    {
        $isApproved = ($this->transfer->status === 'approved');
        $statusText = $isApproved ? 'Disetujui' : 'Ditolak';
        $targetRoom = $this->transfer->targetRoom ? 'Kamar #' . $this->transfer->targetRoom->number : 'Kamar Tujuan';

        return $this->subject('[' . ($this->setting->kos_name ?? 'Kos Mas Robi') . '] Status Permohonan Pindah ' . $targetRoom . ': ' . $statusText)
                    ->view('emails.room_transfer_decision');
    }
}
