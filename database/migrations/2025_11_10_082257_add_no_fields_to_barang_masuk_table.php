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
    Schema::table('barang_masuk', function (Blueprint $table) {
        $table->string('no_sj')->nullable()->after('tanggal_masuk');
        $table->string('no_ppi')->nullable()->after('no_sj');
        $table->string('no_po')->nullable()->after('no_ppi');
    });
}

public function down(): void
{
    Schema::table('barang_masuk', function (Blueprint $table) {
        $table->dropColumn(['no_sj', 'no_ppi', 'no_po']);
    });
}

};
