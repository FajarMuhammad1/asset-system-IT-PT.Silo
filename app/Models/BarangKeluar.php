<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    protected $fillable = [
        'nama_barang', 'kategori', 'jumlah', 'tanggal_masuk', 'keterangan'
    ];
}
