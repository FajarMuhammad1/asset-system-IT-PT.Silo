<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function index()
    {
        $penggunas = User::where('role', 'Pengguna')->get();
        return view('admin.pengguna.index', compact('penggunas'))
               ->with('title', 'Pengguna');
    }

    public function create()
    {
        return view('admin.pengguna.create')
               ->with('title', 'Tambah Pengguna');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik'        => 'required|string|max:50|unique:users,nik',
            'nama'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'jabatan'    => 'required|string|max:255',
            'departemen' => 'required|string|max:255',
            'perusahaan' => 'nullable|string|max:255',
            'password'   => 'required|min:6|confirmed',
        ]);

        User::create([
            'nik'        => $request->nik,
            'nama'       => $request->nama,
            'email'      => $request->email,
            'jabatan'    => $request->jabatan,
            'departemen' => $request->departemen,
            'perusahaan' => $request->perusahaan,
            'password'   => Hash::make($request->password),
            'role'       => 'Pengguna',
        ]);

        return redirect()->route('pengguna.index')
                         ->with('success', 'Akun Pengguna berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $pengguna = User::findOrFail($id);
        return view('admin.pengguna.edit', compact('pengguna'))
               ->with('title', 'Edit Pengguna');
    }

    public function update(Request $request, $id)
    {
        $pengguna = User::findOrFail($id);

        $request->validate([
            'nik'        => 'required|string|max:50|unique:users,nik,' . $pengguna->id,
            'nama'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $pengguna->id,
            'jabatan'    => 'required|string|max:255',
            'departemen' => 'required|string|max:255',
            'perusahaan' => 'nullable|string|max:255',
            'password'   => 'nullable|min:6|confirmed',
        ]);

        $pengguna->update([
            'nik'        => $request->nik,
            'nama'       => $request->nama,
            'email'      => $request->email,
            'jabatan'    => $request->jabatan,
            'departemen' => $request->departemen,
            'perusahaan' => $request->perusahaan,
            'password'   => $request->password ? Hash::make($request->password) : $pengguna->password,
        ]);

        return redirect()->route('pengguna.index')
                         ->with('success', 'Data Pengguna berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pengguna = User::findOrFail($id);
        $pengguna->delete();

        return redirect()->route('pengguna.index')
                         ->with('success', 'Data Pengguna berhasil dihapus!');
    }
}
