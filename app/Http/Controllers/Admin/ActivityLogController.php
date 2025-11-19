<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity; // <-- Panggil model log-nya

class ActivityLogController extends Controller
{
    public function index()
    {
        // Ambil 100 log terbaru, sekalian ambil data 'causer' (siapa yg ngelakuin)
        $activities = Activity::with('causer') 
                              ->latest()
                              ->paginate(50); // Ambil 50 per halaman

        return view('admin.activity_log.index', [
            'title' => 'System Activity Log',
            'activities' => $activities
        ]);
    }
}