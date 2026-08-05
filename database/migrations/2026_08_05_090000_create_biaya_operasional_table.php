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
        Schema::create('biaya_operasional', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tiket (one-to-one, satu tiket satu biaya)
            $table->unsignedBigInteger('ticket_id')->unique();
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            
            // Relasi ke staff yang mengerjakan tiket
            $table->unsignedBigInteger('staff_id');
            $table->foreign('staff_id')->references('id')->on('users')->onDelete('cascade');
            
            // Admin yang memberikan/menyetujui biaya
            $table->unsignedBigInteger('diberikan_oleh');
            $table->foreign('diberikan_oleh')->references('id')->on('users')->onDelete('cascade');
            
            // Nominal bonus
            $table->decimal('nominal', 15, 2)->default(0);
            
            // Keterangan/catatan dari admin
            $table->text('keterangan')->nullable();
            
            // Tanggal pemberian bonus
            $table->date('tanggal_pemberian');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_operasional');
    }
};
