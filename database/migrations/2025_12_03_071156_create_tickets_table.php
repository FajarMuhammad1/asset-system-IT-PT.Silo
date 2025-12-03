<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            
            // Nomor Tiket (Format: TIK-202511-001)
            $table->string('no_tiket')->unique(); 
            
            // Siapa yang lapor? (Pengguna)
            $table->foreignId('pelapor_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            
            // Detail Masalah
            $table->string('judul_masalah');
            $table->text('deskripsi');
            $table->string('foto_masalah')->nullable();
            $table->string('prioritas')->default('Normal'); // Low, Normal, High, Critical
            
            // Siapa yang ngerjain? (Staff IT)
            // Awalnya NULL, diisi Admin saat Assign
            $table->foreignId('teknisi_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            
            // Status Pengerjaan (UPDATED)
            // Default 'Open' saat baru dibuat user
            $table->enum('status', ['Open', 'Progres', 'Closed', 'Ditolak'])->default('Open');
            
            // Tracking Penyelesaian
            $table->dateTime('tgl_selesai')->nullable();
            $table->text('solusi_teknisi')->nullable(); // Catatan perbaikan
            
            // Alasan kalau Staff nolak tugas
            $table->text('alasan_penolakan')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};