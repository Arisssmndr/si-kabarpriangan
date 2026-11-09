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
        // 1. Master Nomor Perkiraan (Chart of Accounts / COA 7 Segmen)
        Schema::create('akun_perkiraan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_perkiraan', 30)->unique()->index();
            $table->string('nama_perkiraan', 150);
            $table->unsignedTinyInteger('kepala')->index(); // 1..8
            $table->string('kategori', 80); // Aset Lancar, Kewajiban, Beban Operasional, dll.
            $table->enum('saldo_normal', ['debet', 'kredit'])->default('debet');
            $table->string('entitas', 60)->default('Kabar Priangan')->index();
            $table->boolean('status_aktif')->default(true)->index();
            $table->string('deskripsi', 255)->nullable();
            $table->timestamps();
        });

        // 2. Kontrol Periode Akuntansi (Period Closing)
        Schema::create('periode_akuntansi', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('tahun')->index();
            $table->unsignedTinyInteger('bulan')->index();
            $table->string('periode_key', 7)->unique(); // YYYY-MM
            $table->enum('status', ['terbuka', 'terkunci'])->default('terbuka')->index();
            $table->timestamp('ditutup_pada')->nullable();
            $table->foreignId('ditutup_oleh_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 3. Log Hitung & Anti-Dobel Akuntansi
        Schema::create('log_hitung_akuntansi', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', [
                'penjualan_iklan',
                'pembayaran_iklan',
                'penjualan_sirkulasi',
                'pembayaran_sirkulasi',
                'saldo_awal_sirkulasi'
            ])->index();
            $table->date('tanggal_transaksi')->nullable()->index();
            $table->unsignedTinyInteger('bulan_periode')->nullable()->index();
            $table->unsignedSmallInteger('tahun_periode')->nullable()->index();
            $table->unsignedInteger('total_faktur')->default(0);
            $table->decimal('total_nominal', 15, 2)->default(0);
            $table->enum('status', ['sukses', 'batal_dibuka'])->default('sukses')->index();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 4. Jurnal Umum (General Ledger Header)
        Schema::create('jurnal_umum', function (Blueprint $table) {
            $table->id();
            $table->string('no_jurnal', 35)->unique()->index();
            $table->date('tanggal')->index();
            $table->enum('tipe', ['auto', 'memorial', 'pembalik'])->default('auto')->index();
            $table->enum('sumber', ['iklan', 'sirkulasi', 'kasir', 'manual', 'penyusutan', 'akrual'])->default('manual')->index();
            $table->string('referensi', 60)->nullable()->index(); // No Faktur / No Kwitansi / No CN
            $table->string('keterangan', 255);
            $table->decimal('total_debet', 15, 2)->default(0);
            $table->decimal('total_kredit', 15, 2)->default(0);
            $table->boolean('is_balanced')->default(true);
            $table->enum('status', ['posted', 'draft', 'void'])->default('posted')->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 5. Jurnal Umum Detail (General Ledger Rows)
        Schema::create('jurnal_umum_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurnal_umum_id')->constrained('jurnal_umum')->cascadeOnDelete();
            $table->foreignId('akun_perkiraan_id')->nullable()->constrained('akun_perkiraan')->nullOnDelete();
            $table->string('kode_perkiraan', 30)->index();
            $table->string('nama_perkiraan', 150);
            $table->decimal('debet', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->string('keterangan_baris', 255)->nullable();
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->timestamps();
        });

        // 6. Koreksi Piutang Sub-Ledger (CN-IKL & CN-SIR)
        Schema::create('koreksi_piutang', function (Blueprint $table) {
            $table->id();
            $table->string('no_bukti', 30)->unique()->index(); // CN-IKL-2026XXXX / CN-SIR-2026XXXX
            $table->enum('jenis', ['iklan', 'sirkulasi'])->index();
            $table->date('tanggal_koreksi')->index();
            $table->unsignedBigInteger('faktur_id')->nullable()->index();
            $table->string('no_faktur', 50)->nullable()->index();
            $table->string('no_order', 50)->nullable()->index();
            $table->string('kode_perkiraan', 30)->index();
            $table->string('nama_entitas', 150);
            $table->string('alamat', 255)->nullable();
            $table->decimal('nilai_penjualan', 15, 2)->default(0);
            $table->decimal('jumlah_koreksi', 15, 2)->default(0);
            $table->decimal('saldo_setelah_koreksi', 15, 2)->default(0);
            // Kalkulator Eksemplar Retur Sirkulasi
            $table->unsignedInteger('eksemplar_langganan')->default(0);
            $table->unsignedInteger('eksemplar_eceran')->default(0);
            $table->decimal('harga_koran', 10, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->foreignId('jurnal_umum_id')->nullable()->constrained('jurnal_umum')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 7. Audit Log Akuntansi (Trail Aktivitas Sensitif)
        Schema::create('audit_log_akuntansi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('aksi', 60)->index();
            $table->string('modul', 40)->index();
            $table->string('deskripsi', 255);
            $table->json('payload_sebelum')->nullable();
            $table->json('payload_sesudah')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_log_akuntansi');
        Schema::dropIfExists('koreksi_piutang');
        Schema::dropIfExists('jurnal_umum_detail');
        Schema::dropIfExists('jurnal_umum');
        Schema::dropIfExists('log_hitung_akuntansi');
        Schema::dropIfExists('periode_akuntansi');
        Schema::dropIfExists('akun_perkiraan');
    }
};

