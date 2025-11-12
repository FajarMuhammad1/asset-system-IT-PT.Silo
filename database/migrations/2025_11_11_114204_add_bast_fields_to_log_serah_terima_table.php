<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('log_serah_terima', function (Blueprint $table) {
            // Tipe data TEXT buat nyimpen data Base64 dari Signature Pad
            $table->text('ttd_penerima')->nullable()->after('keterangan');
            $table->text('ttd_petugas')->nullable()->after('ttd_penerima');
            
            // Tipe data STRING buat nyimpen path file
            $table->string('foto_bukti')->nullable()->after('ttd_petugas');
            
            // INI YANG DIUBAH
            $table->string('file')->nullable()->after('foto_bukti');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_serah_terima', function (Blueprint $table) {
            // INI JUGA DIUBAH
            $table->dropColumn(['ttd_penerima', 'ttd_petugas', 'foto_bukti', 'file']);
        });
    }
};