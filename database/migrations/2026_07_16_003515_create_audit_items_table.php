<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('audit_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('audit_id')->constrained('audits')->onDelete('cascade');
        $table->foreignId('barang_masuk_id')->constrained('barang_masuk'); // Aset yg diaudit
        
        // Data snapshot saat discan (dibandingkan dgn DB)
        $table->string('scanned_location')->nullable(); 
        $table->string('scanned_pic')->nullable(); 
        $table->enum('condition', ['Good', 'Damaged', 'Missing'])->default('Good');
        
        // Hasil perbandingan
        $table->boolean('is_match')->default(false); // Apakah lokasi/pic sesuai DB?
        $table->boolean('is_found')->default(true); // Apakah barangnya fisik ada?
        
        $table->foreignId('scanned_by')->nullable()->constrained('users');
        $table->timestamp('scanned_at')->nullable();
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_items');
    }
};
