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
            // Menambahkan kolom kondisi_saat_serah
            // Tipe: VARCHAR(50), Default: 'Baik', Posisi: Setelah 'keterangan'
            $table->string('kondisi_saat_serah', 50)->default('Baik')->after('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_serah_terima', function (Blueprint $table) {
            // Menghapus kolom jika rollback
            $table->dropColumn('kondisi_saat_serah');
        });
    }
};