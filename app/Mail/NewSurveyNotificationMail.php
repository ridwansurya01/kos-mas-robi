<?php

namespace App\Mail;

use App\Models\User;
use App\Models\SurveyRequest;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewSurveyNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $recipient;
    public $survey;
    public $setting;

    public function __construct(User $recipient, SurveyRequest $survey)
    {
        $this->recipient = $recipient;
        $this->survey = $survey;
        $this->setting = Setting::first();
    }

    public function build()
    {
        $kosName = $this->setting->kos_name ?? 'Kos Mas Robi';
        $calonName = $this->survey->name ?? 'Calon Penghuni';
        $branch = $this->survey->branch ?? 'Lokasi 1';

        return $this->subject("[{$kosName}] Permohonan Survey Baru: {$calonName} ({$branch})")
                    ->view('emails.new_survey_notification');
    }
}

