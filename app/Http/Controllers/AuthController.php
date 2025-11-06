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
        
        'email'    => $request-> email, 
        'password'    => $request-> password, 
    
    );

     if  (Auth::attempt($data)){
        
        return redirect()->route('dashboard')->with('success','Anda Berhasil Login Jer');

     } else{
        return redirect()->back()->with('error','Email atau Password Salah');
     }

    }

    public function logout(){
        Auth::logout();

        return redirect()->route('login')->with('success','Anda Berhasil Log Out ');
    }
    
}
