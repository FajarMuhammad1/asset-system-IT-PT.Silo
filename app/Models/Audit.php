<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $fillable = ['title', 'audit_date', 'status', 'created_by', 'description'];

    public function pengaju() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items() {
        return $this->hasMany(AuditItem::class);
    }
}
