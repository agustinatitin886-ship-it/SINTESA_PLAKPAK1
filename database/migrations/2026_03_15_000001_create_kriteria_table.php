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
        Schema::create('kriteria', function (Blueprint $table) {
            $table->increments('id_kriteria');
            $table->string('kode_kriteria', 20)->unique();
            $table->string('nama_kriteria', 150);
            $table->enum('jenis_atribut', ['Benefit', 'Cost']);
            $table->double('bobot_bwm', 8, 4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kriteria');
    }
};

