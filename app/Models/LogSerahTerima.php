<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogSerahTerima extends Model
{
    use HasFactory;

    protected $table = 'log_serah_terima';

    // WAJIB MENAMBAHKAN 'admin_id' DISINI
    protected $fillable = [
        'barang_masuk_id',
        'user_pemegang_id',
        'admin_id',            // <--- INI YANG KURANG TADI
        'tanggal_serah_terima',
        'keterangan',
        'foto_bukti',
        'kondisi_saat_serah',
        'ttd_penerima',
        'ttd_petugas',
        'status',
        'file_bast_final' // Tambahkan juga jika nanti mau simpan nama file PDF
    ];

    // Relasi (Biarkan seperti yang sudah ada)
    public function aset()
    {
        return $this->belongsTo(BarangMasuk::class, 'barang_masuk_id');
    }

    public function pemegang()
    {
        return $this->belongsTo(User::class, 'user_pemegang_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}