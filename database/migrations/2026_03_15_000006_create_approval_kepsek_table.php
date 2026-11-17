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
        Schema::create('approval_kepsek', function (Blueprint $table) {
            $table->increments('id_approval');
            $table->string('periode', 50)->unique();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->string('nomor_sk', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->dateTime('tanggal_persetujuan')->nullable();
            $table->unsignedInteger('disetujui_oleh')->nullable();
            $table->timestamps();

            $table->foreign('disetujui_oleh')->references('id_user')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_kepsek');
    }
};

