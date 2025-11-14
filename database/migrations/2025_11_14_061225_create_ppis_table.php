<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('ppis', function (Blueprint $table) {
        $table->id(); // ID Internal Database
        $table->string('no_ppi')->unique(); // Format: 0001.PPI-SBK/SILO/2025
        $table->date('tanggal'); // Tanggal Request
        
        // Relasi ke User (Pemohon)
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        
        $table->string('perangkat'); // Barang yg diminta/rusak
        $table->text('ba_kerusakan'); // Detail Masalah
        $table->text('keterangan')->nullable(); // Notes tambahan
        $table->string('file_ppi')->nullable(); // Upload foto/dokumen
        
        // Status flow
        $table->enum('status', ['pending', 'disetujui', 'selesai', 'ditolak'])->default('pending');
        
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('ppis');
}
};
