<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TicketController extends Controller
{
    /**
     * Halaman List Tiket Saya
     */
    public function index()
    {
        // Ambil tiket milik user yang login
        $tickets = Ticket::where('pelapor_id', Auth::id())
                         ->latest()
                         ->get();

        return view('pengguna.helpdesk.index', [
            'title' => 'Tiket Bantuan Saya',
            'tickets' => $tickets
        ]);
    }

    /**
     * Form Lapor Kerusakan
     */
    public function create()
    {
        // Migration sudah TIDAK pakai barang_masuk_id
        return view('pengguna.helpdesk.create', [
            'title' => 'Buat Tiket Baru'
        ]);
    }

    /**
     * Proses Simpan Tiket
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul_masalah' => 'required|string|max:255',
            'deskripsi' => 'required',
            'foto_masalah' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // 1. Generate No Tiket (TIK-202511-001)
        $now = Carbon::now();
        $bulanTahun = $now->format('Ym');

        $cek = Ticket::where('no_tiket', 'like', "TIK-{$bulanTahun}-%")->count();
        $urut = $cek + 1;
        $noTiket = "TIK-{$bulanTahun}-" . sprintf("%03s", $urut);

        // 2. Upload Foto (Jika ada)
        $fotoPath = null;
        if ($request->hasFile('foto_masalah')) {
            $fotoPath = $request->file('foto_masalah')->store('helpdesk_uploads', 'public');
        }

        // 3. Simpan Tiket
        Ticket::create([
            'no_tiket' => $noTiket,
            'pelapor_id' => Auth::id(),
            'judul_masalah' => $request->judul_masalah,
            'deskripsi' => $request->deskripsi,
            'prioritas' => 'Normal',     // Sesuai migration
            'foto_masalah' => $fotoPath,
            'status' => 'Open',          // Default di migration
            'teknisi_id' => null,        // default: belum di-assign
        ]);

        return redirect()->route('pengguna.helpdesk.index')
                         ->with('success', 'Laporan berhasil dikirim! Nomor Tiket: ' . $noTiket);
    }

    /**
     * Lihat Detail Tiket
     */
    public function show($id)
    {
        // Pastikan cuma bisa lihat tiket sendiri
        $ticket = Ticket::where('pelapor_id', Auth::id())->findOrFail($id);

        return view('pengguna.helpdesk.show', [
            'title' => 'Detail Tiket ' . $ticket->no_tiket,
            'ticket' => $ticket
        ]);
    }
}
