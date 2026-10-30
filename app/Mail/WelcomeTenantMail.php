<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeTenantMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $setting;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->setting = Setting::first();
    }

    public function build()
    {
        return $this->subject('Selamat Datang di ' . ($this->setting->kos_name ?? 'Kos Mas Robi') . ' — Akun Berhasil Terdaftar')
                    ->view('emails.welcome_tenant');
    }
}

