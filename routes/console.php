<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ========================================================
// STANDAR KEAMANAN AREA 1: JADWAL PENCADANGAN OTOMATIS
// ========================================================
// Backup Harian Pkl 00:00 WIB
Schedule::command('backup:database --frequency=daily')
    ->dailyAt('00:00')
    ->name('sikapri_backup_daily')
    ->withoutOverlapping();

// Backup Mingguan Setiap Minggu Pkl 01:00 WIB
Schedule::command('backup:database --frequency=weekly')
    ->weeklyOn(0, '01:00')
    ->name('sikapri_backup_weekly')
    ->withoutOverlapping();

// Backup Bulanan Setiap Awal Bulan Pkl 02:00 WIB
Schedule::command('backup:database --frequency=monthly')
    ->monthlyOn(1, '02:00')
    ->name('sikapri_backup_monthly')
    ->withoutOverlapping();
