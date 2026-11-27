<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('akun_perkiraan')) {
            Schema::table('akun_perkiraan', function (Blueprint $table) {
                if (!Schema::hasColumn('akun_perkiraan', 'saldo_awal')) {
                    $table->decimal('saldo_awal', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('akun_perkiraan', 'saldo_awal_tanggal')) {
                    $table->date('saldo_awal_tanggal')->nullable();
                }
            });
        }

        if (Schema::hasTable('jurnal_umum')) {
            Schema::table('jurnal_umum', function (Blueprint $table) {
                if (!Schema::hasColumn('jurnal_umum', 'reversed_by_jurnal_id')) {
                    $table->unsignedBigInteger('reversed_by_jurnal_id')->nullable()->index();
                }
                if (!Schema::hasColumn('jurnal_umum', 'reversal_of_jurnal_id')) {
                    $table->unsignedBigInteger('reversal_of_jurnal_id')->nullable()->index();
                }
                if (!Schema::hasColumn('jurnal_umum', 'approved_by')) {
                    $table->unsignedBigInteger('approved_by')->nullable();
                }
                if (!Schema::hasColumn('jurnal_umum', 'approved_at')) {
                    $table->timestamp('approved_at')->nullable();
                }
                if (!Schema::hasColumn('jurnal_umum', 'approval_notes')) {
                    $table->string('approval_notes', 255)->nullable();
                }
            });
        }

        if (!Schema::hasTable('accounting_account_mappings')) {
            Schema::create('accounting_account_mappings', function (Blueprint $table) {
                $table->id();
                $table->string('mapping_key', 60)->unique()->index();
                $table->string('label', 120);
                $table->string('kategori_modul', 60)->default('General Ledger');
                $table->string('kode_perkiraan', 30);
                $table->string('nama_perkiraan', 150);
                $table->string('keterangan', 255)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('rekonsiliasi_banks')) {
            Schema::create('rekonsiliasi_banks', function (Blueprint $table) {
                $table->id();
                $table->string('periode', 7)->index(); // YYYY-MM
                $table->string('rekening_bank', 30)->default('BCA')->index(); // BCA, BJB
                $table->string('kode_perkiraan_bank', 30);
                $table->date('tanggal_transaksi')->index();
                $table->string('uraian_bank', 255);
                $table->string('referensi_bank', 100)->nullable();
                $table->string('tipe_mutasi', 20)->default('kredit_masuk'); // kredit_masuk | debet_keluar
                $table->decimal('nominal', 15, 2)->default(0);
                $table->string('status_match', 20)->default('unmatched')->index(); // unmatched | matched | adjusted
                $table->string('matched_model', 60)->nullable(); // KasMasuk | KasKeluar
                $table->unsignedBigInteger('matched_id')->nullable();
                $table->unsignedBigInteger('jurnal_umum_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('aset_tetaps')) {
            Schema::create('aset_tetaps', function (Blueprint $table) {
                $table->id();
                $table->string('kode', 30)->unique()->index();
                $table->string('nama', 150);
                $table->string('kategori', 80);
                $table->date('tgl_perolehan');
                $table->decimal('harga_beli', 15, 2)->default(0);
                $table->decimal('tarif_susut', 6, 2)->default(0);
                $table->decimal('susut_tahunan', 15, 2)->default(0);
                $table->decimal('susut_bulanan', 15, 2)->default(0);
                $table->string('kode_perkiraan_aset', 30)->nullable();
                $table->string('status', 20)->default('aktif')->index(); // aktif | dijual | dihapus
                $table->date('tgl_disposal')->nullable();
                $table->decimal('nilai_disposal', 15, 2)->nullable();
                $table->unsignedBigInteger('jurnal_perolehan_id')->nullable();
                $table->unsignedBigInteger('jurnal_disposal_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('vendor_tagihans')) {
            Schema::create('vendor_tagihans', function (Blueprint $table) {
                $table->id();
                $table->string('no_tagihan', 40)->unique()->index();
                $table->string('no_invoice_vendor', 80)->nullable();
                $table->string('nama_vendor', 150);
                $table->string('kategori_beban', 80);
                $table->date('tanggal_tagihan')->index();
                $table->date('jatuh_tempo')->nullable()->index();
                $table->string('kode_perkiraan_beban', 30);
                $table->string('kode_perkiraan_utang', 30);
                $table->decimal('dpp', 15, 2)->default(0);
                $table->decimal('ppn', 15, 2)->default(0);
                $table->decimal('pph23', 15, 2)->default(0);
                $table->decimal('total_tagihan', 15, 2)->default(0);
                $table->decimal('total_terbayar', 15, 2)->default(0);
                $table->decimal('sisa_utang', 15, 2)->default(0);
                $table->string('status', 25)->default('tercatat')->index(); // draft | tercatat | dibayar_sebagian | lunas | batal
                $table->string('keterangan', 255)->nullable();
                $table->unsignedBigInteger('jurnal_akui_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('vendor_pembayarans')) {
            Schema::create('vendor_pembayarans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vendor_tagihan_id')->constrained('vendor_tagihans')->cascadeOnDelete();
                $table->string('no_bukti_bayar', 40)->unique()->index();
                $table->date('tanggal_bayar');
                $table->string('metode_bayar', 30)->default('transfer');
                $table->string('kode_perkiraan_kas_bank', 30);
                $table->decimal('nominal_bayar', 15, 2)->default(0);
                $table->decimal('potongan_pph', 15, 2)->default(0);
                $table->string('keterangan', 255)->nullable();
                $table->unsignedBigInteger('jurnal_bayar_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_pembayarans');
        Schema::dropIfExists('vendor_tagihans');
        Schema::dropIfExists('aset_tetaps');
        Schema::dropIfExists('rekonsiliasi_banks');
        Schema::dropIfExists('accounting_account_mappings');

        if (Schema::hasTable('jurnal_umum')) {
            Schema::table('jurnal_umum', function (Blueprint $table) {
                $cols = array_filter([
                    'reversed_by_jurnal_id',
                    'reversal_of_jurnal_id',
                    'approved_by',
                    'approved_at',
                    'approval_notes',
                ], fn ($col) => Schema::hasColumn('jurnal_umum', $col));

                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }

        if (Schema::hasTable('akun_perkiraan')) {
            Schema::table('akun_perkiraan', function (Blueprint $table) {
                $cols = array_filter(['saldo_awal', 'saldo_awal_tanggal'], fn ($col) => Schema::hasColumn('akun_perkiraan', $col));
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
