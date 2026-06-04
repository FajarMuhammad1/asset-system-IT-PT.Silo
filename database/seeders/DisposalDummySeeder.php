<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mutasi_asets', function (Blueprint $table) {
            $table->id();
            // Relasi ke barang yang dimutasi
            $table->unsignedBigInteger('barang_masuk_id');
            // Relasi ke user pemegang sebelumnya (bisa null jika aset baru keluar gudang)
            $table->unsignedBigInteger('user_asal_id')->nullable();
            // Relasi ke user penerima baru
            $table->unsignedBigInteger('user_tujuan_id');
            // Lokasi penempatan fisik yang baru
            $table->string('lokasi_baru');
            $table->date('tanggal_mutasi');
            $table->text('alasan_mutasi');
            // Path untuk file BAST digital
            $table->string('file_bast')->nullable();
            // Siapa admin yang memproses mutasi ini
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            // Constraints
            $table->foreign('barang_masuk_id')->references('id')->on('barang_masuk')->onDelete('cascade');
            $table->foreign('user_asal_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('user_tujuan_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mutasi_asets');
    }
};