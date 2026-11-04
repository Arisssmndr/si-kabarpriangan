<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memo_unlocks', function (Blueprint $table) {
            if (!Schema::hasColumn('memo_unlocks', 'kategori_urgensi')) {
                $table->string('kategori_urgensi', 100)->default('Iklan Mendesak / Susulan')->after('nomor_memo');
            }
            if (!Schema::hasColumn('memo_unlocks', 'nama_pemasang')) {
                $table->string('nama_pemasang', 150)->nullable()->after('kategori_urgensi');
            }
            if (!Schema::hasColumn('memo_unlocks', 'spesifikasi_iklan')) {
                $table->string('spesifikasi_iklan', 150)->nullable()->after('nama_pemasang');
            }
            if (!Schema::hasColumn('memo_unlocks', 'estimasi_nominal')) {
                $table->bigInteger('estimasi_nominal')->default(0)->after('spesifikasi_iklan');
            }
            if (!Schema::hasColumn('memo_unlocks', 'tujuan_divisi')) {
                $table->string('tujuan_divisi', 100)->default('Divisi Akuntansi & Faktur')->after('alasan');
            }
            if (!Schema::hasColumn('memo_unlocks', 'status_akuntansi')) {
                $table->string('status_akuntansi', 30)->default('TERKIRIM')->after('tujuan_divisi');
            }
            if (!Schema::hasColumn('memo_unlocks', 'diketahui_oleh')) {
                $table->string('diketahui_oleh', 100)->nullable()->default('Shinta Dewi, SH., M.H.')->after('status_akuntansi');
            }
            if (!Schema::hasColumn('memo_unlocks', 'diterima_akuntansi_pada')) {
                $table->timestamp('diterima_akuntansi_pada')->nullable()->after('ditutup_kembali_pada');
            }
        });
    }

    public function down(): void
    {
        Schema::table('memo_unlocks', function (Blueprint $table) {
            $cols = [
                'kategori_urgensi',
                'nama_pemasang',
                'spesifikasi_iklan',
                'estimasi_nominal',
                'tujuan_divisi',
                'status_akuntansi',
                'diketahui_oleh',
                'diterima_akuntansi_pada',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('memo_unlocks', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

