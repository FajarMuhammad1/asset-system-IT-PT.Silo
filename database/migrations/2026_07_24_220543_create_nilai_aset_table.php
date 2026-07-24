<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nilai_aset', function (Blueprint $table) {
            $table->id();
            
            // Relasi (Foreign Key) ke tabel barang_masuk
            $table->foreignId('barang_masuk_id')
                  ->constrained('barang_masuk')
                  ->onDelete('cascade');
                  
            // Kolom nilai finansial
            $table->decimal('nilai_awal', 15, 2)->comment('Harga beli / perolehan');
            $table->decimal('nilai_sekarang', 15, 2)->comment('Nilai buku setelah penyusutan');
            $table->date('tanggal_penilaian');
            
            // Kolom depresiasi
            $table->string('metode_depresiasi', 50)->nullable()->default('Straight Line');
            $table->integer('masa_manfaat_bulan')->nullable()->comment('Dalam hitungan bulan');
            
            // Catatan tambahan
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nilai_aset');
    }
};