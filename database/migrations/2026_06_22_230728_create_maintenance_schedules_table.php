<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_masuk_id')->constrained('barang_masuk')->onDelete('cascade');
            $table->enum('frekuensi', ['mingguan', 'bulanan', 'tahunan']);
            $table->date('tanggal_mulai'); 
            $table->date('tanggal_next_due'); // Kapan sistem harus memunculkan tiket
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->text('deskripsi_tugas')->nullable(); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_schedules');
    }
};