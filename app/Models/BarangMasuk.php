<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    use HasFactory;

    // Nama tabel (karena kamu pakai 'barang_masuk', bukan default 'barang_masuks')
    protected $table = 'barang_masuk';

    // Field yang bisa diisi massal
    protected $fillable = [
        'nama_barang',
        'kategori',
        'jumlah',
        'tanggal_masuk',
        'no_sj',
        'no_ppi',
        'no_po',
        'keterangan',
    ];
}
