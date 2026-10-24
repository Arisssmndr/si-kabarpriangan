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
        Schema::table('transaksionline', function (Blueprint $table) {
            // 1. Workflow & Kategori Produk Publikasi
            $table->string('status_produksi', 50)->default('menunggu_bahan')->after('skema_pembayaran');
            $table->json('kategori_produk_publikasi')->nullable()->after('status_produksi');
            $table->string('no_spk')->nullable()->after('produk_jasa');

            // 2. Pasca-Publish & Bukti Tayang (Digital Tearsheet)
            $table->string('url_berita_tayang', 500)->nullable()->after('url_materi_iklan');
            $table->string('url_medsos_tayang', 500)->nullable()->after('url_berita_tayang');
            $table->string('file_bukti_tayang', 500)->nullable()->after('url_medsos_tayang');
            $table->date('tanggal_realisasi_tayang')->nullable()->after('tanggal_selesai_tayang');
            $table->text('catatan_redaksi')->nullable()->after('file_bukti_tayang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksionline', function (Blueprint $table) {
            $table->dropColumn([
                'status_produksi',
                'kategori_produk_publikasi',
                'no_spk',
                'url_berita_tayang',
                'url_medsos_tayang',
                'file_bukti_tayang',
                'tanggal_realisasi_tayang',
                'catatan_redaksi',
            ]);
        });
    }
};
