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
        Schema::table('surat_jalan', function (Blueprint $table) {
            
            // Nambahin kolom 'id_suratjalan' (Varchar, Unik)
            // POSISI SETELAH 'id_sj' (Sesuai mau lo)
            $table->string('id_suratjalan')
                  ->unique()
                  ->nullable()
                  ->after('id_sj'); // <-- INI YANG DIUBAH

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan', function (Blueprint $table) {
            $table->dropUnique(['id_suratjalan']); // (Hapus unique dulu)
            $table->dropColumn('id_suratjalan');
        });
    }
};