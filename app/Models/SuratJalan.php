<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    use HasFactory;

    protected $table = 'surat_jalan';
    protected $primaryKey = 'id_sj';

  protected $fillable = [
    'no_sj', 'no_ppi', 'no_po', 'kategori', 'merk', 'model', 
    'spesifikasi', 'serial_number', 'qty', 'keterangan', 
    'jenis_surat_jalan', 'tanggal_input', 'kode_asset', 'bast', 'file'
];

}
