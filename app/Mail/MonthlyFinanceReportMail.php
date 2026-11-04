<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MonthlyFinanceReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $owner;
    public $reportData;
    public $setting;

    public function __construct(User $owner, array $reportData)
    {
        $this->owner = $owner;
        $this->reportData = $reportData;
        $this->setting = Setting::first();
    }

    public function build()
    {
        $periodLabel = $this->reportData['period_label'] ?? date('F Y');

        return $this->subject('[' . ($this->setting->kos_name ?? 'Kos Mas Robi') . '] Laporan Keuangan Bulanan Konsolidasi (' . $periodLabel . ')')
                    ->view('emails.monthly_finance_report');
    }
}
