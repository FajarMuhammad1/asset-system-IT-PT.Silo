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
        'barang_masuk_id', // UPDATED: Menggunakan standar Laravel
        'user_asal_id',
        'user_tujuan_id',
        'keterangan',
        'tanggal_mutasi'
    ];

    /**
     * Relasi ke model BarangMasuk
     */
    public function barangMasuk()
    {
        // UPDATED: Menggunakan foreign key baru 'barang_masuk_id'
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
}