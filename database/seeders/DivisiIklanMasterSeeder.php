<?php

namespace Database\Seeders;

use App\Models\JenisIklan;
use App\Models\KategoriMedia;
use App\Models\KopIklan;
use App\Models\KopKota;
use App\Models\SalesAe;
use Illuminate\Database\Seeder;

class DivisiIklanMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan Kategori Media Koran ada
        $krnMedia = KategoriMedia::firstOrCreate(
            ['kode' => 'KRN'],
            ['nama' => 'Koran Cetak', 'deskripsi' => 'Harian Umum Kabar Priangan', 'is_active' => true]
        );

        // 2. Seed Master KOP KOTA (Penempatan Halaman Koran & Wilayah Biro) - Sesuai Koran Fisik 8 Halaman
        $kopKotas = [
            [
                'kode'             => 'HAL1',
                'nomor_halaman'    => 1,
                'nama_penempatan'  => 'Halaman 1 (Halaman Depan / Cover)',
                'wilayah_biro'     => 'Umum / Headline',
                'surcharge_persen' => 50.00,
            ],
            [
                'kode'             => 'HAL2_GRT_SMD',
                'nomor_halaman'    => 2,
                'nama_penempatan'  => 'Halaman 2 (Garut - Sumedang)',
                'wilayah_biro'     => 'Garut - Sumedang',
                'surcharge_persen' => 0.00,
            ],
            [
                'kode'             => 'HAL3_KAB_TSK',
                'nomor_halaman'    => 3,
                'nama_penempatan'  => 'Halaman 3 (Kab. Tasikmalaya)',
                'wilayah_biro'     => 'Kab. Tasikmalaya',
                'surcharge_persen' => 0.00,
            ],
            [
                'kode'             => 'HAL4_CMS',
                'nomor_halaman'    => 4,
                'nama_penempatan'  => 'Halaman 4 (Ciamis)',
                'wilayah_biro'     => 'Ciamis',
                'surcharge_persen' => 0.00,
            ],
            [
                'kode'             => 'HAL5_BJR_PNG',
                'nomor_halaman'    => 5,
                'nama_penempatan'  => 'Halaman 5 (Banjar - Pangandaran)',
                'wilayah_biro'     => 'Banjar - Pangandaran',
                'surcharge_persen' => 0.00,
            ],
            [
                'kode'             => 'HAL6_KOTA_TSK',
                'nomor_halaman'    => 6,
                'nama_penempatan'  => 'Halaman 6 (Kota Tasikmalaya)',
                'wilayah_biro'     => 'Kota Tasikmalaya',
                'surcharge_persen' => 0.00,
            ],
            [
                'kode'             => 'HAL7_PRIANGAN',
                'nomor_halaman'    => 7,
                'nama_penempatan'  => 'Halaman 7 (Kabar Priangan / Ragam)',
                'wilayah_biro'     => 'Umum',
                'surcharge_persen' => 0.00,
            ],
            [
                'kode'             => 'HAL8_BLKG',
                'nomor_halaman'    => 8,
                'nama_penempatan'  => 'Halaman 8 (Halaman Belakang / Back Cover)',
                'wilayah_biro'     => 'Umum / Khusus',
                'surcharge_persen' => 25.00,
            ],
            [
                'kode'             => 'HAL_DALAM',
                'nomor_halaman'    => 9,
                'nama_penempatan'  => 'Halaman Dalam (Reguler)',
                'wilayah_biro'     => 'Umum',
                'surcharge_persen' => 0.00,
            ],
        ];

        foreach ($kopKotas as $item) {
            KopKota::updateOrCreate(['kode' => $item['kode']], $item);
        }

        // 3. Seed Master KOP IKLAN (Rubrik Kategori Iklan Baris)
        $kopIklans = [
            ['kode' => 'HILANG',   'nama_kop' => 'Kehilangan (STNK/BPKB/Sertifikat)', 'urutan' => 1],
            ['kode' => 'OTO',      'nama_kop' => 'Otomotif (Mobil & Motor)',          'urutan' => 2],
            ['kode' => 'PROPERTI', 'nama_kop' => 'Properti (Rumah & Tanah Dijual)',   'urutan' => 3],
            ['kode' => 'LOKER',    'nama_kop' => 'Lowongan Kerja & Karir',            'urutan' => 4],
            ['kode' => 'JASA',     'nama_kop' => 'Jasa & Aneka Pelayanan',            'urutan' => 5],
            ['kode' => 'LELANG',   'nama_kop' => 'Pengumuman / Lelang Pengadilan',   'urutan' => 6],
            ['kode' => 'ANEKA',    'nama_kop' => 'Aneka Kebutuhan Lainnya',           'urutan' => 7],
        ];

        foreach ($kopIklans as $item) {
            KopIklan::updateOrCreate(['kode' => $item['kode']], $item);
        }

        // 4. Seed Master SALES / AE (Account Executive)
        $salesList = [
            [
                'kode_ae'                => 'FO',
                'nama_ae'                => 'Front Office (Walk-in Counter)',
                'jenis_kode'             => 'FO',
                'tarif_insentif_default' => 0.00,
                'tarif_komisi_max'       => 0.00,
            ],
            [
                'kode_ae'                => 'S67',
                'nama_ae'                => 'Suhita Dewi',
                'jenis_kode'             => 'STRUKTURAL',
                'tarif_insentif_default' => 5.00,
                'tarif_komisi_max'       => 20.00,
            ],
            [
                'kode_ae'                => 'S12',
                'nama_ae'                => 'Ateng Mulyadi',
                'jenis_kode'             => 'STRUKTURAL',
                'tarif_insentif_default' => 5.00,
                'tarif_komisi_max'       => 20.00,
            ],
            [
                'kode_ae'                => 'K05',
                'nama_ae'                => 'Budi Santoso',
                'jenis_kode'             => 'KORESPONDEN',
                'tarif_insentif_default' => 7.50,
                'tarif_komisi_max'       => 20.00,
            ],
            [
                'kode_ae'                => 'K12',
                'nama_ae'                => 'Maman Kasir',
                'jenis_kode'             => 'KORESPONDEN',
                'tarif_insentif_default' => 5.00,
                'tarif_komisi_max'       => 20.00,
            ],
        ];

        foreach ($salesList as $item) {
            SalesAe::updateOrCreate(['kode_ae' => $item['kode_ae']], $item);
        }

        // 5. Seed / Update Master Produk JENIS IKLAN (Unified Product Catalog Kabar Priangan)
        $produkKoran = [
            [
                'kode_jenis'        => 'BARIS',
                'nama_jenis'        => 'Iklan Baris (Classified)',
                'tipe_dasar'        => 'BARIS',
                'mode_warna'        => 'BW',
                'cara_hitung'       => 'PER_BARIS',
                'tarif_dasar'       => 22750,
                'is_fixed_size'     => true,
                'default_kolom'     => 1,
                'default_tinggi_mm' => 0,
                'max_kolom'         => 1,
                'max_tinggi_mm'     => 0,
                'min_order'         => 2,
                'keterangan'        => 'Tarif per baris naskah. Minimal 2 baris (maks 7 baris). 32 karakter per baris.',
            ],
            [
                'kode_jenis'        => 'KLM_1X40',
                'nama_jenis'        => 'Iklan Kolom BW (1 Kolom × 40 mm)',
                'tipe_dasar'        => 'KOLOM',
                'mode_warna'        => 'BW',
                'cara_hitung'       => 'PAKET_FIXED',
                'tarif_dasar'       => 360000,
                'is_fixed_size'     => true,
                'default_kolom'     => 1,
                'default_tinggi_mm' => 40,
                'max_kolom'         => 1,
                'max_tinggi_mm'     => 40,
                'min_order'         => 1,
                'keterangan'        => 'Paket kolom mini hitam putih 40 mmk (1x40 mm).',
            ],
            [
                'kode_jenis'        => 'KLM_BW_1X50',
                'nama_jenis'        => 'Iklan Kolom BW (1 Kolom × 50 mm)',
                'tipe_dasar'        => 'KOLOM',
                'mode_warna'        => 'BW',
                'cara_hitung'       => 'PAKET_FIXED',
                'tarif_dasar'       => 450000,
                'is_fixed_size'     => true,
                'default_kolom'     => 1,
                'default_tinggi_mm' => 50,
                'max_kolom'         => 1,
                'max_tinggi_mm'     => 50,
                'min_order'         => 1,
                'keterangan'        => 'Paket kolom mini hitam putih 50 mmk (1x50 mm).',
            ],
            [
                'kode_jenis'        => 'KLM_1X75',
                'nama_jenis'        => 'Iklan Kolom BW (1 Kolom × 75 mm)',
                'tipe_dasar'        => 'KOLOM',
                'mode_warna'        => 'BW',
                'cara_hitung'       => 'PAKET_FIXED',
                'tarif_dasar'       => 675000,
                'is_fixed_size'     => true,
                'default_kolom'     => 1,
                'default_tinggi_mm' => 75,
                'max_kolom'         => 1,
                'max_tinggi_mm'     => 75,
                'min_order'         => 1,
                'keterangan'        => 'Paket kolom hitam putih 75 mmk (1x75 mm).',
            ],
            [
                'kode_jenis'        => 'KLM_1X100',
                'nama_jenis'        => 'Iklan Kolom BW (1 Kolom × 100 mm)',
                'tipe_dasar'        => 'KOLOM',
                'mode_warna'        => 'BW',
                'cara_hitung'       => 'PAKET_FIXED',
                'tarif_dasar'       => 900000,
                'is_fixed_size'     => true,
                'default_kolom'     => 1,
                'default_tinggi_mm' => 100,
                'max_kolom'         => 1,
                'max_tinggi_mm'     => 100,
                'min_order'         => 1,
                'keterangan'        => 'Paket kolom hitam putih 100 mmk (1x100 mm).',
            ],
            [
                'kode_jenis'        => 'KLM_BW_2X100',
                'nama_jenis'        => 'Iklan Kolom BW (2 Kolom × 100 mm)',
                'tipe_dasar'        => 'KOLOM',
                'mode_warna'        => 'BW',
                'cara_hitung'       => 'PAKET_FIXED',
                'tarif_dasar'       => 1600000,
                'is_fixed_size'     => true,
                'default_kolom'     => 2,
                'default_tinggi_mm' => 100,
                'max_kolom'         => 2,
                'max_tinggi_mm'     => 100,
                'min_order'         => 1,
                'keterangan'        => 'Paket kolom standar hitam putih 200 mmk (2x100 mm).',
            ],
            [
                'kode_jenis'        => 'KLM_2X150',
                'nama_jenis'        => 'Iklan Kolom BW (2 Kolom × 150 mm)',
                'tipe_dasar'        => 'KOLOM',
                'mode_warna'        => 'BW',
                'cara_hitung'       => 'PAKET_FIXED',
                'tarif_dasar'       => 2400000,
                'is_fixed_size'     => true,
                'default_kolom'     => 2,
                'default_tinggi_mm' => 150,
                'max_kolom'         => 2,
                'max_tinggi_mm'     => 150,
                'min_order'         => 1,
                'keterangan'        => 'Paket kolom besar hitam putih 300 mmk (2x150 mm).',
            ],
            [
                'kode_jenis'        => 'DISP_BW_CUST',
                'nama_jenis'        => 'Iklan Display Hitam Putih (BW)',
                'tipe_dasar'        => 'DISPLAY',
                'mode_warna'        => 'BW',
                'cara_hitung'       => 'PER_MMK',
                'tarif_dasar'       => 12000,
                'is_fixed_size'     => false,
                'default_kolom'     => 2,
                'default_tinggi_mm' => 100,
                'max_kolom'         => 7,
                'max_tinggi_mm'     => 540,
                'min_order'         => 1,
                'keterangan'        => 'Tarif per milimeter kolom (mmk) Hitam Putih (BW). Ukuran bebas max 7 kolom x 540 mm.',
            ],
            [
                'kode_jenis'        => 'DISP_FC_CUST',
                'nama_jenis'        => 'Iklan Display Full Color (FC)',
                'tipe_dasar'        => 'DISPLAY',
                'mode_warna'        => 'FC',
                'cara_hitung'       => 'PER_MMK',
                'tarif_dasar'       => 24000,
                'is_fixed_size'     => false,
                'default_kolom'     => 2,
                'default_tinggi_mm' => 100,
                'max_kolom'         => 7,
                'max_tinggi_mm'     => 540,
                'min_order'         => 1,
                'keterangan'        => 'Tarif per milimeter kolom (mmk) Full Color (FC). Ukuran bebas max 7 kolom x 540 mm.',
            ],
            [
                'kode_jenis'        => 'KUPING_H1_FC',
                'nama_jenis'        => 'Iklan Display Kuping H1 Full Color (2×50 mm)',
                'tipe_dasar'        => 'DISPLAY',
                'mode_warna'        => 'FC',
                'cara_hitung'       => 'PAKET_FIXED',
                'tarif_dasar'       => 3500000,
                'is_fixed_size'     => true,
                'default_kolom'     => 2,
                'default_tinggi_mm' => 50,
                'max_kolom'         => 2,
                'max_tinggi_mm'     => 50,
                'min_order'         => 1,
                'keterangan'        => 'Posisi premium di samping kepala/logo koran Halaman 1.',
            ],
            [
                'kode_jenis'        => 'ADV_HAL_FC',
                'nama_jenis'        => 'Advertorial 1 Halaman Penuh FC',
                'tipe_dasar'        => 'ADVERTORIAL',
                'mode_warna'        => 'FC',
                'cara_hitung'       => 'PAKET_FIXED',
                'tarif_dasar'       => 25000000,
                'is_fixed_size'     => true,
                'default_kolom'     => 7,
                'default_tinggi_mm' => 540,
                'max_kolom'         => 7,
                'max_tinggi_mm'     => 540,
                'min_order'         => 1,
                'keterangan'        => 'Paket advertorial naratif 1 halaman koran penuh 7 kolom x 540 mm Full Color.',
            ],
            [
                'kode_jenis'        => 'ADV_SET_FC',
                'nama_jenis'        => 'Advertorial 1/2 Halaman FC (7×270 mm)',
                'tipe_dasar'        => 'ADVERTORIAL',
                'mode_warna'        => 'FC',
                'cara_hitung'       => 'PAKET_FIXED',
                'tarif_dasar'       => 13500000,
                'is_fixed_size'     => true,
                'default_kolom'     => 7,
                'default_tinggi_mm' => 270,
                'max_kolom'         => 7,
                'max_tinggi_mm'     => 270,
                'min_order'         => 1,
                'keterangan'        => 'Paket advertorial naratif 1/2 halaman koran 7 kolom x 270 mm Full Color.',
            ],
            [
                'kode_jenis'        => 'DISP_BONUS',
                'nama_jenis'        => 'Iklan Layanan Masyarakat / Bonus / Barter',
                'tipe_dasar'        => 'BONUS',
                'mode_warna'        => 'FC',
                'cara_hitung'       => 'FIXED_ZERO',
                'tarif_dasar'       => 0,
                'is_fixed_size'     => false,
                'default_kolom'     => 2,
                'default_tinggi_mm' => 100,
                'max_kolom'         => 7,
                'max_tinggi_mm'     => 540,
                'min_order'         => 1,
                'keterangan'        => 'Iklan sosial, layanan masyarakat, CSR, barter, atau bonus pemuatan Rp 0.',
            ],
        ];

        foreach ($produkKoran as $item) {
            $item['kategori_media_id'] = $krnMedia->id;
            $item['is_active'] = true;
            JenisIklan::updateOrCreate(['kode_jenis' => $item['kode_jenis']], $item);
        }
    }
}
