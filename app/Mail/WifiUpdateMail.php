<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WifiUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tenantUser;
    public $wifiList;
    public $setting;

    public function __construct(User $tenantUser, array $wifiList)
    {
        $this->tenantUser = $tenantUser;
        $this->wifiList = $wifiList;
        $this->setting = Setting::first();
    }

    public function build()
    {
        return $this->subject('[' . ($this->setting->kos_name ?? 'Kos Mas Robi') . '] Pembaruan Informasi Jaringan WiFi Kos')
                    ->view('emails.wifi_update');
    }
}
