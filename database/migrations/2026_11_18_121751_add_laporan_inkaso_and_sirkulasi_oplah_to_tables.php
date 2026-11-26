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
        // 1. Tambah Kolom Laporan Hasil Penugasan Lapangan pada penugasan_inkaso
        Schema::table('penugasan_inkaso', function (Blueprint $table) {
            if (!Schema::hasColumn('penugasan_inkaso', 'tanggal_lapor')) {
                $table->date('tanggal_lapor')->nullable()->after('tanggal_tugas');
            }
            if (!Schema::hasColumn('penugasan_inkaso', 'hasil_kunjungan')) {
                $table->string('hasil_kunjungan', 50)->nullable()->after('status_tugas');
            }
            if (!Schema::hasColumn('penugasan_inkaso', 'tanggal_janji_bayar')) {
                $table->date('tanggal_janji_bayar')->nullable()->after('hasil_kunjungan');
            }
            if (!Schema::hasColumn('penugasan_inkaso', 'nominal_diterima')) {
                $table->decimal('nominal_diterima', 15, 2)->default(0)->after('tanggal_janji_bayar');
            }
            if (!Schema::hasColumn('penugasan_inkaso', 'keterangan_hasil')) {
                $table->text('keterangan_hasil')->nullable()->after('catatan');
            }
            if (!Schema::hasColumn('penugasan_inkaso', 'dilaporkan_oleh')) {
                $table->unsignedBigInteger('dilaporkan_oleh')->nullable()->after('keterangan_hasil');
            }
        });

        // 2. Tambah Kolom Oplah Sirkulasi (Langganan, Eceran, Gratis, Retur) & Bonus Iklan pada faktur
        Schema::table('faktur', function (Blueprint $table) {
            if (!Schema::hasColumn('faktur', 'langganan_eks')) {
                $table->integer('langganan_eks')->default(0)->after('rincian_biaya');
            }
            if (!Schema::hasColumn('faktur', 'eceran_eks')) {
                $table->integer('eceran_eks')->default(0)->after('langganan_eks');
            }
            if (!Schema::hasColumn('faktur', 'gratis_eks')) {
                $table->integer('gratis_eks')->default(0)->after('eceran_eks');
            }
            if (!Schema::hasColumn('faktur', 'retur_eks')) {
                $table->integer('retur_eks')->default(0)->after('gratis_eks');
            }
            if (!Schema::hasColumn('faktur', 'retur_nominal')) {
                $table->decimal('retur_nominal', 15, 2)->default(0)->after('retur_eks');
            }
            if (!Schema::hasColumn('faktur', 'saldo_awal_piutang')) {
                $table->decimal('saldo_awal_piutang', 15, 2)->default(0)->after('retur_nominal');
            }
            if (!Schema::hasColumn('faktur', 'kh_insentif_nominal')) {
                $table->decimal('kh_insentif_nominal', 15, 2)->default(0)->after('siap_cair_kh_insentif');
            }
            if (!Schema::hasColumn('faktur', 'is_bonus')) {
                $table->boolean('is_bonus')->default(false)->after('kh_insentif_nominal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penugasan_inkaso', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_lapor',
                'hasil_kunjungan',
                'tanggal_janji_bayar',
                'nominal_diterima',
                'keterangan_hasil',
                'dilaporkan_oleh',
            ]);
        });

        Schema::table('faktur', function (Blueprint $table) {
            $table->dropColumn([
                'langganan_eks',
                'eceran_eks',
                'gratis_eks',
                'retur_eks',
                'retur_nominal',
                'saldo_awal_piutang',
                'kh_insentif_nominal',
                'is_bonus',
            ]);
        });
    }
};
