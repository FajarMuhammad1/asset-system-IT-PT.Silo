<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('perawatan_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_schedule_id')->nullable()->constrained('maintenance_schedules')->onDelete('set null');
            $table->foreignId('barang_masuk_id')->constrained('barang_masuk')->onDelete('cascade');
            $table->foreignId('teknisi_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->date('tanggal_jadwal'); // Kapan harus dikerjakan
            $table->date('tanggal_selesai')->nullable(); // Kapan selesai
            $table->text('catatan_perawatan')->nullable(); 
            $table->enum('status', ['Menunggu', 'Progres', 'Selesai'])->default('Menunggu');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('perawatan_barangs');
    }
};