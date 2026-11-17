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
        Schema::create('hasil_waspas', function (Blueprint $table) {
            $table->increments('id_hasil');
            $table->unsignedInteger('id_siswa');
            $table->string('periode', 50);
            $table->double('nilai_wsm', 10, 6);
            $table->double('nilai_wpm', 10, 6);
            $table->double('nilai_qi', 10, 6);
            $table->integer('peringkat');
            $table->timestamps();

            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_waspas');
    }
};

