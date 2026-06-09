<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rkab_budgets', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun'); // Contoh: 2026
            $table->string('kategori'); // Contoh: Pengadaan Laptop, Maintenance Server, Software License
            $table->bigInteger('anggaran_alokasi')->default(0); // Total dana yang direncanakan
            $table->bigInteger('anggaran_terpakai')->default(0); // Realisasi pengeluaran berjalan
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rkab_budgets');
    }
};