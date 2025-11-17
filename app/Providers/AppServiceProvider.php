<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // <-- 1. IMPORT VIEW
use App\View\Composers\NotificationComposer; // <-- 2. IMPORT COMPOSER KITA

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
        // <-- 3. TAMBAHIN KODE INI -->
        // 'layouts.app' adalah nama file layout utama lo.
        // Kalo nama file layout lo 'layouts.main', ganti jadi 'layouts.main'
        // Ini bilang: "Setiap kali layout 'app' dipanggil, panggil NotificationComposer"
        View::composer('layouts.app', NotificationComposer::class);
    }
}