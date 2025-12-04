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

        // hanya untuk admin / superadmin
        if (! in_array($role, ['admin', 'super admin', 'superadmin'])) {
            $view->with('notifications', $notifications);
            $view->with('notifCount', $notifCount);
            return;
        }

        // ambil ppi pending (model -> biasa)
        $ppis = Ppi::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get();

        // ambil helpdesk open
        $helpdesks = Ticket::with('pelapor')
            ->where('status', 'Open')
            ->latest()
            ->get();

        // bangun array plain notifications (hindari memasukkan model langsung)
        $list = [];

        foreach ($ppis as $p) {
            $list[] = [
                'type'   => 'PPI',
                'title'  => 'PPI Baru dari ' . ($p->user->name ?? 'User'),
                'detail' => $p->perangkat ?? '-',
                'time'   => $p->created_at,
                // pakai route dengan parameter array (id) agar tidak memaksa model
                'url'    => route('admin.ppi.show', ['id' => $p->id]),
            ];
        }

        foreach ($helpdesks as $t) {
            $list[] = [
                'type'   => 'HELPDESK',
                'title'  => 'Tiket Baru: ' . $t->judul_masalah,
                'detail' => $t->pelapor->nama ?? '-',
                'time'   => $t->created_at,
                'url'    => route('admin.helpdesk.show', ['id' => $t->id]),
            ];
        }

        // convert ke collection, sort by time desc, ambil 5
        $notifications = collect($list)
                            ->sortByDesc(function ($item) {
                                return $item['time'] instanceof \DateTime ? $item['time']->getTimestamp() : strtotime($item['time']);
                            })
                            ->values()
                            ->take(5);

        $notifCount = $notifications->count();

        // kirim ke view
        $view->with('notifications', $notifications);
        $view->with('notifCount', $notifCount);
    }
}
