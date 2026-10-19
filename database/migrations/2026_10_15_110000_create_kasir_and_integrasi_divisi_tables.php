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
        // 1. Tabel Shift Kasir (Shift & Serah Terima Kasir)
        if (!Schema::hasTable('shift_kasirs')) {
            Schema::create('shift_kasirs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('kasir_id')->constrained('users')->cascadeOnDelete();
                $table->date('tanggal_shift')->nullable();
                $table->date('tgl_shift')->nullable();
                $table->decimal('saldo_awal_fisik', 15, 2)->default(0);
                $table->decimal('total_ki', 15, 2)->default(0);
                $table->decimal('total_ko', 15, 2)->default(0);
                $table->decimal('saldo_akhir_teoritis', 15, 2)->default(0);
                $table->decimal('total_masuk', 15, 2)->default(0);
                $table->decimal('total_keluar', 15, 2)->default(0);
                $table->decimal('saldo_akhir_fisik', 15, 2)->default(0);
                $table->decimal('selisih', 15, 2)->default(0);
                $table->decimal('selisih_kas', 15, 2)->default(0);
                $table->string('status')->default('open'); // 'open', 'closed', 'aktif', 'ditutup'
                $table->timestamp('closed_at')->nullable();
                $table->text('catatan_serah_terima')->nullable();
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('verified_at')->nullable();
                $table->text('catatan_supervisor')->nullable();
                $table->timestamps();

                $table->index(['kasir_id', 'status']);
            });
        }

        // 2. Tabel Bukti Pengeluaran (BP) Anggaran & Keuangan
        if (!Schema::hasTable('bukti_pengeluarans')) {
            Schema::create('bukti_pengeluarans', function (Blueprint $table) {
                $table->id();
                $table->string('no_bp')->unique();
                $table->string('no_register_anggaran')->nullable(); // Format: PR-YYYYMMDD-XXXX
                $table->foreignId('pengajuan_anggaran_id')->nullable()->constrained('pengajuan_anggaran')->nullOnDelete();
                $table->string('divisi');
                $table->date('tanggal_pengajuan');
                $table->string('pemohon');
                $table->text('keperluan');
                $table->decimal('nominal_acc', 15, 2);
                $table->enum('status', ['pending', 'approved', 'dicairkan_kasir', 'rejected'])->default('approved');
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('dicairkan_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('dicairkan_at')->nullable();
                $table->timestamps();

                $table->index(['divisi', 'status', 'tanggal_pengajuan']);
            });
        }

        // 3. Tabel Master Agen Sirkulasi (Integrasi Piutang Sirkulasi)
        if (!Schema::hasTable('agens')) {
            Schema::create('agens', function (Blueprint $table) {
                $table->id();
                $table->string('kode_agen')->unique();
                $table->string('nama_agen');
                $table->string('kategori')->default('11');
                $table->string('induk_agen')->nullable();
                $table->string('rute_kode')->nullable();
                $table->string('wilayah')->nullable();
                $table->string('telepon')->nullable();
                $table->integer('jatah_default')->default(0);
                $table->decimal('plafon_piutang', 15, 2)->default(0);
                $table->decimal('saldo_piutang', 15, 2)->default(0);
                $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
                $table->timestamps();

                $table->index(['kode_agen', 'status']);
            });
        }

        // 4. Tabel Kas Masuks (Penerimaan Iklan, Sirkulasi, Tunai, Bank)
        if (!Schema::hasTable('kas_masuks')) {
            Schema::create('kas_masuks', function (Blueprint $table) {
                $table->id();
                $table->string('no_kuitansi')->nullable()->unique(); // KI-01-YYYYMMXXXX / KI-02-YYYYMMXXXX / KI-14-YYYYMMXXXX
                $table->string('no_bkm')->nullable()->unique(); // Alias BKM
                $table->foreignId('shift_id')->nullable()->constrained('shift_kasirs')->nullOnDelete();
                $table->date('tanggal_terima')->nullable();
                $table->date('tgl_transaksi')->nullable();
                $table->string('no_blanko')->nullable();
                $table->string('kode_bagian')->default('01'); // '01', '02', '14'
                $table->string('kode_perkiraan')->nullable(); // COA: 1.00.04.01.01.01, dsb.
                $table->string('nama_perkiraan')->nullable();
                $table->string('terima_dari')->nullable();
                $table->string('nama_pembayar')->nullable();
                $table->string('penagih_inkaso')->nullable();
                $table->string('no_faktur_ref')->nullable(); // FK Ref ke Faktur Iklan
                $table->string('kode_agen_ref')->nullable();  // FK Ref ke Agen Sirkulasi
                $table->string('no_bapr_ref')->nullable();   // Nomor BAPR Retur
                $table->enum('tipe_transaksi', ['iklan', 'sirkulasi', 'tunai', 'bank'])->default('iklan');
                $table->unsignedBigInteger('ref_id')->nullable();
                $table->string('ref_type')->nullable();
                $table->enum('metode_bayar', ['tunai', 'transfer', 'qris'])->default('tunai');
                $table->string('bank_tujuan')->nullable();
                $table->string('no_referensi_transfer')->nullable();
                $table->string('bukti_transfer_path')->nullable();
                $table->decimal('jumlah_tagihan', 15, 2)->default(0);
                $table->decimal('total_tagihan', 15, 2)->default(0);
                $table->decimal('potongan_retur', 15, 2)->default(0); // Nilai Rupiah Retur Koran
                $table->decimal('potongan_pajak', 15, 2)->default(0); // PPN / PPh
                $table->string('no_bapr')->nullable();
                $table->integer('jumlah_retur_eks')->default(0);
                $table->decimal('jumlah_diterima', 15, 2)->default(0);
                $table->decimal('jumlah_dibayar', 15, 2)->default(0);
                $table->decimal('sisa_piutang', 15, 2)->default(0);
                $table->string('status_bayar')->default('lunas'); // 'lunas', 'sebagian', 'uang_muka'
                $table->string('status_pembayaran')->default('lunas');
                $table->text('keterangan')->nullable();
                $table->foreignId('kasir_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
                $table->boolean('is_closed')->default(false);
                $table->boolean('is_pulled_accounting')->default(false);
                $table->boolean('status_tarik_akunting')->default(false);
                $table->string('status_void')->nullable();
                $table->foreignId('void_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('void_at')->nullable();
                $table->text('alasan_void')->nullable();
                $table->timestamps();

                $table->index(['tanggal_terima', 'kode_bagian', 'status_bayar']);
                $table->index(['shift_id', 'is_closed', 'is_pulled_accounting']);
            });
        }

        // 5. Tabel Kas Keluars (Realisasi BP & Pengeluaran Kasir)
        if (!Schema::hasTable('kas_keluars')) {
            Schema::create('kas_keluars', function (Blueprint $table) {
                $table->id();
                $table->string('no_bkk')->unique(); // KO-YYYYMMXXXX
                $table->foreignId('shift_id')->nullable()->constrained('shift_kasirs')->nullOnDelete();
                $table->date('tanggal_keluar')->nullable();
                $table->date('tgl_transaksi')->nullable();
                $table->string('no_bp_ref')->nullable();
                $table->string('no_register_anggaran')->nullable(); // PR-YYYYMMDD-XXXX
                $table->string('kode_bagian')->default('03'); // '01', '03', '06', dll.
                $table->string('kode_perkiraan')->nullable();
                $table->string('nama_perkiraan')->nullable();
                $table->string('diberikan_kepada')->nullable();
                $table->string('nama_penerima')->nullable();
                $table->string('kategori_beban')->default('umum'); // 'komisi_kh', 'insentif_ae', 'bbm_armada', 'perlengkapan_rt', 'umum'
                $table->string('kategori')->default('umum');
                $table->decimal('jumlah_keluar', 15, 2)->default(0);
                $table->decimal('nominal_keluar', 15, 2)->default(0);
                $table->enum('metode_bayar', ['kas_fisik', 'transfer'])->default('kas_fisik');
                $table->foreignId('bp_id')->nullable()->constrained('bukti_pengeluarans')->nullOnDelete();
                $table->text('keterangan')->nullable();
                $table->foreignId('kasir_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
                $table->boolean('is_closed')->default(false);
                $table->boolean('is_pulled_accounting')->default(false);
                $table->boolean('status_tarik_akunting')->default(false);
                $table->string('status_void')->nullable();
                $table->foreignId('void_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('void_at')->nullable();
                $table->text('alasan_void')->nullable();
                $table->timestamps();

                $table->index(['tanggal_keluar', 'kategori_beban']);
                $table->index(['shift_id', 'is_closed', 'is_pulled_accounting']);
            });
        }

        // 6. Penambahan kolom siap_cair_kh_insentif pada transaksikoran
        if (Schema::hasTable('transaksikoran') && !Schema::hasColumn('transaksikoran', 'siap_cair_kh_insentif')) {
            Schema::table('transaksikoran', function (Blueprint $table) {
                $table->boolean('siap_cair_kh_insentif')->default(false)->after('piutang_transaksikoran');
            });
        }

        // 7. Penambahan kolom status_bayar dan siap_cair_kh_insentif pada faktur
        if (Schema::hasTable('faktur')) {
            Schema::table('faktur', function (Blueprint $table) {
                if (!Schema::hasColumn('faktur', 'siap_cair_kh_insentif')) {
                    $table->boolean('siap_cair_kh_insentif')->default(false)->after('status');
                }
                if (!Schema::hasColumn('faktur', 'status_bayar')) {
                    $table->string('status_bayar')->nullable()->after('status'); // 'lunas', 'uang_muka', dsb.
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transaksikoran') && Schema::hasColumn('transaksikoran', 'siap_cair_kh_insentif')) {
            Schema::table('transaksikoran', function (Blueprint $table) {
                $table->dropColumn('siap_cair_kh_insentif');
            });
        }

        if (Schema::hasTable('faktur')) {
            Schema::table('faktur', function (Blueprint $table) {
                if (Schema::hasColumn('faktur', 'status_bayar')) {
                    $table->dropColumn('status_bayar');
                }
                if (Schema::hasColumn('faktur', 'siap_cair_kh_insentif')) {
                    $table->dropColumn('siap_cair_kh_insentif');
                }
            });
        }

        Schema::dropIfExists('kas_keluars');
        Schema::dropIfExists('kas_masuks');
        Schema::dropIfExists('bukti_pengeluarans');
        Schema::dropIfExists('agens');
        Schema::dropIfExists('shift_kasirs');
    }
};
