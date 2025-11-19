<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Tampilkan Halaman Profile
     */
    public function edit()
    {
        return view('settings.profile', [
            'title' => 'Pengaturan Profil',
            'user' => Auth::user()
        ]);
    }

    /**
     * Update Informasi Dasar (Nama & Email)
     */
    public function updateProfile(Request $request)
    {
        $user = User::find(Auth::id());

        $request->validate([
            'name' => 'required|string|max:255',
            // Email harus unik, TAPI kecualikan ID user yang sedang login biar gak error "Email sudah dipakai"
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update Password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed', // 'confirmed' butuh input name="password_confirmation"
        ]);

        $user = User::find(Auth::id());

        // 1. Cek apakah password lama bener?
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah!']);
        }

        // 2. Update password baru
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password berhasil diganti!');
    }
}