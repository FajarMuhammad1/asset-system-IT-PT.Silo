<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disposals', function (Blueprint $table) {
            $table->id();
            // Menyambungkan foreign key ke tabel barang_masuk
            $table->foreignId('barang_masuk_id')->constrained('barang_masuk')->onDelete('cascade');
            
            $table->text('reason');
            $table->string('data_wiping_proof')->nullable(); // File bukti penghapusan data
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->foreignId('submitted_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disposals');
    }
};