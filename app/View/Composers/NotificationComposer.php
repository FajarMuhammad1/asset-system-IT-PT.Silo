<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Ppi;
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        // Default values (biar gak error undefined variable)
        $notifCount = 0;
        $notifItems = collect();
        $notifLabel = 'Notifikasi';

        if (Auth::check()) {
            $user = Auth::user();
            $role = strtolower($user->role);

            // --- LOGIKA 1: ADMIN (Melihat Request Baru) ---
            if (in_array($role, ['admin', 'super admin', 'superadmin'])) {
                
                // Ambil PPI yang masih Pending
                $notifItems = Ppi::with('user')
                                 ->where('status', 'pending')
                                 ->latest()
                                 ->take(5)
                                 ->get();
                
                $notifCount = Ppi::where('status', 'pending')->count();
                $notifLabel = 'Permintaan Masuk (Pending)';
            } 
            
            // --- LOGIKA 2: PENGGUNA (Melihat Status Update) ---
            elseif ($role == 'pengguna') {
                
                // Ambil PPI milik user ini yang statusnya SUDAH DIPROSES (Bukan Pending)
                // Kita urutkan berdasarkan 'updated_at' (kapan terakhir diubah admin)
                $notifItems = Ppi::where('user_id', $user->id)
                                 ->where('status', '!=', 'pending') 
                                 ->latest('updated_at') 
                                 ->take(5)
                                 ->get();
                
                // Hitung jumlah notifikasi (bisa disesuaikan logikanya, misal yg belum dibaca)
                // Disini kita hitung total yg sudah diproses sebagai notifikasi
                $notifCount = $notifItems->count();
                $notifLabel = 'Status Pengajuan Anda';
            }
        }

        // Kirim data ke View dengan nama variabel yang seragam
        // (Pastikan di layouts/app.blade.php menggunakan variabel ini)
        $view->with('notifCount', $notifCount);
        $view->with('notifItems', $notifItems);
        $view->with('notifLabel', $notifLabel);
        
        // Fallback untuk variabel lama (jika layout belum diupdate total)
        $view->with('pendingPpiCount', $notifCount);
        $view->with('recentPendingPpis', $notifItems);
    }
}