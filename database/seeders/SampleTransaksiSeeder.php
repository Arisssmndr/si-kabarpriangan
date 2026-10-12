<?php

namespace Database\Seeders;

use App\Models\JenisIklan;
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

        // 1. KORAN CETAK
        $koranPakets = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'KRN'))->get();
        if ($koranPakets->isNotEmpty()) {
            $p1 = $koranPakets->first()->id;
            $p2 = $koranPakets->skip(1)->first()?->id ?? $p1;
            $p3 = $koranPakets->skip(3)->first()?->id ?? $p1;

            $koranData = [
                [
                    'nofakturkoran' => 'FKKRN001',
                    'tanggal_transaksikoran' => $today,
                    'nama_pemasangkoran' => 'Sekretariat Daerah Kab. Tasikmalaya',
                    'alamat_pemasangkoran' => 'Jl. Bojongkoneng No. 1, Singaparna',
                    'id_iklankoran' => $p3,
                    'halaman_iklan' => 'Halaman 1 (Utama)',
                    'warna_iklan' => 'Full Color',
                    'ukuran_iklan' => '4x150 mmk',
                    'tanggal_muatkoran' => $today,
                    'sales_iklankoran' => 'Ahmad Fauzi',
                    'total_muatkoran' => 1,
                    'harga_transaksikoran' => 4500000,
                    'diskon_transaksikoran' => 0,
                    'insentif_transaksikoran' => 0,
                    'komisi_transaksikoran' => 0,
                    'ppn_transaksikoran' => 0,
                    'totaltagihan_transaksikoran' => 4500000,
                    'jumlahbayar_transaksikoran' => 4500000,
                    'piutang_transaksikoran' => 0,
                ],
                [
                    'nofakturkoran' => 'FKKRN002',
                    'tanggal_transaksikoran' => $today,
                    'nama_pemasangkoran' => 'Bank BJB Cabang Tasikmalaya',
                    'alamat_pemasangkoran' => 'Jl. Mayor Utarya No. 24, Tasikmalaya',
                    'id_iklankoran' => $p2,
                    'halaman_iklan' => 'Halaman 3',
                    'warna_iklan' => 'Full Color',
                    'ukuran_iklan' => '3x100 mmk',
                    'tanggal_muatkoran' => $today,
                    'sales_iklankoran' => 'Rina Marlina',
                    'total_muatkoran' => 1,
                    'harga_transaksikoran' => 2000000,
                    'diskon_transaksikoran' => 200000,
                    'insentif_transaksikoran' => 0,
                    'komisi_transaksikoran' => 0,
                    'ppn_transaksikoran' => 0,
                    'totaltagihan_transaksikoran' => 1800000,
                    'jumlahbayar_transaksikoran' => 1800000,
                    'piutang_transaksikoran' => 0,
                ],
                [
                    'nofakturkoran' => 'FKKRN003',
                    'tanggal_transaksikoran' => $yesterday,
                    'nama_pemasangkoran' => 'Universitas Siliwangi (UNSIL)',
                    'alamat_pemasangkoran' => 'Jl. Siliwangi No. 24, Tasikmalaya',
                    'id_iklankoran' => $p3,
                    'halaman_iklan' => 'Halaman 5 (Pendidikan)',
                    'warna_iklan' => 'Full Color',
                    'ukuran_iklan' => '1 Halaman Penuh',
                    'tanggal_muatkoran' => $yesterday,
                    'sales_iklankoran' => 'Dedi Gunawan',
                    'total_muatkoran' => 1,
                    'harga_transaksikoran' => 5000000,
                    'diskon_transaksikoran' => 500000,
                    'insentif_transaksikoran' => 0,
                    'komisi_transaksikoran' => 0,
                    'ppn_transaksikoran' => 0,
                    'totaltagihan_transaksikoran' => 4500000,
                    'jumlahbayar_transaksikoran' => 2000000,
                    'piutang_transaksikoran' => 2500000,
                ],
                [
                    'nofakturkoran' => 'FKKRN004',
                    'tanggal_transaksikoran' => $fiveDaysAgo,
                    'nama_pemasangkoran' => 'Polres Ciamis',
                    'alamat_pemasangkoran' => 'Jl. Jend. Sudirman No. 271, Ciamis',
                    'id_iklankoran' => $p1,
                    'halaman_iklan' => 'Halaman 2',
                    'warna_iklan' => 'Hitam Putih (BW)',
                    'ukuran_iklan' => '2x50 mmk',
                    'tanggal_muatkoran' => $fiveDaysAgo,
                    'sales_iklankoran' => 'Ahmad Fauzi',
                    'total_muatkoran' => 2,
                    'harga_transaksikoran' => 600000,
                    'diskon_transaksikoran' => 0,
                    'insentif_transaksikoran' => 0,
                    'komisi_transaksikoran' => 0,
                    'ppn_transaksikoran' => 0,
                    'totaltagihan_transaksikoran' => 600000,
                    'jumlahbayar_transaksikoran' => 600000,
                    'piutang_transaksikoran' => 0,
                ],
                [
                    'nofakturkoran' => 'FKKRN005',
                    'tanggal_transaksikoran' => $tenDaysAgo,
                    'nama_pemasangkoran' => 'RSUD dr. Soekardjo Tasikmalaya',
                    'alamat_pemasangkoran' => 'Jl. Rumah Sakit No. 33, Tasikmalaya',
                    'id_iklankoran' => $p2,
                    'halaman_iklan' => 'Halaman 4',
                    'warna_iklan' => 'Full Color',
                    'ukuran_iklan' => '3x80 mmk',
                    'tanggal_muatkoran' => $tenDaysAgo,
                    'sales_iklankoran' => 'Rina Marlina',
                    'total_muatkoran' => 1,
                    'harga_transaksikoran' => 1200000,
                    'diskon_transaksikoran' => 0,
                    'insentif_transaksikoran' => 0,
                    'komisi_transaksikoran' => 0,
                    'ppn_transaksikoran' => 0,
                    'totaltagihan_transaksikoran' => 1200000,
                    'jumlahbayar_transaksikoran' => 1200000,
                    'piutang_transaksikoran' => 0,
                ],
            ];

            foreach ($koranData as $row) {
                TransaksiKoran::firstOrCreate(['nofakturkoran' => $row['nofakturkoran']], $row);
            }
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
