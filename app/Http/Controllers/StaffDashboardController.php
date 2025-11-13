<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class StaffDashboardController extends Controller
{
    public function index()
    {
        // Arahin ke folder staff file dashboard
        return view('staff.dashboard');
    }
}