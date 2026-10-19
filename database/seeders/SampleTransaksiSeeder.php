<?php

namespace Database\Seeders;

use App\Models\JenisIklan;
use App\Models\KopIklan;
use App\Models\KopKota;
use App\Models\OrderJadwalMuat;
use App\Models\Pelanggan;
use App\Models\SalesAe;
use App\Models\TransaksiKoran;
use App\Models\TransaksiOnline;
use App\Models\TransaksiPriangan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SampleTransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::now()->toDateString();
        $yesterday = Carbon::now()->subDay()->toDateString();
        $threeDaysAgo = Carbon::now()->subDays(3)->toDateString();
        $fiveDaysAgo = Carbon::now()->subDays(5)->toDateString();
        $tenDaysAgo = Carbon::now()->subDays(10)->toDateString();

        // 1. KORAN CETAK (Sinkron dengan Master Database)
        $plgPemkot = Pelanggan::where('kode_pelanggan', 'PLG-0001')->first();
        $plgAgensi = Pelanggan::where('kode_pelanggan', 'PLG-0002')->first();
        $plgCiamis = Pelanggan::where('kode_pelanggan', 'PLG-0003')->first();
        $plgBjb    = Pelanggan::where('kode_pelanggan', 'PLG-0004')->first();
        $plgWalkin = Pelanggan::where('kode_pelanggan', 'PLG-0005')->first();

        $aeFo     = SalesAe::where('kode_ae', 'FO')->first();
        $aeSuhita = SalesAe::where('kode_ae', 'S67')->first() ?? $aeFo;
        $aeAteng  = SalesAe::where('kode_ae', 'S12')->first() ?? $aeFo;
        $aeBudi   = SalesAe::where('kode_ae', 'K05')->first() ?? $aeFo;

        $halCover = KopKota::where('kode', 'HAL1')->first();
        $halTsk   = KopKota::where('kode', 'HAL7_TSK')->first();
        $halCms   = KopKota::where('kode', 'HAL8_CMS')->first();
        $halSmd   = KopKota::where('kode', 'HAL5_SMD')->first();

        $kopHilang = KopIklan::where('kode', 'HILANG')->first();

        $pkgBaris      = JenisIklan::where('kode_jenis', 'BARIS')->first();
        $pkgKolom2x100 = JenisIklan::where('kode_jenis', 'KLM_BW_2X100')->first();
        $pkgKolom1x50  = JenisIklan::where('kode_jenis', 'KLM_BW_1X50')->first();
        $pkgAdv        = JenisIklan::where('kode_jenis', 'ADV_HAL_FC')->first();
        $pkgDispFc     = JenisIklan::where('kode_jenis', 'DISP_FC_CUST')->first();

        $koranData = [
            [
                'nofakturkoran' => 'FKKRN001',
                'tanggal_transaksikoran' => $today,
                'pelanggan_id' => $plgPemkot?->id,
                'nama_pemasangkoran' => $plgPemkot?->nama_instansi ?? 'Bagian Protokol & Kompim Pemkot Tasikmalaya',
                'alamat_pemasangkoran' => $plgPemkot?->alamat ?? 'Jl. Letnan Harun No. 1, Kota Tasikmalaya',
                'id_iklankoran' => $pkgDispFc?->id ?? 18,
                'kategori_form' => 'DISPLAY',
                'halaman_iklan' => $halCover?->nama_penempatan ?? 'Halaman 1 (Halaman Depan / Cover)',
                'kop_kota_id' => $halCover?->id,
                'warna_iklan' => 'Full Color',
                'ukuran_iklan' => '4 Kolom × 150 mm',
                'lebar_kolom' => 4,
                'tinggi_mm' => 150,
                'tanggal_muatkoran' => $today,
                'sales_ae_id' => $aeSuhita?->id,
                'sales_iklankoran' => $aeSuhita ? "[{$aeSuhita->kode_ae}] {$aeSuhita->nama_ae}" : 'FO - Front Office',
                'total_muatkoran' => 1,
                'harga_transaksikoran' => 4500000,
                'diskon_transaksikoran' => 0,
                'insentif_transaksikoran' => 225000,
                'komisi_transaksikoran' => 900000,
                'ppn_transaksikoran' => 0,
                'totaltagihan_transaksikoran' => 4500000,
                'jumlahbayar_transaksikoran' => 4500000,
                'piutang_transaksikoran' => 0,
                'status_order' => 'Terbit',
            ],
            [
                'nofakturkoran' => 'FKKRN002',
                'tanggal_transaksikoran' => $today,
                'pelanggan_id' => $plgBjb?->id,
                'nama_pemasangkoran' => $plgBjb?->nama_instansi ?? 'PT Bank BJB Kantor Cabang Tasikmalaya',
                'alamat_pemasangkoran' => $plgBjb?->alamat ?? 'Jl. Mayor Utarya No. 20, Kota Tasikmalaya',
                'id_iklankoran' => $pkgKolom2x100?->id ?? 20,
                'kategori_form' => 'KOLOM',
                'halaman_iklan' => $halTsk?->nama_penempatan ?? 'Halaman 7 (Edisi Tasikmalaya)',
                'kop_kota_id' => $halTsk?->id,
                'warna_iklan' => 'Hitam Putih (BW)',
                'ukuran_iklan' => '2 Kolom × 100 mm',
                'lebar_kolom' => 2,
                'tinggi_mm' => 100,
                'tanggal_muatkoran' => $today,
                'sales_ae_id' => $aeAteng?->id,
                'sales_iklankoran' => $aeAteng ? "[{$aeAteng->kode_ae}] {$aeAteng->nama_ae}" : 'FO - Front Office',
                'total_muatkoran' => 1,
                'harga_transaksikoran' => 1600000,
                'diskon_transaksikoran' => 0,
                'insentif_transaksikoran' => 80000,
                'komisi_transaksikoran' => 320000,
                'ppn_transaksikoran' => 0,
                'totaltagihan_transaksikoran' => 1600000,
                'jumlahbayar_transaksikoran' => 1600000,
                'piutang_transaksikoran' => 0,
                'status_order' => 'Antrean_Pracetak',
            ],
            [
                'nofakturkoran' => 'FKKRN003',
                'tanggal_transaksikoran' => $yesterday,
                'pelanggan_id' => $plgAgensi?->id,
                'nama_pemasangkoran' => $plgAgensi?->nama_instansi ?? 'Biro Iklan Citra Pariwara Mandiri',
                'alamat_pemasangkoran' => $plgAgensi?->alamat ?? 'Jl. KHZ. Mustofa No. 142, Kota Tasikmalaya',
                'id_iklankoran' => $pkgAdv?->id ?? 24,
                'kategori_form' => 'ADVERTORIAL',
                'halaman_iklan' => $halSmd?->nama_penempatan ?? 'Halaman 5 (Edisi Sumedang)',
                'kop_kota_id' => $halSmd?->id,
                'warna_iklan' => 'Full Color',
                'ukuran_iklan' => '7 Kolom × 540 mm (1 Halaman)',
                'lebar_kolom' => 7,
                'tinggi_mm' => 540,
                'tanggal_muatkoran' => $yesterday,
                'sales_ae_id' => $aeBudi?->id,
                'sales_iklankoran' => $aeBudi ? "[{$aeBudi->kode_ae}] {$aeBudi->nama_ae}" : 'FO - Front Office',
                'total_muatkoran' => 1,
                'harga_transaksikoran' => 25000000,
                'diskon_transaksikoran' => 3750000, // Diskon Agensi 15%
                'insentif_transaksikoran' => 1062500,
                'komisi_transaksikoran' => 4250000,
                'ppn_transaksikoran' => 0,
                'totaltagihan_transaksikoran' => 21250000,
                'jumlahbayar_transaksikoran' => 10000000,
                'piutang_transaksikoran' => 11250000,
                'status_order' => 'Antrean_Pracetak',
            ],
            [
                'nofakturkoran' => 'FKKRN004',
                'tanggal_transaksikoran' => $fiveDaysAgo,
                'pelanggan_id' => $plgCiamis?->id,
                'nama_pemasangkoran' => $plgCiamis?->nama_instansi ?? 'Diskominfo Kabupaten Ciamis',
                'alamat_pemasangkoran' => $plgCiamis?->alamat ?? 'Jl. Jendral Sudirman No. 90, Ciamis',
                'id_iklankoran' => $pkgBaris?->id ?? 18,
                'kategori_form' => 'BARIS',
                'halaman_iklan' => $halCms?->nama_penempatan ?? 'Halaman 8 (Edisi Ciamis)',
                'kop_kota_id' => $halCms?->id,
                'kop_iklan_id' => $kopHilang?->id,
                'warna_iklan' => 'Hitam Putih (BW)',
                'ukuran_iklan' => '1 Kolom (3 Baris)',
                'lebar_kolom' => 1,
                'tinggi_mm' => 0,
                'jumlah_baris' => 3,
                'naskah_iklan_baris' => "HILANG STNK Spd Mtr Honda Beat\nNopol Z 4521 TA an. Dedi Sukardi\nBagi yg menemukan hubungi 082112345678",
                'tanggal_muatkoran' => $fiveDaysAgo,
                'sales_ae_id' => $aeFo?->id,
                'sales_iklankoran' => $aeFo ? "[{$aeFo->kode_ae}] {$aeFo->nama_ae}" : 'FO - Front Office',
                'total_muatkoran' => 2,
                'harga_transaksikoran' => 68250, // 3 baris x 22.750
                'diskon_transaksikoran' => 0,
                'insentif_transaksikoran' => 0,
                'komisi_transaksikoran' => 0,
                'ppn_transaksikoran' => 0,
                'totaltagihan_transaksikoran' => 136500, // 2 pemuatan
                'jumlahbayar_transaksikoran' => 136500,
                'piutang_transaksikoran' => 0,
                'status_order' => 'Terbit',
            ],
            [
                'nofakturkoran' => 'FKKRN005',
                'tanggal_transaksikoran' => $tenDaysAgo,
                'pelanggan_id' => $plgWalkin?->id,
                'nama_pemasangkoran' => $plgWalkin?->nama_instansi ?? 'Budi Santoso (Walk-in Customer)',
                'alamat_pemasangkoran' => $plgWalkin?->alamat ?? 'Jl. Tentara Pelajar No. 45, Tasikmalaya',
                'id_iklankoran' => $pkgKolom1x50?->id ?? 19,
                'kategori_form' => 'KOLOM',
                'halaman_iklan' => $halTsk?->nama_penempatan ?? 'Halaman 7 (Edisi Tasikmalaya)',
                'kop_kota_id' => $halTsk?->id,
                'warna_iklan' => 'Hitam Putih (BW)',
                'ukuran_iklan' => '1 Kolom × 50 mm',
                'lebar_kolom' => 1,
                'tinggi_mm' => 50,
                'tanggal_muatkoran' => $tenDaysAgo,
                'sales_ae_id' => $aeFo?->id,
                'sales_iklankoran' => $aeFo ? "[{$aeFo->kode_ae}] {$aeFo->nama_ae}" : 'FO - Front Office',
                'total_muatkoran' => 1,
                'harga_transaksikoran' => 450000,
                'diskon_transaksikoran' => 0,
                'insentif_transaksikoran' => 0,
                'komisi_transaksikoran' => 0,
                'ppn_transaksikoran' => 0,
                'totaltagihan_transaksikoran' => 450000,
                'jumlahbayar_transaksikoran' => 450000,
                'piutang_transaksikoran' => 0,
                'status_order' => 'Terbit',
            ],
        ];

        foreach ($koranData as $row) {
            $trans = TransaksiKoran::updateOrCreate(['nofakturkoran' => $row['nofakturkoran']], $row);
            
            // Buat jadwal muat terkait agar sinkron 100% dengan kalender
            OrderJadwalMuat::firstOrCreate(
                ['transaksi_id' => $trans->id, 'edisi_ke' => 1],
                [
                    'tanggal_muat'  => $trans->tanggal_muatkoran,
                    'status_tayang' => $trans->status_order === 'Terbit' ? 'SUDAH_TAYANG' : 'TERJADWAL',
                    'catatan'       => "Edisi perdana {$trans->nofakturkoran}"
                ]
            );
        }

        // 2. MEDIA ONLINE
        $onlinePakets = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'ONL'))->get();
        if ($onlinePakets->isNotEmpty()) {
            $op1 = $onlinePakets->first()->id;
            $op2 = $onlinePakets->skip(1)->first()?->id ?? $op1;
            $op3 = $onlinePakets->skip(2)->first()?->id ?? $op1;

            $onlineData = [
                [
                    'nofakturonline' => 'FKONL001',
                    'tanggal_transaksionline' => $today,
                    'nama_pemasangonline' => 'Diskominfo Kota Tasikmalaya',
                    'alamat_pemasangonline' => 'Bale Kota Tasikmalaya',
                    'id_iklanonline' => $op1,
                    'sales_iklanonline' => 'Hendra Setiawan',
                    'total_muatiklanonline' => 1,
                    'tanggal_muatiklanonline' => $today,
                    'portal_iklanonline' => 'kabarpriangan.pikiran-rakyat.com',
                    'harga_transaksionline' => 2500000,
                    'diskon_transaksionline' => 0,
                    'insentif_transaksionline' => 0,
                    'komisi_transaksionline' => 0,
                    'ppn_transaksionline' => 0,
                    'totaltagihan_transaksionline' => 2500000,
                    'jumlahbayar_transaksionline' => 2500000,
                    'piutang_transaksionline' => 0,
                ],
                [
                    'nofakturonline' => 'FKONL002',
                    'tanggal_transaksionline' => $yesterday,
                    'nama_pemasangonline' => 'Djarum Foundation Tasikmalaya',
                    'alamat_pemasangonline' => 'Jl. HZ Mustofa No. 120, Tasikmalaya',
                    'id_iklanonline' => $op2,
                    'sales_iklanonline' => 'Hendra Setiawan',
                    'total_muatiklanonline' => 1,
                    'tanggal_muatiklanonline' => $yesterday,
                    'portal_iklanonline' => 'kabarpriangan.pikiran-rakyat.com',
                    'harga_transaksionline' => 3500000,
                    'diskon_transaksionline' => 500000,
                    'insentif_transaksionline' => 0,
                    'komisi_transaksionline' => 0,
                    'ppn_transaksionline' => 0,
                    'totaltagihan_transaksionline' => 3000000,
                    'jumlahbayar_transaksionline' => 1500000,
                    'piutang_transaksionline' => 1500000,
                ],
                [
                    'nofakturonline' => 'FKONL003',
                    'tanggal_transaksionline' => $threeDaysAgo,
                    'nama_pemasangonline' => 'Mayasari Plaza Tasikmalaya',
                    'alamat_pemasangonline' => 'Jl. Pasar Wetan No. 1, Tasikmalaya',
                    'id_iklanonline' => $op3,
                    'sales_iklanonline' => 'Rina Marlina',
                    'total_muatiklanonline' => 1,
                    'tanggal_muatiklanonline' => $threeDaysAgo,
                    'portal_iklanonline' => 'kabarpriangan.pikiran-rakyat.com',
                    'harga_transaksionline' => 1500000,
                    'diskon_transaksionline' => 0,
                    'insentif_transaksionline' => 0,
                    'komisi_transaksionline' => 0,
                    'ppn_transaksionline' => 0,
                    'totaltagihan_transaksionline' => 1500000,
                    'jumlahbayar_transaksionline' => 1500000,
                    'piutang_transaksionline' => 0,
                ],
                [
                    'nofakturonline' => 'FKONL004',
                    'tanggal_transaksionline' => $fiveDaysAgo,
                    'nama_pemasangonline' => 'Plaza Asia Tasikmalaya',
                    'alamat_pemasangonline' => 'Jl. KHZ Mustofa No. 326, Tasikmalaya',
                    'id_iklanonline' => $op1,
                    'sales_iklanonline' => 'Dedi Gunawan',
                    'total_muatiklanonline' => 1,
                    'tanggal_muatiklanonline' => $fiveDaysAgo,
                    'portal_iklanonline' => 'kabarpriangan.pikiran-rakyat.com',
                    'harga_transaksionline' => 2500000,
                    'diskon_transaksionline' => 0,
                    'insentif_transaksionline' => 0,
                    'komisi_transaksionline' => 0,
                    'ppn_transaksionline' => 0,
                    'totaltagihan_transaksionline' => 2500000,
                    'jumlahbayar_transaksionline' => 2500000,
                    'piutang_transaksionline' => 0,
                ],
            ];

            foreach ($onlineData as $row) {
                TransaksiOnline::firstOrCreate(['nofakturonline' => $row['nofakturonline']], $row);
            }
        }

        // 3. PRIANGAN TV
        $tvPakets = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'PTV'))->get();
        if ($tvPakets->isNotEmpty()) {
            $tp1 = $tvPakets->first()->id;
            $tp2 = $tvPakets->skip(1)->first()?->id ?? $tp1;
            $tp3 = $tvPakets->skip(2)->first()?->id ?? $tp1;

            $tvData = [
                [
                    'nofakturpriangan' => 'FKPTV001',
                    'tanggal_transaksipriangan' => $today,
                    'nama_pemasangpriangan' => 'KPU Kabupaten Ciamis',
                    'alamat_pemasangpriangan' => 'Jl. Jenderal Sudirman No. 110, Ciamis',
                    'id_iklanpriangan' => $tp2,
                    'sales_iklanpriangan' => 'Iwan Ridwan',
                    'tanggal_muatiklanpriangan' => $today,
                    'total_muatiklanpriangan' => 5,
                    'harga_transaksipriangan' => 5000000,
                    'diskon_transaksipriangan' => 0,
                    'insentif_transaksipriangan' => 0,
                    'komisi_transaksipriangan' => 0,
                    'ppn_transaksipriangan' => 0,
                    'totaltagihan_transaksipriangan' => 5000000,
                    'jumlahbayar_transaksipriangan' => 5000000,
                    'piutang_transaksipriangan' => 0,
                ],
                [
                    'nofakturpriangan' => 'FKPTV002',
                    'tanggal_transaksipriangan' => $yesterday,
                    'nama_pemasangpriangan' => 'PT Surya Madistrindo Tasikmalaya',
                    'alamat_pemasangpriangan' => 'Jl. Brigjen Wasita Kusumah, Tasikmalaya',
                    'id_iklanpriangan' => $tp3,
                    'sales_iklanpriangan' => 'Iwan Ridwan',
                    'tanggal_muatiklanpriangan' => $yesterday,
                    'total_muatiklanpriangan' => 10,
                    'harga_transaksipriangan' => 7500000,
                    'diskon_transaksipriangan' => 500000,
                    'insentif_transaksipriangan' => 0,
                    'komisi_transaksipriangan' => 0,
                    'ppn_transaksipriangan' => 0,
                    'totaltagihan_transaksipriangan' => 7000000,
                    'jumlahbayar_transaksipriangan' => 3500000,
                    'piutang_transaksipriangan' => 3500000,
                ],
                [
                    'nofakturpriangan' => 'FKPTV003',
                    'tanggal_transaksipriangan' => $threeDaysAgo,
                    'nama_pemasangpriangan' => 'PDAM Tirta Sukapura Tasikmalaya',
                    'alamat_pemasangpriangan' => 'Jl. Bebedahan No. 12, Tasikmalaya',
                    'id_iklanpriangan' => $tp1,
                    'sales_iklanpriangan' => 'Ahmad Fauzi',
                    'tanggal_muatiklanpriangan' => $threeDaysAgo,
                    'total_muatiklanpriangan' => 3,
                    'harga_transaksipriangan' => 1500000,
                    'diskon_transaksipriangan' => 0,
                    'insentif_transaksipriangan' => 0,
                    'komisi_transaksipriangan' => 0,
                    'ppn_transaksipriangan' => 0,
                    'totaltagihan_transaksipriangan' => 1500000,
                    'jumlahbayar_transaksipriangan' => 1500000,
                    'piutang_transaksipriangan' => 0,
                ],
            ];

            foreach ($tvData as $row) {
                TransaksiPriangan::firstOrCreate(['nofakturpriangan' => $row['nofakturpriangan']], $row);
            }
        }
    }
}
