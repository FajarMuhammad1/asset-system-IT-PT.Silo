<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_reports', function (Blueprint $table) {
            $table->id();
            
            // Siapa yang lapor? (Staff)
            $table->foreignId('staff_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            
            // Terkait tiket apa? (Bisa NULL)
            $table->foreignId('ticket_id')
                  ->nullable()
                  ->constrained('tickets')
                  ->onDelete('set null');
            
            $table->string('judul');
            $table->text('deskripsi');
            $table->text('hasil')->nullable(); // Hasil pekerjaan
            $table->string('lampiran')->nullable(); // Foto/File bukti
            
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_reports');
    }
};