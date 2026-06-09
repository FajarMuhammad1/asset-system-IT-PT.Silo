<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RkabBudget extends Model
{
    use HasFactory;

    protected $table = 'rkab_budgets';

    protected $fillable = [
        'tahun',
        'kategori',
        'anggaran_alokasi',
        'anggaran_terpakai',
        'keterangan'
    ];

    // Menghitung Sisa Anggaran Otomatis ($budget->sisa_anggaran)
    public function getSisaAnggaranAttribute()
    {
        return $this->anggaran_alokasi - $this->anggaran_terpakai;
    }

    // Menghitung Persentase Penyerapan Otomatis ($budget->persentase_penyerapan)
    public function getPersentasePenyerapanAttribute()
    {
        if ($this->anggaran_alokasi <= 0) return 0;
        return round(($this->anggaran_terpakai / $this->anggaran_alokasi) * 100, 2);
    }
}