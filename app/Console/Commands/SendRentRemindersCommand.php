<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\EmailReminderController;

class SendRentRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'kos:send-rent-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Kirim pengingat pembayaran sewa kos otomatis ke Gmail pelanggan (H-14, H-7, H-3, H-1, Hari H)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pemindaian jatuh tempo sewa dan pengiriman email pengingat...');
        $controller = new EmailReminderController();
        $response = $controller->dispatchReminders();
        $data = $response->getData(true);

        $this->info($data['message'] ?? 'Selesai');
        if (!empty($data['dispatched'])) {
            foreach ($data['dispatched'] as $d) {
                $this->line("- [{$d['reminder']}] {$d['user']} ({$d['email']}) Kamar #{$d['room']} (Jatuh tempo: {$d['due_date']})");
            }
        } else {
            $this->comment('Tidak ada tagihan yang pas di H-14, H-7, H-3, H-1, atau Hari H untuk hari ini.');
        }

        return 0;
    }
}

