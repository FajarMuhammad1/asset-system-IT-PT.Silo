<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('perawatan_barangs', function (Blueprint $table) {
        // Tambahkan pengaman ini: Hanya buat jika kolom BELUM ada di database
        if (!Schema::hasColumn('perawatan_barangs', 'tanggal_jadwal')) {
            $table->date('tanggal_jadwal')->nullable();
        }

        // Catatan: Jika ada kolom lain di bawahnya yang ikut error, bungkus dengan cara yang sama, contoh:
        // if (!Schema::hasColumn('perawatan_barangs', 'kolom_lain')) {
        //     $table->string('kolom_lain')->nullable();
        // }
    });
}

    public function down()
    {
        Schema::table('perawatan_barangs', function (Blueprint $table) {
            $table->dropColumn(['tanggal_jadwal', 'tanggal_selesai', 'catatan_perawatan', 'status']);
        });
    }
};