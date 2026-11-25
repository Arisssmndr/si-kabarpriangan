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
        Schema::table('anggaran_rabs', function (Blueprint $table) {
            // Drop old unique constraint on periode + kode_bagian if it exists
            $table->dropUnique('anggaran_rabs_periode_kode_bagian_unique');

            // Add kode_perkiraan
            $table->string('kode_perkiraan', 50)->default('')->after('kode_bagian');

            // New unique constraint on periode + kode_bagian + kode_perkiraan
            $table->unique(['periode', 'kode_bagian', 'kode_perkiraan'], 'anggaran_rabs_periode_bagian_perkiraan_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggaran_rabs', function (Blueprint $table) {
            $table->dropUnique('anggaran_rabs_periode_bagian_perkiraan_unique');
            $table->dropColumn('kode_perkiraan');
            $table->unique(['periode', 'kode_bagian'], 'anggaran_rabs_periode_kode_bagian_unique');
        });
    }
};
