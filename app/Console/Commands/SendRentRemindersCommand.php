<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailNotificationService;

class SendRentRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'kos:send-rent-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Kirim pengingat tagihan sewa ke Gmail Penghuni (H-7, H-3, H-1, Hari H) dan Staff Pengelola (H-3, H-1, Hari H)';

    /**
     * Execute the console command.
     */
    public function handle(EmailNotificationService $service)
    {
        $this->info('Memulai pemindaian jatuh tempo sewa dan pengiriman email pengingat...');

        $dispatched = $service->sendRentReminders();

        $count = count($dispatched);
        $this->info("Proses selesai: {$count} notifikasi email terkirim.");

        if ($count > 0) {
            foreach ($dispatched as $d) {
                $roleLabel = strtoupper($d['recipient_type']);
                $this->line("- [{$d['reminder']} | {$roleLabel}] {$d['name']} ({$d['email']}) - Kamar #{$d['room']}");
            }
        } else {
            $this->comment('Tidak ada tagihan pada jadwal H-7, H-3, H-1, atau Hari H untuk hari ini.');
        }

        return 0;
    }
}
