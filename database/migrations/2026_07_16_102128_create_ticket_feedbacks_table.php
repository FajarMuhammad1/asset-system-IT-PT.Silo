<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_feedbacks', function (Blueprint $table) {
            $table->id();
            // Asumsi nama tabel tiket Anda adalah 'tickets' dan tabel user 'users'
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            $table->integer('rating')->comment('Skala 1-5');
            $table->text('feedback_text')->nullable()->comment('Komentar pengguna');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_feedbacks');
    }
};