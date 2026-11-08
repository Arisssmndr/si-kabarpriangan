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
            $table->string('produk_jasa')->nullable()->after('nama_pemasangonline');
            $table->string('nama_pic')->nullable()->after('produk_jasa');
            $table->string('jabatan_pic')->nullable()->after('nama_pic');
            $table->string('no_kontak_pemasang')->nullable()->after('alamat_pemasangonline');
            $table->json('platforms_publikasi')->nullable()->after('portal_iklanonline');
            $table->string('skema_pembayaran', 50)->default('sekali_tayang')->after('platforms_publikasi');
            $table->string('bentuk_publikasi', 50)->default('display')->after('skema_pembayaran');
            $table->string('bentuk_publikasi_lainnya')->nullable()->after('bentuk_publikasi');
            $table->text('materi_iklan')->nullable()->after('bentuk_publikasi_lainnya');
            $table->string('url_materi_iklan', 500)->nullable()->after('materi_iklan');
            $table->date('tanggal_selesai_tayang')->nullable()->after('tanggal_muatiklanonline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksionline', function (Blueprint $table) {
            $table->dropColumn([
                'produk_jasa',
                'nama_pic',
                'jabatan_pic',
                'no_kontak_pemasang',
                'platforms_publikasi',
                'skema_pembayaran',
                'bentuk_publikasi',
                'bentuk_publikasi_lainnya',
                'materi_iklan',
                'url_materi_iklan',
                'tanggal_selesai_tayang',
            ]);
        });
    }
};
