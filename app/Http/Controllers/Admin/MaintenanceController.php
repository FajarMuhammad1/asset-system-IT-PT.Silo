<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSchedule;
use App\Models\PerawatanBarang;
use App\Models\BarangMasuk;
use App\Models\User; // [BARU] Import model User untuk mengambil data staff
use App\Notifications\MaintenanceReminderNotification; // [BARU] Import class notifikasi
use Illuminate\Support\Facades\Notification; // [BARU] Import facade notifikasi Laravel
use Illuminate\Http\Request;
use Carbon\Carbon;

class MaintenanceController extends Controller
{
    // 1. Menampilkan Master Jadwal dan Daftar Tugas Perawatan Alat
    public function index()
    {
        $user = auth()->user();

        // PEMISAHAN LOGIKA TAMPILAN & DATA BERDASARKAN ROLE
        if ($user->role === 'Admin') {
            // -- KHUSUS ADMIN --
            // Ambal data barang untuk form dropdown jadwal
            $barangs = BarangMasuk::with('masterBarang')
                ->whereIn('status', ['Stok', 'Dipakai', 'Digunakan'])
                ->get();

            // Tarik semua Master Jadwal dan semua Tugas Perawatan
            $schedules = MaintenanceSchedule::with('barangMasuk.masterBarang')->latest()->get();
            $tugasPerawatan = PerawatanBarang::with(['barangMasuk.masterBarang', 'teknisi'])->latest()->get();

            // Arahkan ke folder view admin (Variabel menggunakan $tugasPerawatan)
            return view('admin.maintenance.index', compact('schedules', 'tugasPerawatan', 'barangs'));

        } else {
            // -- KHUSUS STAFF / TEKNISI --
            // Filter: Munculkan hanya tugas perawatan alat yang belum selesai (Menunggu / Progres)
            $tugasPerawatan = PerawatanBarang::with(['barangMasuk.masterBarang'])
                ->whereIn('status', ['Menunggu', 'Progres'])
                ->latest()
                ->get();

            // Arahkan ke folder view khusus staff
            return view('staff.maintenance.index', compact('tugasPerawatan'));
        }
    }

    // 2. Menyimpan Master Jadwal Baru (Khusus Admin - Otomatisasi Perawatan)
    public function storeSchedule(Request $request)
    {
        $request->validate([
            'barang_masuk_id' => 'required',
            'frekuensi'       => 'required|in:mingguan,bulanan,tahunan',
            'tanggal_mulai'   => 'required|date',
            'deskripsi_tugas' => 'required'
        ]);

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalNextDue = $tanggalMulai->copy();

        if ($tanggalNextDue->isPast()) {
            $tanggalNextDue = Carbon::today();
        }

        // 1. SIMPAN ATURAN JADWAL RUTIN KE DATABASE
        $jadwal = MaintenanceSchedule::create([
            'barang_masuk_id'  => $request->barang_masuk_id,
            'frekuensi'        => $request->frekuensi,
            'tanggal_mulai'    => $request->tanggal_mulai,
            'tanggal_next_due' => $tanggalNextDue,
            'deskripsi_tugas'  => $request->deskripsi_tugas,
            'status'           => 'aktif'
        ]);

        // 2. OTOMATIS BUAT TUGAS PERTAMA (GENERATE TIKET KE STAFF)
        // Jika tanggal jadwal adalah HARI INI (atau sebelumnya), langsung buatkan tugas untuk Staff
        if ($tanggalNextDue->isToday() || $tanggalNextDue->isPast()) {
            $task = PerawatanBarang::create([
                'maintenance_schedule_id' => $jadwal->id, // Relasi ke jadwal induk
                'barang_masuk_id'         => $jadwal->barang_masuk_id,
                'tanggal_jadwal'          => $tanggalNextDue,
                'status'                  => 'Menunggu' // Status awal tugas agar muncul di layar staff
            ]);

            // [BARU] Tarik relasi nama barang agar bisa dikirim ke teks Telegram
            $task->load('barangMasuk.masterBarang');
            $task->keterangan = $task->barangMasuk->masterBarang->nama_barang ?? $jadwal->deskripsi_tugas;

            // [BARU] JALUR 1: Kirim ke lonceng web SEMUA user yang rolenya Staff
            $semuaTeknisi = User::where('role', 'Staff')->get();
            Notification::send($semuaTeknisi, new MaintenanceReminderNotification($task));

            // [BARU] JALUR 2: Kirim langsung nge-blass ke GRUP TELEGRAM TIM IT
            if (env('TELEGRAM_GROUP_ID')) {
                Notification::route('telegram', env('TELEGRAM_GROUP_ID'))
                            ->notify(new MaintenanceReminderNotification($task));
            }
        }

        return back()->with('success', 'Jadwal rutin berhasil dibuat. Tugas otomatis dikirim ke halaman Staff dan Grup Telegram Tim IT!');
    }

    // 3. Teknisi Memulai Tugas Perawatan Alat (Ubah status Menunggu -> Progres)
    public function mulaiPerawatan($id)
    {
        $perawatan = PerawatanBarang::findOrFail($id);

        $perawatan->update([
            'status'     => 'Progres', 
            'teknisi_id' => auth()->id() // Log staff yang mengeksekusi perawatan alat
        ]);

        return back()->with('success', 'Perawatan alat berhasil dimulai! Silakan lakukan pengecekan fisik.');
    }

    // 4. Teknisi Menyelesaikan Perawatan (Menerima input Checklist & Catatan)
    public function selesaikanPerawatan(Request $request, $id)
    {
        $perawatan = PerawatanBarang::findOrFail($id);
        
        // Olah data checklist tindakan perawatan menjadi teks
        $laporanChecklist = "Tindakan: ";
        if ($request->has('checklist') && is_array($request->checklist)) {
            $laporanChecklist .= implode(', ', $request->checklist) . ".";
        } else {
            $laporanChecklist .= "Pengecekan umum (Tidak ada checklist prosedur yang dicentang).";
        }

        // Tambahkan catatan manual dari lapangan jika ada
        if ($request->filled('catatan_perawatan')) {
            $laporanChecklist .= " | Catatan Tambahan: " . $request->catatan_perawatan;
        }

        // Update log perawatan alat di database menjadi 'Selesai'
        $perawatan->update([
            'status'            => 'Selesai',
            'tanggal_selesai'   => Carbon::now()->format('Y-m-d'),
            'catatan_perawatan' => $laporanChecklist, 
            'teknisi_id'        => auth()->id() 
        ]);

        return back()->with('success', 'Laporan maintenance alat berhasil dikirim!');
    }
}