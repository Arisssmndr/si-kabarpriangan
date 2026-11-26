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
        // 1. Tambah Otorisasi Penerimaan/Penolakan Tugas Inkaso Mobile pada penugasan_inkaso
        Schema::table('penugasan_inkaso', function (Blueprint $table) {
            if (!Schema::hasColumn('penugasan_inkaso', 'status_konfirmasi')) {
                $table->enum('status_konfirmasi', ['menunggu', 'diterima', 'ditolak'])->default('menunggu')->after('status_tugas');
            }
            if (!Schema::hasColumn('penugasan_inkaso', 'alasan_penolakan')) {
                $table->text('alasan_penolakan')->nullable()->after('status_konfirmasi');
            }
            if (!Schema::hasColumn('penugasan_inkaso', 'waktu_konfirmasi')) {
                $table->dateTime('waktu_konfirmasi')->nullable()->after('alasan_penolakan');
            }
        });

        // 2. Tabel Bukti Setoran Koleksi (BSK) Digital Lapangan Ber-Barcode / QR
        if (!Schema::hasTable('setoran_inkaso_lapangans')) {
            Schema::create('setoran_inkaso_lapangans', function (Blueprint $table) {
                $table->id();
                $table->string('no_bsk', 50)->unique();
                $table->unsignedBigInteger('penugasan_id')->nullable()->index();
                $table->unsignedBigInteger('faktur_id')->index();
                $table->unsignedBigInteger('petugas_id')->index();
                $table->string('nama_pembayar', 150);
                $table->decimal('nominal_titipan', 15, 2)->default(0);
                $table->enum('metode_bayar', ['tunai', 'transfer'])->default('tunai');
                $table->string('keterangan_angsuran', 255)->nullable();
                $table->string('barcode_token', 80)->unique();
                $table->dateTime('waktu_terima_lapangan');
                $table->enum('status_approval', ['pending', 'approved', 'rejected'])->default('pending')->index();
                $table->text('catatan_kasir')->nullable();
                $table->unsignedBigInteger('approved_by_kasir_id')->nullable()->index();
                $table->dateTime('approved_at')->nullable();
                $table->string('no_kuitansi_kasir', 50)->nullable()->index();
                $table->unsignedBigInteger('kas_masuk_id')->nullable()->index();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setoran_inkaso_lapangans');

        Schema::table('penugasan_inkaso', function (Blueprint $table) {
            $table->dropColumn(['status_konfirmasi', 'alasan_penolakan', 'waktu_konfirmasi']);
        });
    }
};
