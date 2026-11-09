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
        // 1. Penambahan kolom SOP Penerimaan Kas Besar (BKM) & Bank Masuk (BBM) serta Aging Faktur pada kas_masuks
        if (Schema::hasTable('kas_masuks')) {
            Schema::table('kas_masuks', function (Blueprint $table) {
                if (!Schema::hasColumn('kas_masuks', 'jenis_dokumen')) {
                    $table->string('jenis_dokumen', 20)->default('BKM')->after('no_bkm'); // 'BKM', 'BBM'
                }
                if (!Schema::hasColumn('kas_masuks', 'is_cicilan')) {
                    $table->boolean('is_cicilan')->default(false)->after('status_pembayaran');
                }
                if (!Schema::hasColumn('kas_masuks', 'no_kuitansi_sementara')) {
                    $table->string('no_kuitansi_sementara', 60)->nullable()->after('is_cicilan');
                }
                if (!Schema::hasColumn('kas_masuks', 'bulan_faktur_asal')) {
                    $table->unsignedTinyInteger('bulan_faktur_asal')->nullable()->after('no_faktur_ref');
                }
                if (!Schema::hasColumn('kas_masuks', 'tahun_faktur_asal')) {
                    $table->unsignedSmallInteger('tahun_faktur_asal')->nullable()->after('bulan_faktur_asal');
                }
                if (!Schema::hasColumn('kas_masuks', 'manager_otorisasi')) {
                    $table->string('manager_otorisasi', 100)->nullable()->after('user_id');
                }
            });
        }

        // 2. Penambahan kolom SOP Pengeluaran Kas Besar (BKK), Bank Keluar (BBK & Cek), Kas Kecil (BKKC), dan Pengadaan pada kas_keluars
        if (Schema::hasTable('kas_keluars')) {
            Schema::table('kas_keluars', function (Blueprint $table) {
                $table->string('metode_bayar', 30)->default('kas_fisik')->change();

                if (!Schema::hasColumn('kas_keluars', 'jenis_dokumen')) {
                    $table->string('jenis_dokumen', 20)->default('BKK')->after('no_bkk'); // 'BKK', 'BBK', 'BKKC', 'BBM'
                }
                if (!Schema::hasColumn('kas_keluars', 'no_dp')) {
                    $table->string('no_dp', 60)->nullable()->after('no_register_anggaran');
                }
                if (!Schema::hasColumn('kas_keluars', 'no_pr')) {
                    $table->string('no_pr', 60)->nullable()->after('no_dp');
                }
                if (!Schema::hasColumn('kas_keluars', 'no_po')) {
                    $table->string('no_po', 60)->nullable()->after('no_pr');
                }
                if (!Schema::hasColumn('kas_keluars', 'no_pum')) {
                    $table->string('no_pum', 60)->nullable()->after('no_po');
                }
                if (!Schema::hasColumn('kas_keluars', 'no_cek')) {
                    $table->string('no_cek', 60)->nullable()->after('metode_bayar');
                }
                if (!Schema::hasColumn('kas_keluars', 'catatan_penolakan')) {
                    $table->text('catatan_penolakan')->nullable()->after('keterangan');
                }
            });
        }

        // 3. Penambahan kolom referensi Pengadaan & Catatan Penolakan DP pada bukti_pengeluarans
        if (Schema::hasTable('bukti_pengeluarans')) {
            Schema::table('bukti_pengeluarans', function (Blueprint $table) {
                if (!Schema::hasColumn('bukti_pengeluarans', 'no_dp')) {
                    $table->string('no_dp', 60)->nullable()->after('no_register_anggaran');
                }
                if (!Schema::hasColumn('bukti_pengeluarans', 'no_pr')) {
                    $table->string('no_pr', 60)->nullable()->after('no_dp');
                }
                if (!Schema::hasColumn('bukti_pengeluarans', 'no_po')) {
                    $table->string('no_po', 60)->nullable()->after('no_pr');
                }
                if (!Schema::hasColumn('bukti_pengeluarans', 'no_pum')) {
                    $table->string('no_pum', 60)->nullable()->after('no_po');
                }
                if (!Schema::hasColumn('bukti_pengeluarans', 'catatan_penolakan')) {
                    $table->text('catatan_penolakan')->nullable()->after('dicairkan_at');
                }
            });
        }

        // 4. Penambahan kolom SOP Kas Kecil: Bon Sementara (BS), Penyelesaian BKKC, dan Pengisian Kembali LKK pada kasbons
        if (Schema::hasTable('kasbons')) {
            Schema::table('kasbons', function (Blueprint $table) {
                if (!Schema::hasColumn('kasbons', 'no_bs')) {
                    $table->string('no_bs', 50)->nullable()->after('no_kasbon');
                }
                if (!Schema::hasColumn('kasbons', 'nominal_bs')) {
                    $table->decimal('nominal_bs', 15, 2)->default(0)->after('nominal');
                }
                if (!Schema::hasColumn('kasbons', 'nominal_realisasi')) {
                    $table->decimal('nominal_realisasi', 15, 2)->default(0)->after('nominal_bs');
                }
                if (!Schema::hasColumn('kasbons', 'selisih_kembali_tunai')) {
                    $table->decimal('selisih_kembali_tunai', 15, 2)->default(0)->after('nominal_realisasi');
                }
                if (!Schema::hasColumn('kasbons', 'no_bkkc_ref')) {
                    $table->string('no_bkkc_ref', 50)->nullable()->after('selisih_kembali_tunai');
                }
                if (!Schema::hasColumn('kasbons', 'no_lkk_ref')) {
                    $table->string('no_lkk_ref', 50)->nullable()->after('no_bkkc_ref');
                }
                if (!Schema::hasColumn('kasbons', 'ttd_user')) {
                    $table->string('ttd_user', 100)->nullable()->after('approved_at');
                }
                if (!Schema::hasColumn('kasbons', 'ttd_kasir')) {
                    $table->string('ttd_kasir', 100)->nullable()->after('ttd_user');
                }
                if (!Schema::hasColumn('kasbons', 'ttd_manajer')) {
                    $table->string('ttd_manajer', 100)->nullable()->after('ttd_kasir');
                }
            });
        }

        // 5. Penambahan kolom SOP Re-open Berita Acara pada shift_kasirs
        if (Schema::hasTable('shift_kasirs')) {
            Schema::table('shift_kasirs', function (Blueprint $table) {
                if (!Schema::hasColumn('shift_kasirs', 'reopen_requested')) {
                    $table->boolean('reopen_requested')->default(false)->after('status');
                }
                if (!Schema::hasColumn('shift_kasirs', 'berita_acara_reopen')) {
                    $table->text('berita_acara_reopen')->nullable()->after('reopen_reason');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('shift_kasirs')) {
            Schema::table('shift_kasirs', function (Blueprint $table) {
                foreach (['reopen_requested', 'berita_acara_reopen'] as $col) {
                    if (Schema::hasColumn('shift_kasirs', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('kasbons')) {
            Schema::table('kasbons', function (Blueprint $table) {
                foreach ([
                    'no_bs',
                    'nominal_bs',
                    'nominal_realisasi',
                    'selisih_kembali_tunai',
                    'no_bkkc_ref',
                    'no_lkk_ref',
                    'ttd_user',
                    'ttd_kasir',
                    'ttd_manajer',
                ] as $col) {
                    if (Schema::hasColumn('kasbons', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('bukti_pengeluarans')) {
            Schema::table('bukti_pengeluarans', function (Blueprint $table) {
                foreach (['no_dp', 'no_pr', 'no_po', 'no_pum', 'catatan_penolakan'] as $col) {
                    if (Schema::hasColumn('bukti_pengeluarans', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('kas_keluars')) {
            Schema::table('kas_keluars', function (Blueprint $table) {
                foreach (['jenis_dokumen', 'no_dp', 'no_pr', 'no_po', 'no_pum', 'no_cek', 'catatan_penolakan'] as $col) {
                    if (Schema::hasColumn('kas_keluars', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('kas_masuks')) {
            Schema::table('kas_masuks', function (Blueprint $table) {
                foreach ([
                    'jenis_dokumen',
                    'is_cicilan',
                    'no_kuitansi_sementara',
                    'bulan_faktur_asal',
                    'tahun_faktur_asal',
                    'manager_otorisasi',
                ] as $col) {
                    if (Schema::hasColumn('kas_masuks', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};

