<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class PenggunaDashboardController extends Controller
{
    public function index()
    {
        $data = array(
            'title' => 'Dashboard Pengguna',
            'menuDashboardPengguna' => 'active',

        );
        // Arahin ke folder pengguna file dashboard
        return view('pengguna.dashboard',$data);
    }
}