<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity; 
use Spatie\Activitylog\LogOptions;

class Ticket extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'tickets';

    protected $fillable = [
        'no_tiket', 
        'pelapor_id',
        'barang_masuk_id', // <--- TIKET TERHUBUNG KE ASET FISIK
        'judul_masalah', 
        'deskripsi', 
        'foto_masalah', 
        'prioritas',
        'teknisi_id', 
        'status', 
        'tgl_selesai', 
        'solusi_teknisi',
        'alasan_penolakan',
        'started_at',
        'tipe_penyelesaian' // <--- DITAMBAHKAN UNTUK TIAP TIKET (Individu / Tim)
    ];

    // --- KONFIGURASI ACTIVITY LOG (Spatie) ---
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            // Ditambahkan 'tipe_penyelesaian' agar jika diubah, log mencatat aktivitasnya
            // Hanya catat yang berubah saja (biar log gak penuh sampah)
            ->logOnly(['status', 'teknisi_id', 'prioritas', 'solusi_teknisi', 'alasan_penolakan', 'tipe_penyelesaian'])
            ->logOnlyDirty()
            
            // Deskripsi log
            ->setDescriptionForEvent(fn(string $eventName) => "Tiket Helpdesk telah di-{$eventName}");
    }
    // -----------------------------------------

    // Relasi: Pelapor (User)
    public function pelapor()
    {
        // Pastikan nama model User Anda sesuai dengan namespace (App\Models\User)
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    // Relasi: Teknisi (Staff IT)
    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }

    // Relasi: Aset / Unit Fisik (Barang Masuk)
    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class, 'barang_masuk_id');
    }

    /**
     * Relasi: Feedback / Penilaian Tiket
     * Satu tiket hanya memiliki satu feedback dari pelapor
     */
    public function feedback()
    {
        // Eloquent secara otomatis akan mencari kolom 'ticket_id' di tabel feedback
        return $this->hasOne(TicketFeedback::class);
    }

    /**
     * Relasi: Biaya Operasional / Bonus Staff
     * Satu tiket hanya memiliki satu biaya operasional
     */
    public function biayaOperasional()
    {
        return $this->hasOne(BiayaOperasional::class, 'ticket_id');
    }
}