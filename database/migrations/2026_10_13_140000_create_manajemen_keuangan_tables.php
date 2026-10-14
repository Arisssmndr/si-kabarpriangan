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
        // 1. Tabel Customers
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('nama_customer');
                $table->string('no_telepon')->nullable();
                $table->text('alamat')->nullable();
                $table->enum('tipe', ['iklan', 'sirkulasi'])->default('iklan');
                $table->timestamps();

                $table->index(['tipe', 'nama_customer']);
            });
        }

        // 2. Tabel Faktur
        if (!Schema::hasTable('faktur')) {
            Schema::create('faktur', function (Blueprint $table) {
                $table->id();
                $table->string('no_faktur')->unique();
                $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
                $table->enum('kategori', ['iklan', 'sirkulasi']);
                $table->date('tanggal_faktur');
                $table->date('jatuh_tempo');
                $table->decimal('total_tagihan', 15, 2);
                $table->decimal('total_terbayar', 15, 2)->default(0);
                $table->decimal('sisa_piutang', 15, 2)->default(0);
                $table->enum('status', ['belum_bayar', 'cicilan', 'lunas'])->default('belum_bayar');
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->index(['kategori', 'status', 'tanggal_faktur']);
                $table->index('sisa_piutang');
            });
        }

        // 3. Tabel Penugasan Inkaso
        if (!Schema::hasTable('penugasan_inkaso')) {
            Schema::create('penugasan_inkaso', function (Blueprint $table) {
                $table->id();
                $table->string('no_tugas')->unique();
                $table->foreignId('faktur_id')->constrained('faktur')->cascadeOnDelete();
                $table->foreignId('petugas_id')->constrained('users')->cascadeOnDelete();
                $table->date('tanggal_tugas');
                $table->enum('status_tugas', ['berjalan', 'selesai', 'batal'])->default('berjalan');
                $table->text('catatan')->nullable();
                $table->timestamps();

                $table->index(['petugas_id', 'status_tugas', 'tanggal_tugas']);
            });
        }

        // 4. Tabel Pembayaran Faktur
        if (!Schema::hasTable('pembayaran_faktur')) {
            Schema::create('pembayaran_faktur', function (Blueprint $table) {
                $table->id();
                $table->string('no_kwitansi')->unique();
                $table->foreignId('faktur_id')->constrained('faktur')->cascadeOnDelete();
                $table->foreignId('penugasan_inkaso_id')->nullable()->constrained('penugasan_inkaso')->nullOnDelete();
                $table->date('tanggal_bayar');
                $table->decimal('nominal_bayar', 15, 2);
                $table->enum('metode_bayar', ['tunai', 'transfer'])->default('tunai');
                $table->enum('tipe_bayar', ['lunas_langsung', 'cicilan_inkaso'])->default('lunas_langsung');
                $table->foreignId('diterima_oleh')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->index(['faktur_id', 'tanggal_bayar']);
                $table->index('tipe_bayar');
            });
        }

        // 5. Tabel Kas Transaksi (Buku Kas / Treasury)
        if (!Schema::hasTable('kas_transaksi')) {
            Schema::create('kas_transaksi', function (Blueprint $table) {
                $table->id();
                $table->string('no_transaksi')->unique();
                $table->date('tanggal');
                $table->enum('jenis', ['masuk', 'keluar']);
                $table->string('kategori_kas');
                $table->decimal('nominal', 15, 2);
                $table->unsignedBigInteger('referensi_id')->nullable();
                $table->string('referensi_tipe')->nullable();
                $table->text('keterangan')->nullable();
                $table->decimal('saldo_akhir', 15, 2)->default(0);
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->index(['tanggal', 'jenis']);
                $table->index(['referensi_tipe', 'referensi_id']);
            });
        }

        // 6. Tabel Pengajuan Anggaran (Cash Out)
        if (!Schema::hasTable('pengajuan_anggaran')) {
            Schema::create('pengajuan_anggaran', function (Blueprint $table) {
                $table->id();
                $table->string('no_pengajuan')->unique();
                $table->string('divisi');
                $table->date('tanggal_pengajuan');
                $table->decimal('nominal_diajukan', 15, 2);
                $table->decimal('nominal_disetujui', 15, 2)->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected', 'cair'])->default('pending');
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('dicairkan_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->text('keterangan')->nullable();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->index(['divisi', 'status', 'tanggal_pengajuan']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_anggaran');
        Schema::dropIfExists('kas_transaksi');
        Schema::dropIfExists('pembayaran_faktur');
        Schema::dropIfExists('penugasan_inkaso');
        Schema::dropIfExists('faktur');
        Schema::dropIfExists('customers');
    }
};
