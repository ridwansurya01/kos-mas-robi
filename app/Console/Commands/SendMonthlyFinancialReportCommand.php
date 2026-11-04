<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailNotificationService;

class SendMonthlyFinancialReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'kos:send-monthly-financial-report {--month= : Bulan laporan (1-12)} {--year= : Tahun laporan (contoh: 2026)}';

    /**
     * The console command description.
     */
    protected $description = 'Kirim laporan ringkasan keuangan bulanan konsolidasi ke Gmail Owner';

    /**
     * Execute the console command.
     */
    public function handle(EmailNotificationService $service)
    {
        $this->info('Menyiapkan dan mengirim laporan keuangan bulanan ke Owner...');

        $month = $this->option('month') ? (int) $this->option('month') : null;
        $year = $this->option('year') ? (int) $this->option('year') : null;

        $result = $service->sendMonthlyFinancialReportToOwner($month, $year);

        $report = $result['report_data'] ?? [];
        $this->info('Laporan periode ' . ($report['period_label'] ?? '-') . ' berhasil dikirim ke ' . ($result['owners_notified'] ?? 0) . ' Owner.');
        $this->line('- Pemasukan: Rp ' . number_format($report['total_income'] ?? 0, 0, ',', '.'));
        $this->line('- Pengeluaran: Rp ' . number_format($report['total_expense'] ?? 0, 0, ',', '.'));
        $this->line('- Laba Bersih: Rp ' . number_format($report['net_profit'] ?? 0, 0, ',', '.'));
        $this->line('- Okupansi: ' . ($report['occupancy_rate'] ?? '-'));

        return 0;
    }
}
