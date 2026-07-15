<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditItem extends Model
{
    protected $fillable = [
        'audit_id', 'barang_masuk_id', 'scanned_location', 'scanned_pic', 
        'condition', 'is_match', 'is_found', 'scanned_by', 'scanned_at', 'notes'
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
        'is_match' => 'boolean',
        'is_found' => 'boolean',
    ];

    public function audit() {
        return $this->belongsTo(Audit::class);
    }

    public function aset() {
        return $this->belongsTo(BarangMasuk::class, 'barang_masuk_id');
    }

    public function scanner() {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
