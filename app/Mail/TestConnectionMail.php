<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestConnectionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $setting;
    public $senderEmail;
    public $targetEmail;

    public function __construct(string $senderEmail, string $targetEmail)
    {
        $this->setting = Setting::first();
        $this->senderEmail = $senderEmail;
        $this->targetEmail = $targetEmail;
    }

    public function build()
    {
        $kosName = $this->setting->kos_name ?? 'Kos Mas Robi';

        return $this->subject("[{$kosName}] Uji Coba Koneksi Gmail SMTP Berhasil")
                    ->view('emails.test_connection');
    }
}

