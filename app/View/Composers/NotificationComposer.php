<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Ppi;
use App\Models\Ticket;
use Illuminate\Support\Collection;

class NotificationComposer
{
    public function compose(View $view)
    {
        // default
        $notifications = collect();
        $notifCount = 0;

        // pastikan user login
        if (! Auth::check()) {
            $view->with('notifications', $notifications);
            $view->with('notifCount', $notifCount);
            return;
        }

        $user = Auth::user();
        $role = strtolower($user->role ?? '');

        $list = [];

        // --- LOGIKA UNTUK ADMIN (Tetap sama seperti sebelumnya) ---
        if (in_array($role, ['admin', 'super admin', 'superadmin'])) {
            
            // Ambil PPI pending
            $ppis = Ppi::with('user')
                ->where('status', 'pending')
                ->latest()
                ->get();

            // Ambil Helpdesk Open (Tiket Baru Masuk)
            $helpdesks = Ticket::with('pelapor')
                ->where('status', 'Open')
                ->latest()
                ->get();

            // Format notifikasi Admin
            foreach ($ppis as $p) {
                $list[] = [
                    'type'   => 'PPI',
                    'title'  => 'PPI Baru dari ' . ($p->user->name ?? 'User'),
                    'detail' => $p->perangkat ?? '-',
                    'time'   => $p->created_at,
                    'url'    => route('admin.ppi.show', ['id' => $p->id]), // Pastikan route admin PPI ada
                    'color'  => 'bg-primary'
                ];
            }

            foreach ($helpdesks as $t) {
                $list[] = [
                    'type'   => 'HELPDESK',
                    'title'  => 'Tiket Baru: ' . $t->no_tiket,
                    'detail' => $t->judul_masalah,
                    'time'   => $t->created_at,
                    'url'    => route('admin.helpdesk.show', ['id' => $t->id]), // Pastikan route admin tiket ada
                    'color'  => 'bg-warning'
                ];
            }

        } 
        
        // --- LOGIKA UNTUK PENGGUNA (User Biasa) ---
        elseif ($role == 'pengguna') {

            // 1. Ambil PPI milik user ini yang statusnya SUDAH DIPROSES/SELESAI
            // (Bukan 'pending', karena pending berarti belum diapa-apain admin)
            $ppis = Ppi::where('user_id', $user->id)
                ->where('status', '!=', 'pending') 
                ->latest('updated_at') // Urutkan berdasarkan update terakhir
                ->take(5)
                ->get();

            // 2. Ambil Tiket Helpdesk milik user ini yang SUDAH SELESAI (Closed)
            // Ini yang Anda minta: "notifikasi helpdesk sudah selesai"
            $helpdesks = Ticket::where('pelapor_id', $user->id)
                ->where('status', 'Closed') // Hanya ambil yang statusnya 'Closed'
                ->latest('updated_at') // Urutkan berdasarkan kapan diselesaikannya
                ->take(5)
                ->get();

            // Format notifikasi Pengguna
            foreach ($ppis as $p) {
                $statusLabel = ucfirst($p->status);
                $color = ($p->status == 'disetujui') ? 'bg-info' : (($p->status == 'ditolak') ? 'bg-danger' : 'bg-success');

                $list[] = [
                    'type'   => 'PPI',
                    'title'  => "PPI {$p->no_ppi}: {$statusLabel}",
                    'detail' => $p->perangkat,
                    'time'   => $p->updated_at, // Gunakan updated_at
                    'url'    => route('pengguna.ppi.index'), // Arahkan ke list riwayat
                    'color'  => $color
                ];
            }

            foreach ($helpdesks as $t) {
                $list[] = [
                    'type'   => 'HELPDESK',
                    'title'  => "Tiket {$t->no_tiket} Telah Selesai",
                    'detail' => $t->judul_masalah,
                    'time'   => $t->updated_at, // Gunakan updated_at (waktu penyelesaian)
                    'url'    => route('pengguna.helpdesk.show', ['id' => $t->id]), // Lihat detail penyelesaian
                    'color'  => 'bg-success' // Hijau untuk sukses
                ];
            }
        }

        // --- GABUNGKAN DAN URUTKAN ---
        
        // Convert ke collection, sort by time desc, ambil 5 teratas
        $notifications = collect($list)
            ->sortByDesc(function ($item) {
                return $item['time'] instanceof \DateTime ? $item['time']->getTimestamp() : strtotime($item['time']);
            })
            ->values()
            ->take(5);

        $notifCount = $notifications->count();

        // Kirim ke view
        $view->with('notifications', $notifications);
        $view->with('notifCount', $notifCount);
    }
}