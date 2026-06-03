<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_masuk_id', 'reason', 'data_wiping_proof', 'status', 'submitted_by'
    ];

    // Relasi ke tabel barang_masuk
    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class, 'barang_masuk_id');
    }

    // Relasi ke tabel users (yang mengajukan)
    public function pengaju()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}