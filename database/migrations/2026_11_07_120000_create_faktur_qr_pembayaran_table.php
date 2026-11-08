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
        Schema::create('faktur_qr_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->enum('kategori', ['KORAN', 'ONLINE', 'TV']);
            $table->unsignedBigInteger('transaksi_id');
            $table->string('no_faktur', 64);
            $table->string('kode_pengaman', 16);
            $table->string('signature', 64);

            // Snapshot saat QR diterbitkan untuk audit & pencocokan cepat
            $table->string('nama_pemasang', 255);
            $table->decimal('total_tagihan', 15, 2)->default(0);
            $table->decimal('nominal_bayar', 15, 2);
            $table->decimal('sisa_piutang', 15, 2)->default(0);

            // Siklus hidup QR: AKTIF -> DISETOR (sudah discan kasir) / BATAL (direvisi/dibatalkan)
            $table->enum('status', ['AKTIF', 'DISETOR', 'BATAL'])->default('AKTIF');
            $table->string('alasan_batal', 255)->nullable();

            // Jejak audit pembuatan di Iklan / FO / Akuntansi
            $table->unsignedBigInteger('dibuat_oleh')->nullable();
            $table->timestamp('dibuat_pada')->nullable();

            // Disiapkan khusus untuk integrasi modul Kasir (Nurwan)
            $table->timestamp('discan_pada')->nullable();
            $table->unsignedBigInteger('discan_oleh')->nullable();
            $table->unsignedBigInteger('penerimaan_kasir_id')->nullable();

            $table->timestamps();

            $table->index(['kategori', 'transaksi_id', 'status'], 'idx_fqr_kat_trx_status');
            $table->index(['no_faktur', 'status'], 'idx_fqr_faktur_status');
            $table->unique(['no_faktur', 'kode_pengaman'], 'uniq_fqr_faktur_kode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faktur_qr_pembayaran');
    }
};

