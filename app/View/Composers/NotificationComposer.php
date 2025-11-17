<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Ppi; // Panggil Model PPI
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        // Kita cuma mau nampilin notif ini ke Admin
        // Pake strtolower biar aman
        $user = Auth::user();
        if (Auth::check() && in_array(strtolower($user->role), ['admin', 'super admin'])) {
            
            // 1. Ambil 5 PPI terbaru yang statusnya 'pending'
            $pendingPpis = Ppi::with('user') // Ambil data user-nya sekalian
                              ->where('status', 'pending')
                              ->latest() // Urutkan dari yg terbaru
                              ->take(5) // Ambil 5 aja
                              ->get();
            
            // 2. Hitung TOTAL PPI yang pending (buat angka di lonceng)
            $pendingPpiCount = Ppi::where('status', 'pending')->count();

            // 3. Kirim data ini ke View
            $view->with('pendingPpiCount', $pendingPpiCount);
            $view->with('recentPendingPpis', $pendingPpis);
        
        } else {
            
            // Kalo yg login bukan Admin, kasih data kosong
            $view->with('pendingPpiCount', 0);
            $view->with('recentPendingPpis', collect()); // Kasih koleksi kosong
        }
    }
}