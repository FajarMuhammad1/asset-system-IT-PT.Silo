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
        'judul_masalah', 
        'deskripsi', 
        'foto_masalah', 
        'prioritas',
        'teknisi_id', 
        'status', 
        'tgl_selesai', 
        'solusi_teknisi',
        'alasan_penolakan'
    ];

    // --- KONFIGURASI ACTIVITY LOG (Spatie) ---
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            // Catat perubahan pada kolom-kolom penting ini
            ->logOnly(['status', 'teknisi_id', 'prioritas', 'solusi_teknisi', 'alasan_penolakan'])
            
            // Hanya catat yang berubah saja (biar log gak penuh sampah)
            ->logOnlyDirty()
            
            // Deskripsi log
            ->setDescriptionForEvent(fn(string $eventName) => "Tiket Helpdesk telah di-{$eventName}");
    }
    // -----------------------------------------

    // Relasi: Pelapor (User)
    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    // Relasi: Teknisi (Staff IT)
    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }
}