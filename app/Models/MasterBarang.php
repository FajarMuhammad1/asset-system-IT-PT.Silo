<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MasterBarang extends Model
{
    protected $table = 'master_barang';
    protected $fillable = ['nama_barang', 'kategori', 'merk', 'spesifikasi'];

    // (Nanti ini bakal kepake)
    public function suratJalanDetails() {
        return $this->hasMany(SuratJalanDetail::class, 'master_barang_id');
    }
    public function assets() {
        return $this->hasMany(BarangMasuk::class, 'master_barang_id');
    }
}