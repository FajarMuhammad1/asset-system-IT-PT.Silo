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
            
            // Hapus 7 kolom ini karena ini pindah ke 'details'
            $table->dropColumn([
                'kategori',
                'merk',
                'model',
                'qty',
                'spesifikasi',
                'serial_number',
                'kode_asset'
            ]);

            // 'jenis_surat_jalan' dan 'bast' GAK KITA HAPUS
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan', function (Blueprint $table) {
            // (Kebalikannya)
            $table->string('kategori')->nullable();
            $table->string('merk')->nullable();
            $table->string('model')->nullable();
            $table->integer('qty')->nullable();
            $table->text('spesifikasi')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('kode_asset')->nullable();
        });
    }
};