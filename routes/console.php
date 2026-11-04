<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Kirim pengingat sewa otomatis harian (H-7, H-3, H-1, Hari H) pada pukul 08:00
Schedule::command('kos:send-rent-reminders')->dailyAt('08:00');

// Kirim laporan keuangan konsolidasi bulanan ke Owner setiap tanggal 1 pada pukul 08:00
Schedule::command('kos:send-monthly-financial-report')->monthlyOn(1, '08:00');
