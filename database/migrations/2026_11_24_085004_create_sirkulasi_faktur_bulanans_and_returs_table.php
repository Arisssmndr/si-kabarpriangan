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
        // 1. Tabel Faktur Sirkulasi Bulanan Terkonsolidasi (1 Agen = 1 Faktur / Bulan)
        Schema::create('sirkulasi_faktur_bulanans', function (Blueprint $table) {
            $table->id();
            $table->string('no_faktur', 50)->unique();
            $table->string('periode_bulan', 7)->index(); // YYYY-MM
            $table->unsignedBigInteger('agen_id')->index();
            $table->string('kode_agen', 20)->index();
            $table->string('nama_agen', 150);
            $table->string('kategori_agen', 20)->default('sub_agen'); // '11' / '99' / 'agen_besar' / 'sub_agen'
            $table->string('wilayah', 100)->nullable();
            
            // Oplah & Retur (Eks)
            $table->integer('total_oplah_kirim')->default(0);
            $table->integer('total_retur_eks')->default(0);
            $table->integer('oplah_bersih')->default(0);
            
            // Finansial
            $table->decimal('tarif_per_eks', 12, 2)->default(0);
            $table->decimal('total_tagihan_kotor', 15, 2)->default(0);
            $table->decimal('total_potongan_retur', 15, 2)->default(0);
            $table->decimal('total_tagihan_bersih', 15, 2)->default(0);
            $table->decimal('total_terbayar', 15, 2)->default(0);
            $table->decimal('sisa_piutang', 15, 2)->default(0);
            $table->enum('status_pembayaran', ['belum_lunas', 'sebagian', 'lunas'])->default('belum_lunas')->index();
            
            // Audit & Referensi
            $table->json('rincian_harian')->nullable(); // Snapshot 30 hari kirim
            $table->string('no_bapr', 50)->nullable();
            $table->text('catatan_retur')->nullable();
            $table->date('tanggal_faktur');
            $table->date('jatuh_tempo');
            $table->dateTime('tanggal_lunas')->nullable();
            $table->unsignedBigInteger('diterbitkan_oleh')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Verifikasi Fisik Retur Koran Sirkulasi oleh Akuntansi
        Schema::create('sirkulasi_returs', function (Blueprint $table) {
            $table->id();
            $table->string('no_bapr', 50)->unique();
            $table->string('periode_bulan', 7)->index(); // YYYY-MM
            $table->unsignedBigInteger('agen_id')->index();
            $table->string('kode_agen', 20)->index();
            $table->string('nama_agen', 150);
            $table->date('tanggal_retur');
            $table->integer('jumlah_retur_fisik')->default(0);
            $table->integer('jumlah_retur_disetujui')->default(0);
            $table->text('catatan_kondisi')->nullable();
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('disetujui')->index();
            $table->unsignedBigInteger('diverifikasi_oleh')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sirkulasi_returs');
        Schema::dropIfExists('sirkulasi_faktur_bulanans');
    }
};
