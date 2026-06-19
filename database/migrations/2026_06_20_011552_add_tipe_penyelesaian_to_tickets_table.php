<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'tipe_penyelesaian')) {
                // Menambahkan kolom tipe_penyelesaian
                $table->enum('tipe_penyelesaian', ['Individu', 'Tim'])->default('Individu')->after('teknisi_id');
            }
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('tipe_penyelesaian');
        });
    }
};