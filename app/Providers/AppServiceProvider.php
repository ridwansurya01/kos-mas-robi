<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Dinamis konfigurasi SMTP Gmail dari database (jika tersedia)
        try {
            $setting = rescue(fn() => Setting::first(), null, false);
            if ($setting && !empty($setting->email_settings)) {
                $es = $setting->email_settings;
                if (!empty($es['mail_username']) && !empty($es['mail_password'])) {
                    Config::set('mail.default', $es['mail_mailer'] ?? 'smtp');
                    Config::set('mail.mailers.smtp.transport', 'smtp');
                    Config::set('mail.mailers.smtp.host', $es['mail_host'] ?? 'smtp.gmail.com');
                    Config::set('mail.mailers.smtp.port', (int) ($es['mail_port'] ?? 587));
                    Config::set('mail.mailers.smtp.encryption', $es['mail_encryption'] ?? 'tls');
                    Config::set('mail.mailers.smtp.username', $es['mail_username']);
                    Config::set('mail.mailers.smtp.password', $es['mail_password']);
                    Config::set('mail.from.address', $es['mail_from_address'] ?? $es['mail_username']);
                    Config::set('mail.from.name', $es['mail_from_name'] ?? ($setting->kos_name ?? 'Kos Mas Robi'));
                }
            }
        } catch (\Throwable $e) {
            // Abaikan kegagalan DB saat CLI build / migrate awal
        }
    }
}
