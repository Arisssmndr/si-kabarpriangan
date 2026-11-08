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
        if (!Schema::hasTable('penomoran_dokumen')) {
            Schema::create('penomoran_dokumen', function (Blueprint $table) {
                $table->id();
                $table->string('tipe_dokumen', 50); // FAKTUR, KWITANSI, SPPI
                $table->integer('tahun');
                $table->integer('bulan')->default(0); // 1-12, atau 0 jika tahunan
                $table->unsignedInteger('nomor_terakhir')->default(0);
                $table->string('prefix', 20)->default('');
                $table->timestamps();

                $table->unique(['tipe_dokumen', 'tahun', 'bulan'], 'idx_tipe_tahun_bulan');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penomoran_dokumen');
    }
};
