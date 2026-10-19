<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('kop_kota')) {
            return;
        }

        // 1. Definisikan 8 Halaman Otentik Harian Umum Kabar Priangan sesuai koran fisik & docs/KP PDF 09 Oktober 2026.pdf
        $newPages = [
            [
                'kode'             => 'HAL1',
                'nomor_halaman'    => 1,
                'nama_penempatan'  => 'Halaman 1 (Halaman Depan / Cover)',
                'wilayah_biro'     => 'Umum / Headline',
                'surcharge_persen' => 50.00,
                'is_active'        => true,
            ],
            [
                'kode'             => 'HAL2_GRT_SMD',
                'nomor_halaman'    => 2,
                'nama_penempatan'  => 'Halaman 2 (Garut - Sumedang)',
                'wilayah_biro'     => 'Garut - Sumedang',
                'surcharge_persen' => 0.00,
                'is_active'        => true,
            ],
            [
                'kode'             => 'HAL3_KAB_TSK',
                'nomor_halaman'    => 3,
                'nama_penempatan'  => 'Halaman 3 (Kab. Tasikmalaya)',
                'wilayah_biro'     => 'Kab. Tasikmalaya',
                'surcharge_persen' => 0.00,
                'is_active'        => true,
            ],
            [
                'kode'             => 'HAL4_CMS',
                'nomor_halaman'    => 4,
                'nama_penempatan'  => 'Halaman 4 (Ciamis)',
                'wilayah_biro'     => 'Ciamis',
                'surcharge_persen' => 0.00,
                'is_active'        => true,
            ],
            [
                'kode'             => 'HAL5_BJR_PNG',
                'nomor_halaman'    => 5,
                'nama_penempatan'  => 'Halaman 5 (Banjar - Pangandaran)',
                'wilayah_biro'     => 'Banjar - Pangandaran',
                'surcharge_persen' => 0.00,
                'is_active'        => true,
            ],
            [
                'kode'             => 'HAL6_KOTA_TSK',
                'nomor_halaman'    => 6,
                'nama_penempatan'  => 'Halaman 6 (Kota Tasikmalaya)',
                'wilayah_biro'     => 'Kota Tasikmalaya',
                'surcharge_persen' => 0.00,
                'is_active'        => true,
            ],
            [
                'kode'             => 'HAL7_PRIANGAN',
                'nomor_halaman'    => 7,
                'nama_penempatan'  => 'Halaman 7 (Kabar Priangan / Ragam)',
                'wilayah_biro'     => 'Umum',
                'surcharge_persen' => 0.00,
                'is_active'        => true,
            ],
            [
                'kode'             => 'HAL8_BLKG',
                'nomor_halaman'    => 8,
                'nama_penempatan'  => 'Halaman 8 (Halaman Belakang / Back Cover)',
                'wilayah_biro'     => 'Umum / Khusus',
                'surcharge_persen' => 25.00,
                'is_active'        => true,
            ],
            [
                'kode'             => 'HAL_DALAM',
                'nomor_halaman'    => 9,
                'nama_penempatan'  => 'Halaman Dalam (Reguler)',
                'wilayah_biro'     => 'Umum',
                'surcharge_persen' => 0.00,
                'is_active'        => true,
            ],
        ];

        // Buat atau perbarui entri halaman baru
        foreach ($newPages as $p) {
            $existing = DB::table('kop_kota')->where('kode', $p['kode'])->first();
            if ($existing) {
                DB::table('kop_kota')->where('id', $existing->id)->update([
                    'nomor_halaman'    => $p['nomor_halaman'],
                    'nama_penempatan'  => $p['nama_penempatan'],
                    'wilayah_biro'     => $p['wilayah_biro'],
                    'surcharge_persen' => $p['surcharge_persen'],
                    'is_active'        => true,
                    'updated_at'       => now(),
                ]);
            } else {
                DB::table('kop_kota')->insert([
                    'kode'             => $p['kode'],
                    'nomor_halaman'    => $p['nomor_halaman'],
                    'nama_penempatan'  => $p['nama_penempatan'],
                    'wilayah_biro'     => $p['wilayah_biro'],
                    'surcharge_persen' => $p['surcharge_persen'],
                    'is_active'        => true,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
        }

        // Ambil pemetaan ID baru
        $mapId = DB::table('kop_kota')->pluck('id', 'kode')->toArray();

        // Pemetaan dari kode lama ke kode baru yang sesuai edisi koran asli
        $legacyMapping = [
            'HAL5_SMD'  => 'HAL2_GRT_SMD',   // Sumedang ada di Hal 2 (Garut - Sumedang)
            'HAL7_TSK'  => 'HAL6_KOTA_TSK',   // Tasikmalaya kota di Hal 6
            'HAL8_CMS'  => 'HAL4_CMS',        // Ciamis ada di Hal 4
            'HAL9_BJR'  => 'HAL5_BJR_PNG',    // Banjar ada di Hal 5
            'HAL10_GRT' => 'HAL2_GRT_SMD',   // Garut ada di Hal 2
            'HAL_BLKG'  => 'HAL8_BLKG',       // Back cover sekarang Hal 8 (total 8 halaman)
        ];

        foreach ($legacyMapping as $oldCode => $newCode) {
            $oldRec = DB::table('kop_kota')->where('kode', $oldCode)->first();
            if ($oldRec && isset($mapId[$newCode]) && $oldRec->id !== $mapId[$newCode]) {
                // Remap transaksi koran yang menggunakan kop_kota_id lama
                if (Schema::hasTable('transaksikoran')) {
                    DB::table('transaksikoran')
                        ->where('kop_kota_id', $oldRec->id)
                        ->update(['kop_kota_id' => $mapId[$newCode]]);
                }
                // Hapus record lama yang usang
                DB::table('kop_kota')->where('id', $oldRec->id)->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tetap dipertahankan karena merupakan standar struktur cetak 8 halaman
    }
};
