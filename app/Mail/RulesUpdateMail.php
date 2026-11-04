<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RulesUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tenantUser;
    public $rules;
    public $setting;

    public function __construct(User $tenantUser, array $rules)
    {
        $this->tenantUser = $tenantUser;
        $this->rules = $rules;
        $this->setting = Setting::first();
    }

    public function build()
    {
        return $this->subject('[' . ($this->setting->kos_name ?? 'Kos Mas Robi') . '] Pembaruan Tata Tertib & Peraturan Hunian Kos')
                    ->view('emails.rules_update');
    }
}
