<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // <--- INI WAJIB DITAMBAHKAN

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


// --- JADWAL MAINTENANCE OTOMATIS ---
// Sistem akan mengecek jadwal maintenance setiap jam 00:00 (tengah malam)
Schedule::command('maintenance:generate')->daily();