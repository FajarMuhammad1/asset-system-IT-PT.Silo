<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class TeamController extends Controller
{
    public function index()
    {
       $team = User::whereIn('role', ['SuperAdmin', 'Admin', 'Staff'])->get();
       return view('admin.team.index', compact('team'))->with('title', 'Team IT');
    }

    //FUNGSI MENAMBAH DATA
    public function create()
    {
        return view('admin.team.create', [
            'title' => 'Tambah Team'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik'      => 'required|unique:users',
            'nama'     => 'required',
            'email'    => 'required|email|unique:users',
            'no_hp'    => 'nullable|string|max:20', // ✅ [BARU] Validasi no_hp
            'jabatan'  => 'required',
            'role'     => 'required',
            'status'   => 'required',
            'password' => 'required|min:5|confirmed',
        ]);

        User::create([
            'nik'      => $request->nik,
            'nama'     => $request->nama,
            'email'    => $request->email,
            'no_hp'    => $request->no_hp, // ✅ [BARU] Simpan no_hp ke database
            'jabatan'  => $request->jabatan,
            'role'     => $request->role,
            'status'   => $request->status,
            'password' => Hash::make($request->password), 
        ]);

        return redirect()->route('team')->with('success', 'Team berhasil ditambahkan');
    }
    //END FUNGSI MENAMBAH DATA


    //FUNGSI EDIT DATA
    public function edit($id)
    {
        $team = User::findOrFail($id);
        return view('admin.team.edit', [
            'title' => 'Edit Data Team',
            'team'  => $team
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nik'     => 'required|unique:users,nik,' . $id,
            'nama'    => 'required',
            'email'   => 'required|email|unique:users,email,' . $id,
            'no_hp'   => 'nullable|string|max:20', // ✅ [BARU] Validasi no_hp saat update
            'jabatan' => 'required',
            'role'    => 'required',
            'status'  => 'required',
        ]);

        $team = User::findOrFail($id);

        $team->update([
            'nik'     => $request->nik,
            'nama'    => $request->nama,
            'email'   => $request->email,
            'no_hp'   => $request->no_hp, // ✅ [BARU] Update data no_hp
            'jabatan' => $request->jabatan,
            'role'    => $request->role,
            'status'  => $request->status,
        ]);

        return redirect()->route('team')->with('success', 'Data Team berhasil diupdate');
    }
    //END FUNGSI EDIT DATA


    //FUNGSI HAPUS DATA
    public function destroy($id)
    {
        $team = User::findOrFail($id);
        $team->delete();

        return redirect()->route('team')->with('success', 'Data berhasil dihapus.');
    }
    //END FUNGSI HAPUS DATA
}