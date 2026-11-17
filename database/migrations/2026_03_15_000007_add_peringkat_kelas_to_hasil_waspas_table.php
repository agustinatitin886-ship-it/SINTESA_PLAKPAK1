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
        Schema::table('hasil_waspas', function (Blueprint $table) {
            $table->integer('peringkat_kelas')->nullable()->after('peringkat');
            $table->string('status_kelayakan', 100)->nullable()->after('peringkat_kelas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_waspas', function (Blueprint $table) {
            $table->dropColumn(['peringkat_kelas', 'status_kelayakan']);
        });
    }
};

