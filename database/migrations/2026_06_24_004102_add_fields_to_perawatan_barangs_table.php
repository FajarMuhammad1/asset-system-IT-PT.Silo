<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('perawatan_barangs', function (Blueprint $table) {
            // Pastikan Anda menyesuaikan atau menghapus kolom yang mungkin sudah ada
            $table->date('tanggal_jadwal')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->text('catatan_perawatan')->nullable();
            // Inilah kunci utamanya! Kita buat status khusus untuk teknisi:
            $table->enum('status', ['Menunggu', 'Proses', 'Selesai'])->default('Menunggu');
        });
    }

    public function down()
    {
        Schema::table('perawatan_barangs', function (Blueprint $table) {
            $table->dropColumn(['tanggal_jadwal', 'tanggal_selesai', 'catatan_perawatan', 'status']);
        });
    }
};