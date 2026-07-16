<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketFeedback extends Model
{
    use HasFactory;

    protected $table = 'ticket_feedbacks';

    // Kolom yang diizinkan untuk diisi
    protected $fillable = [
        'ticket_id',
        'user_id',
        'rating',
        'feedback_text',
    ];

    // Relasi ke tabel Ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    // Relasi ke tabel User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}