<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriMedia;
use App\Models\JenisIklan;
use Illuminate\Support\Facades\DB;

class KategoriDanJenisIklanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat 3 Kategori Media Utama
        $mediaList = [
            [
                'kode' => 'KRN',
                'nama' => 'Koran Cetak',
                'deskripsi' => 'Penerbitan iklan cetak Harian Umum Kabar Priangan edisi reguler dan weekend.',
            ],
            [
                'kode' => 'ONL',
                'nama' => 'Media Online',
                'deskripsi' => 'Portal digital kabarpriangan.pikiran-rakyat.com dan jejaring media sosial.',
            ],
            [
                'kode' => 'PTV',
                'nama' => 'Priangan TV',
                'deskripsi' => 'Saluran siaran televisi lokal Priangan TV (video ads, liputan, running text).',
            ],
        ];

        $mediaModels = [];
        foreach ($mediaList as $item) {
            $mediaModels[$item['kode']] = KategoriMedia::firstOrCreate(
                ['kode' => $item['kode']],
                [
                    'nama' => $item['nama'],
                    'deskripsi' => $item['deskripsi'],
                    'is_active' => true,
                ]
            );
        }

        // 2. Data Jenis / Paket Iklan untuk Koran Cetak
        $koranPaket = [
            ['kode' => 'KRN001', 'nama' => 'Iklan Baris', 'tarif' => 35000],
            ['kode' => 'KRN002', 'nama' => 'Iklan Kolom (Hitam Putih / BW)', 'tarif' => 150000],
            ['kode' => 'KRN003', 'nama' => 'Iklan Kolom (Full Color / FC)', 'tarif' => 300000],
            ['kode' => 'KRN004', 'nama' => 'Iklan Display Halaman 1 (Cover Depan)', 'tarif' => 1500000],
            ['kode' => 'KRN005', 'nama' => 'Iklan Advertorial Cetak (1 Halaman Penuh)', 'tarif' => 4500000],
            ['kode' => 'KRN006', 'nama' => 'Iklan Duka Cita & Ucapan Selamat', 'tarif' => 500000],
        ];

        // Jika tabel iklankoran lama memiliki data, kita gabungkan
        if (DB::getSchemaBuilder()->hasTable('iklankoran')) {
            $oldKoran = DB::table('iklankoran')->get();
            foreach ($oldKoran as $k) {
                if (!collect($koranPaket)->pluck('kode')->contains($k->kode_iklankoran)) {
                    $koranPaket[] = [
                        'kode' => $k->kode_iklankoran,
                        'nama' => $k->jenis_iklankoran,
                        'tarif' => 100000,
                    ];
                }
            }
        }

        foreach ($koranPaket as $p) {
            JenisIklan::firstOrCreate(
                ['kode_jenis' => $p['kode']],
                [
                    'kategori_media_id' => $mediaModels['KRN']->id,
                    'nama_jenis' => $p['nama'],
                    'tarif_dasar' => $p['tarif'],
                    'is_active' => true,
                ]
            );
        }

        // 3. Data Jenis Iklan untuk Media Online
        $onlinePaket = [
            ['kode' => 'ONL001', 'nama' => 'Banner Header (Leaderboard)', 'tarif' => 1500000],
            ['kode' => 'ONL002', 'nama' => 'Banner Sidebar (Medium Rectangle)', 'tarif' => 1000000],
            ['kode' => 'ONL003', 'nama' => 'Advertorial / Rilis Berita Online', 'tarif' => 750000],
            ['kode' => 'ONL004', 'nama' => 'Social Media Post (Instagram / FB)', 'tarif' => 500000],
            ['kode' => 'ONL005', 'nama' => 'Video Ads / Reels / TikTok', 'tarif' => 1250000],
        ];

        if (DB::getSchemaBuilder()->hasTable('iklanonline')) {
            $oldOnline = DB::table('iklanonline')->get();
            foreach ($oldOnline as $o) {
                if (!collect($onlinePaket)->pluck('kode')->contains($o->kode_iklanonline)) {
                    $onlinePaket[] = [
                        'kode' => $o->kode_iklanonline,
                        'nama' => $o->jenis_iklanonline,
                        'tarif' => 500000,
                    ];
                }
            }
        }

        foreach ($onlinePaket as $p) {
            JenisIklan::firstOrCreate(
                ['kode_jenis' => $p['kode']],
                [
                    'kategori_media_id' => $mediaModels['ONL']->id,
                    'nama_jenis' => $p['nama'],
                    'tarif_dasar' => $p['tarif'],
                    'is_active' => true,
                ]
            );
        }

        // 4. Data Jenis Iklan untuk Priangan TV
        $tvPaket = [
            ['kode' => 'PTV001', 'nama' => 'Video Ads Commercial (30 Detik)', 'tarif' => 2000000],
            ['kode' => 'PTV002', 'nama' => 'Running Text Berita', 'tarif' => 350000],
            ['kode' => 'PTV003', 'nama' => 'Liputan Khusus / Talkshow', 'tarif' => 3500000],
            ['kode' => 'PTV004', 'nama' => 'Bumper In / Out Program Acara', 'tarif' => 1500000],
            ['kode' => 'PTV005', 'nama' => 'Sponsoring Program Acara', 'tarif' => 5000000],
        ];

        if (DB::getSchemaBuilder()->hasTable('iklanpriangan')) {
            $oldPriangan = DB::table('iklanpriangan')->get();
            foreach ($oldPriangan as $tv) {
                if (!collect($tvPaket)->pluck('kode')->contains($tv->kode_iklanpriangan)) {
                    $tvPaket[] = [
                        'kode' => $tv->kode_iklanpriangan,
                        'nama' => $tv->jenis_iklanpriangan,
                        'tarif' => 1000000,
                    ];
                }
            }
        }

        foreach ($tvPaket as $p) {
            JenisIklan::firstOrCreate(
                ['kode_jenis' => $p['kode']],
                [
                    'kategori_media_id' => $mediaModels['PTV']->id,
                    'nama_jenis' => $p['nama'],
                    'tarif_dasar' => $p['tarif'],
                    'is_active' => true,
                ]
            );
        }
    }
}
