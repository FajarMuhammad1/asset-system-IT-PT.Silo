<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogSerahTerima extends Model
{
    use HasFactory;
    
    protected $table = 'log_serah_terima';
    
    protected $fillable = [
        'barang_masuk_id',
        'user_pemegang_id',
        'admin_id',
        'tanggal_serah_terima',
        'keterangan',
        'ttd_penerima',
        'ttd_petugas',
        'foto_bukti',
        'file' // Ini udah bener
    ];

    /**
     * Relasi ke Aset (tabel 'barang_masuk')
     * "1 Log ini milik 1 Aset"
     */
    public function aset()
    {
        // 'barang_masuk_id' adalah foreign key di tabel INI
        return $this->belongsTo(BarangMasuk::class, 'barang_masuk_id');
    }

    /**
     * Relasi ke User (Pemegang/Penerima)
     * "1 Log ini milik 1 User (Pemegang)"
     */
    public function pemegang()
    {
        // 'user_pemegang_id' adalah foreign key di tabel INI
        return $this->belongsTo(User::class, 'user_pemegang_id');
    }

    /**
     * Relasi ke User (Admin/Petugas IT)
     * "1 Log ini dibuat oleh 1 User (Admin)"
     */
    public function admin()
    {
        // 'admin_id' adalah foreign key di tabel INI
        return $this->belongsTo(User::class, 'admin_id');
    }
}