<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class checkLogin
{
    /**
     * Handle an incoming request.
     * * @param ...$roles  <-- Ini biar bisa nangkep parameter role (admin, staff, dll)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek Login dulu
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Login dulu bos!');
        }

        // 2. Ambil Role User
        $user = Auth::user();
        
        // 3. Cek apakah role user ada di daftar role yang dibolehkan
        // Kita pake in_array buat ngecek
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // 4. Kalo role gak cocok, tendang!
        // Lo bisa ganti ini jadi abort(403) biar lebih aman, atau redirect balik.
        return redirect()->route('login')->with('error', 'Anda tidak punya akses ke halaman tersebut!'); 
    }
}