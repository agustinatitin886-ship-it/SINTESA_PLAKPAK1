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
        Schema::create('log_bwm', function (Blueprint $table) {
            $table->increments('id_log');
            $table->unsignedInteger('best_kriteria_id');
            $table->unsignedInteger('worst_kriteria_id');
            $table->text('vektor_best_to_others');
            $table->text('vektor_others_to_worst');
            $table->double('consistency_ratio', 8, 4);
            $table->double('xi_value', 8, 4)->nullable();
            $table->string('periode', 50)->nullable();
            $table->timestamps();

            $table->foreign('best_kriteria_id')->references('id_kriteria')->on('kriteria')->onDelete('cascade');
            $table->foreign('worst_kriteria_id')->references('id_kriteria')->on('kriteria')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_bwm');
    }
};

