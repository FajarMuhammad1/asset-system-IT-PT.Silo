<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiAsset extends Model
{
    use HasFactory;

    protected $table = 'mutasi_assets';

    // Sesuaikan fillable dengan kolom database Anda yang baru
    protected $fillable = [
        'barang_masuk_id',
        'user_asal_id',
        'user_tujuan_id',
        'pemohon_id',
        'approved_by_id',
        'status',
        'alasan_penolakan',
        'lokasi_baru',
        'log_serah_terima_id',
        'keterangan',
        'tanggal_mutasi'
    ];

    /**
     * Relasi ke model BarangMasuk
     */
    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class, 'barang_masuk_id');
    }

    public function userAsal()
    {
        return $this->belongsTo(User::class, 'user_asal_id');
    }

    public function userTujuan()
    {
        return $this->belongsTo(User::class, 'user_tujuan_id');
    }

    public function pemohon()
    {
        return $this->belongsTo(User::class, 'pemohon_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function logSerahTerima()
    {
        return $this->belongsTo(LogSerahTerima::class, 'log_serah_terima_id');
    }
}