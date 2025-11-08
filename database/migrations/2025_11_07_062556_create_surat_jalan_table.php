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
        Schema::create('surat_jalan', function (Blueprint $table) {
    $table->id('id_sj');
    $table->string('no_sj')->unique();
    $table->string('no_ppi')->nullable();
    $table->string('no_po')->nullable();
    $table->string('kategori')->nullable();
    $table->string('merk')->nullable();
    $table->string('model')->nullable();
    $table->text('spesifikasi')->nullable();
    $table->string('serial_number')->nullable();
    $table->integer('qty')->default(1);
    $table->text('keterangan')->nullable();
    $table->string('jenis_surat_jalan')->nullable();
    $table->date('tanggal_input')->nullable();
    $table->string('kode_asset')->nullable();
    $table->string('bast')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalan');
    }
};
