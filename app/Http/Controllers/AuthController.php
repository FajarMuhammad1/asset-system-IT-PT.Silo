<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function loginProses(Request $request)
    {
       $request -> validate([
        'email'     => 'required',      
        'password'  => 'required|min:8', 
       ],[
        'email.required' => 'Email tidak boleh kosong',
        'password.required' => 'Password tidak boleh kosong',
        'password.min' => 'Password Minimal 8 karakter deks !!!',
       ]);

       $data = array(
           'email'     => $request-> email, 
           'password'  => $request-> password, 
       );

        if  (Auth::attempt($data)){
            
            // --- INI KODE LAMA LO ---
            // return redirect()->route('dashboard')->with('success','Anda Berhasil Login Jer');

            // --- INI KODE BARUNYA (NGARAHIN SESUAI ROLE) ---
            
            $user = Auth::user(); // Ambil data user yang baru login
            $role = $user->role;  // Asumsi kolom role lo namanya 'role'

            // Cek rolenya
            switch ($role) {
                case 'SuperAdmin':
                case 'Admin':
                    // Mental ke dashboard admin
                    // Pastiin nama route lo 'admin.dashboard'
                    return redirect()->route('admin.dashboard')->with('success','Anda Berhasil Login, Jer');
                
                case 'Staff':
                    // Mental ke dashboard staff
                    // Pastiin nama route lo 'staff.dashboard'
                    return redirect()->route('staff.dashboard')->with('success','Anda Berhasil Login, Jer'); 
                
                case 'Pengguna':
                    // Mental ke dashboard pengguna
                    // Pastiin nama route lo 'pengguna.dashboard'
                    return redirect()->route('pengguna.dashboard')->with('success','Anda Berhasil Login, Jer');
                
                default:
                    // Kalo rolenya gak jelas, tendang keluar lagi
                    Auth::logout();
                    return redirect()->route('login')->with('error', 'Role Anda tidak dikenali.');
            }
            // --- BATAS KODE BARU ---

        } else{
            return redirect()->back()->with('error','Email atau Password Salah');
        }
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('login')->with('success','Anda Berhasil Log Out ');
    }
}