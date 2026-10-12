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
        // 1. Tabel Kategori Media (Induk)
        Schema::create('kategori_media', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique(); // KRN, ONL, PTV, dll
            $table->string('nama', 100);          // Koran Cetak, Media Online, Priangan TV
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tabel Jenis / Paket Iklan (Anak / Produk yang berelasi ke Kategori Media)
        Schema::create('jenis_iklan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_media_id')
                  ->constrained('kategori_media')
                  ->onDelete('cascade');
            $table->string('kode_jenis', 30)->unique(); // KRN001, ONL001, PTV001
            $table->string('nama_jenis', 150);          // Iklan Baris, Banner Header, Running Text
            $table->bigInteger('tarif_dasar')->default(0);
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_iklan');
        Schema::dropIfExists('kategori_media');
    }
};
