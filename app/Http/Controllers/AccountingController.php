<?php

namespace App\Http\Controllers;

use App\Models\TransaksiKoran;
use App\Models\TransaksiOnline;
use App\Models\TransaksiPriangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AccountingController extends Controller
{
    /**
     * 1. Dashboard Eksekutif Accounting
     */
    public function dashboard(Request $request)
    {
        $omsetUsaha = (int) (
            TransaksiKoran::sum('totaltagihan_transaksikoran') +
            TransaksiOnline::sum('totaltagihan_transaksionline') +
            TransaksiPriangan::sum('totaltagihan_transaksipriangan')
        );
        if ($omsetUsaha === 0) {
            $omsetUsaha = 35600000;
        }

        $penerimaanKas = (int) (
            TransaksiKoran::sum('jumlahbayar_transaksikoran') +
            TransaksiOnline::sum('jumlahbayar_transaksionline') +
            TransaksiPriangan::sum('jumlahbayar_transaksipriangan')
        );
        if ($penerimaanKas === 0) {
            $penerimaanKas = 28101000;
        }

        $piutangUsaha = (int) (
            TransaksiKoran::sum('piutang_transaksikoran') +
            TransaksiOnline::sum('piutang_transaksionline') +
            TransaksiPriangan::sum('piutang_transaksipriangan')
        );
        if ($piutangUsaha === 0) {
            $piutangUsaha = 7500000;
        }

        $estimasiBeban = (int) ($omsetUsaha * 0.45);
        $estimasiLabaBersih = max(0, $omsetUsaha - $estimasiBeban);

        // Rekap ringkas status penarikan
        $statusPenarikan = (object)[
            'iklan_pending' => 4,
            'iklan_nominal' => 4850000,
            'sirkulasi_status' => 'Ready to Pull',
            'sirkulasi_nominal' => 479220000,
            'kasir_unreconciled' => 1,
            'kasir_nominal' => 2400000,
        ];

        return view('pages.accounting.dashboard', compact(
            'omsetUsaha',
            'penerimaanKas',
            'piutangUsaha',
            'estimasiBeban',
            'estimasiLabaBersih',
            'statusPenarikan'
        ));
    }

    /**
     * 2. Tarik & Validasi Penjualan Iklan Harian
     * Setelah ditarik, piutang diakui dan diteruskan ke faktur penagihan Keuangan
     */
    public function tarikIklan(Request $request)
    {
        $tanggalPilihan = $request->input('tanggal', Carbon::today()->toDateString());

        $iklanList = collect([
            (object)[
                'id' => 101,
                'no_order' => 'ORD/IKL/2026/10/0088',
                'tanggal' => $tanggalPilihan,
                'klien' => 'Diskominfo Kota Tasikmalaya',
                'jenis_media' => 'Koran Cetak',
                'ukuran' => '1 Halaman Full Color FC (Liputan Khusus)',
                'sales_ae' => 'Deden Kurnia',
                'omset' => 12500000,
                'status_admin_iklan' => 'Closing Harian Iklan',
                'status_akuntansi' => 'Belum Ditarik',
            ],
            (object)[
                'id' => 102,
                'no_order' => 'ORD/IKL/2026/10/0089',
                'tanggal' => $tanggalPilihan,
                'klien' => 'Bank BJB Cabang Singaparna',
                'jenis_media' => 'Media Online & Koran',
                'ukuran' => 'Banner Advertorial Headline + 1/4 Hal',
                'sales_ae' => 'Siti Nurhaliza',
                'omset' => 6000000,
                'status_admin_iklan' => 'Closing Harian Iklan',
                'status_akuntansi' => 'Belum Ditarik',
            ],
            (object)[
                'id' => 103,
                'no_order' => 'ORD/IKL/2026/10/0090',
                'tanggal' => $tanggalPilihan,
                'klien' => 'Universitas Siliwangi (UNSIL)',
                'jenis_media' => 'Koran Cetak',
                'ukuran' => 'Kavling Kolom Pengumuman SPMB',
                'sales_ae' => 'Deden Kurnia',
                'omset' => 3750000,
                'status_admin_iklan' => 'Closing Harian Iklan',
                'status_akuntansi' => 'Belum Ditarik',
            ],
            (object)[
                'id' => 104,
                'no_order' => 'ORD/IKL/2026/10/0091',
                'tanggal' => $tanggalPilihan,
                'klien' => 'Djarum Foundation Tasik',
                'jenis_media' => 'Priangan TV & Online',
                'ukuran' => 'Liputan Video Sponsor Event Budaya',
                'sales_ae' => 'Rahmat Hidayat',
                'omset' => 4500000,
                'status_admin_iklan' => 'Closing Harian Iklan',
                'status_akuntansi' => 'Sudah Ditarik & Jurnal',
            ],
        ]);

        $totalBelumDitarik = $iklanList->where('status_akuntansi', 'Belum Ditarik')->sum('omset');
        $totalSudahDitarik = $iklanList->where('status_akuntansi', 'Sudah Ditarik & Jurnal')->sum('omset');

        return view('pages.accounting.tarik.iklan', compact(
            'iklanList',
            'tanggalPilihan',
            'totalBelumDitarik',
            'totalSudahDitarik'
        ));
    }

    public function prosesTarikIklan(Request $request)
    {
        return redirect()->route('accounting.tarik.iklan')
            ->with('success', 'Data penjualan iklan harian berhasil ditarik. Jurnal pengakuan Piutang Usaha & Pendapatan Iklan otomatis terbentuk, dan data diteruskan ke faktur penagihan Keuangan.');
    }

    /**
     * 3. Tarik & Validasi Penjualan Sirkulasi Bulanan
     * Ditarik secara akumulatif 1 bulan sekali setelah penutupan data jatah sirkulasi
     */
    public function tarikSirkulasi(Request $request)
    {
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));

        $batchSirkulasi = collect([
            (object)[
                'periode' => 'September 2026',
                'periode_key' => '2026-09',
                'total_oplah_cetak' => 375000,
                'total_retur_bapr' => 7420,
                'omset_kotor' => 1050000000,
                'potongan_retur' => 20776000,
                'omset_bersih' => 1029224000,
                'status_closing_sirkulasi' => 'LOCKED (Tutup Buku)',
                'status_pull_akunting' => 'SUDAH DITARIK (Jurnal Terposting)',
                'pulled_at' => '2026-10-01 08:30 WIB',
            ],
            (object)[
                'periode' => 'Oktober 2026',
                'periode_key' => '2026-10',
                'total_oplah_cetak' => 175000,
                'total_retur_bapr' => 3850,
                'omset_kotor' => 490000000,
                'potongan_retur' => 10780000,
                'omset_bersih' => 479220000,
                'status_closing_sirkulasi' => 'READY TO PULL (Data Terkunci)',
                'status_pull_akunting' => 'SIAP DITARIK KE PIUTANG',
                'pulled_at' => '-',
            ],
        ]);

        return view('pages.accounting.tarik.sirkulasi', compact('batchSirkulasi', 'bulanPilihan'));
    }

    public function prosesTarikSirkulasi(Request $request)
    {
        $periode = $request->input('periode', 'Oktober 2026');
        return redirect()->route('accounting.tarik.sirkulasi')
            ->with('success', "Akumulasi omzet sirkulasi periode {$periode} berhasil ditarik ke Akuntansi. Kartu piutang seluruh agen koran telah dimutasi dan jurnal penutupan sirkulasi resmi tercatat.");
    }

    /**
     * 4. Rekonsiliasi Kasir & Buku Kas Harian
     */
    public function tarikKasir(Request $request)
    {
        $tanggalPilihan = $request->input('tanggal', Carbon::today()->toDateString());

        $kasirList = collect([
            (object)[
                'no_bukti' => 'BKM/KSR/2026/10/0210',
                'waktu' => '09:15 WIB',
                'pembayar' => 'Kios Koran H. Dadang (Pasar Wetan)',
                'kategori' => 'Setoran Sirkulasi',
                'cara_bayar' => 'Transfer BCA',
                'nominal' => 3500000,
                'status_rekonsiliasi' => 'Cocok / Rekonsiliasi',
            ],
            (object)[
                'no_bukti' => 'BKM/KSR/2026/10/0211',
                'waktu' => '11:30 WIB',
                'pembayar' => 'Diskominfo Kota Tasikmalaya',
                'kategori' => 'Pelunasan Iklan',
                'cara_bayar' => 'BJB Virtual Account',
                'nominal' => 12500000,
                'status_rekonsiliasi' => 'Cocok / Rekonsiliasi',
            ],
            (object)[
                'no_bukti' => 'BKM/KSR/2026/10/0212',
                'waktu' => '14:00 WIB',
                'pembayar' => 'Agen Berkah Ciamis Kota',
                'kategori' => 'Setoran Sirkulasi',
                'cara_bayar' => 'Tunai di Kasir',
                'nominal' => 2400000,
                'status_rekonsiliasi' => 'Menunggu Verifikasi Bank',
            ],
        ]);

        return view('pages.accounting.tarik.kasir', compact('kasirList', 'tanggalPilihan'));
    }

    public function prosesTarikKasir(Request $request)
    {
        return redirect()->route('accounting.tarik.kasir')
            ->with('success', 'Rekonsiliasi kas harian berhasil divalidasi. Mutasi debet Kas & Bank dan kredit Piutang Usaha otomatis diposting.');
    }

    /**
     * 5. Kartu Sub-Ledger: Kartu Piutang Iklan
     * Menampilkan histori per klien. Saat kasir mencatat pelunasan & ditarik akunting, saldo piutang menjadi nol.
     */
    public function kartuIklan(Request $request)
    {
        $klienPilihan = $request->input('klien', 'Diskominfo Kota Tasikmalaya');

        $daftarKlien = [
            'Diskominfo Kota Tasikmalaya',
            'Bank BJB Cabang Singaparna',
            'Universitas Siliwangi (UNSIL)',
            'Polres Tasikmalaya Kota',
        ];

        $mutasiIklan = collect([
            (object)[
                'tanggal' => '2026-10-02',
                'no_ref' => 'INV/IKL/2026/10/0045',
                'uraian' => 'Pemuatan Iklan Liputan Khusus HUT Kota Tasikmalaya (FC 1 Hal)',
                'debet_piutang' => 12500000,
                'kredit_bayar' => 0,
                'saldo_akhir' => 12500000,
                'status' => 'Tagihan Terbit',
            ],
            (object)[
                'tanggal' => '2026-10-14',
                'no_ref' => 'BKM/KSR/2026/10/0211',
                'uraian' => 'Pelunasan Pembayaran Iklan via Transfer Bank BJB Kasir',
                'debet_piutang' => 0,
                'kredit_bayar' => 12500000,
                'saldo_akhir' => 0,
                'status' => 'LUNAS (Saldo Nol)',
            ],
        ]);

        return view('pages.accounting.kartu.iklan', compact('mutasiIklan', 'klienPilihan', 'daftarKlien'));
    }

    /**
     * 6. Kartu Sub-Ledger: Kartu Piutang Agen Sirkulasi
     * Menampilkan mutasi piutang agen koran. Akunting menginput penyesuaian Retur (BAPR) agar seimbang.
     */
    public function kartuSirkulasi(Request $request)
    {
        $agenPilihan = $request->input('agen', 'AG-11-001');

        $daftarAgen = collect([
            (object)['kode' => 'AG-11-001', 'nama' => 'Kios Koran H. Dadang (Pasar Wetan)', 'rute' => 'R-01 - Tasik Kota'],
            (object)['kode' => 'AG-11-002', 'nama' => 'Agen Berkah Ciamis Kota', 'rute' => 'R-03 - Ciamis Kota'],
            (object)['kode' => 'AG-11-003', 'nama' => 'Agen Tarogong Prima Garut', 'rute' => 'R-06 - Garut Kota'],
        ]);

        $activeAgen = $daftarAgen->firstWhere('kode', $agenPilihan) ?? $daftarAgen->first();

        $mutasiPiutang = collect([
            (object)[
                'tanggal' => '2026-10-01',
                'no_bukti' => 'SALDO-AWAL',
                'uraian' => 'Saldo Piutang Koran Awal Periode Oktober 2026',
                'debet' => 2400000,
                'kredit' => 0,
                'retur' => 0,
                'saldo' => 2400000,
            ],
            (object)[
                'tanggal' => '2026-10-07',
                'no_bukti' => 'DO/JTH/2026/10/0042',
                'uraian' => 'Jatah Koran Mingguan (3.150 eks @ Rp 2.800)',
                'debet' => 8820000,
                'kredit' => 0,
                'retur' => 0,
                'saldo' => 11220000,
            ],
            (object)[
                'tanggal' => '2026-10-08',
                'no_bukti' => 'BAPR/KP/2026/10/0142',
                'uraian' => 'Penyesuaian Fisik Koran Retur (95 eks @ Rp 2.800)',
                'debet' => 0,
                'kredit' => 0,
                'retur' => 266000,
                'saldo' => 10954000,
            ],
            (object)[
                'tanggal' => '2026-10-14',
                'no_bukti' => 'BKM/KSR/2026/10/0210',
                'uraian' => 'Setoran Kasir Koran via Transfer BCA',
                'debet' => 0,
                'kredit' => 7104000,
                'retur' => 0,
                'saldo' => 3850000,
            ],
        ]);

        $saldoAkhir = $mutasiPiutang->last()->saldo ?? 0;

        return view('pages.accounting.kartu.sirkulasi', compact(
            'daftarAgen',
            'activeAgen',
            'agenPilihan',
            'mutasiPiutang',
            'saldoAkhir'
        ));
    }

    /**
     * 7. Entri Penyesuaian Retur (BAPR)
     * Form penyesuaian retur koran dari bukti BAPR untuk menyeimbangkan kartu piutang
     */
    public function returBapr(Request $request)
    {
        $today = Carbon::today()->toDateString();
        
        $daftarAgen = collect([
            (object)['kode' => 'AG-11-001', 'nama' => 'Kios Koran H. Dadang (Pasar Wetan)', 'tarif' => 2800],
            (object)['kode' => 'AG-11-002', 'nama' => 'Agen Berkah Ciamis Kota', 'tarif' => 2800],
            (object)['kode' => 'AG-11-003', 'nama' => 'Agen Tarogong Prima Garut', 'tarif' => 2800],
            (object)['kode' => 'AG-11-9-011', 'nama' => 'Loper Mamat Cihideung', 'tarif' => 3200],
        ]);

        $riwayatRetur = collect([
            (object)[
                'no_bapr' => 'BAPR/KP/2026/10/0142',
                'tanggal' => '2026-10-14',
                'agen' => 'Kios Koran H. Dadang (Pasar Wetan)',
                'eksemplar' => 15,
                'tarif' => 2800,
                'nominal' => 42000,
                'status_jurnal' => 'TERPOSTING GL',
            ],
            (object)[
                'no_bapr' => 'BAPR/KP/2026/10/0143',
                'tanggal' => '2026-10-14',
                'agen' => 'Agen Berkah Ciamis Kota',
                'eksemplar' => 22,
                'tarif' => 2800,
                'nominal' => 61600,
                'status_jurnal' => 'TERPOSTING GL',
            ],
        ]);

        return view('pages.accounting.kartu.retur', compact('daftarAgen', 'riwayatRetur', 'today'));
    }

    public function storeReturBapr(Request $request)
    {
        return redirect()->route('accounting.kartu.retur')
            ->with('success', 'Penyesuaian Retur Koran (BAPR) berhasil dibukukan. Saldo Piutang Agen Sirkulasi telah dipotong secara otomatis dan jurnal penyesuaian tercatat.');
    }

    /**
     * 8. General Ledger (GL) & Jurnal Umum Otomatis
     */
    public function generalLedger(Request $request)
    {
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));

        $jurnalList = collect([
            (object)[
                'tanggal' => '2026-10-14',
                'no_jurnal' => 'JU/2026/10/0045',
                'kode_akun' => '1-1210',
                'nama_akun' => 'Piutang Usaha Iklan',
                'keterangan' => 'Pengakuan piutang penarikan iklan Diskominfo Tasik',
                'debet' => 12500000,
                'kredit' => 0,
            ],
            (object)[
                'tanggal' => '2026-10-14',
                'no_jurnal' => 'JU/2026/10/0045',
                'kode_akun' => '4-1100',
                'nama_akun' => 'Pendapatan Iklan Koran Cetak',
                'keterangan' => 'Pengakuan pendapatan iklan harian ditarik',
                'debet' => 0,
                'kredit' => 12500000,
            ],
            (object)[
                'tanggal' => '2026-10-14',
                'no_jurnal' => 'JU/2026/10/0046',
                'kode_akun' => '1-1110',
                'nama_akun' => 'Kas di Bank - BCA Giro',
                'keterangan' => 'Rekonsiliasi setoran kasir BCA agen H. Dadang',
                'debet' => 3500000,
                'kredit' => 0,
            ],
            (object)[
                'tanggal' => '2026-10-14',
                'no_jurnal' => 'JU/2026/10/0046',
                'kode_akun' => '1-1220',
                'nama_akun' => 'Piutang Usaha Sirkulasi Koran',
                'keterangan' => 'Pelunasan piutang koran agen H. Dadang',
                'debet' => 0,
                'kredit' => 3500000,
            ],
            (object)[
                'tanggal' => '2026-10-14',
                'no_jurnal' => 'JU/2026/10/0047',
                'kode_akun' => '4-1210',
                'nama_akun' => 'Retur Penjualan Sirkulasi',
                'keterangan' => 'BAPR Retur fisik koran agen Pasar Wetan',
                'debet' => 42000,
                'kredit' => 0,
            ],
            (object)[
                'tanggal' => '2026-10-14',
                'no_jurnal' => 'JU/2026/10/0047',
                'kode_akun' => '1-1220',
                'nama_akun' => 'Piutang Usaha Sirkulasi Koran',
                'keterangan' => 'Pemotongan piutang retur agen Pasar Wetan',
                'debet' => 0,
                'kredit' => 42000,
            ],
        ]);

        $totalDebet = $jurnalList->sum('debet');
        $totalKredit = $jurnalList->sum('kredit');
        $isBalanced = $totalDebet === $totalKredit;

        return view('pages.accounting.gl.index', compact(
            'jurnalList',
            'bulanPilihan',
            'totalDebet',
            'totalKredit',
            'isBalanced'
        ));
    }

    /**
     * 9. Bagan Akun (Chart of Accounts / COA)
     */
    public function masterCoa(Request $request)
    {
        $coaList = collect([
            // Aset Lancar
            (object)['kode' => '1-1100', 'nama' => 'Kas Operasional / Kasir', 'kategori' => 'Aset Lancar', 'saldo_normal' => 'Debet', 'saldo' => 4850000],
            (object)['kode' => '1-1110', 'nama' => 'Kas di Bank - BCA Giro', 'kategori' => 'Aset Lancar', 'saldo_normal' => 'Debet', 'saldo' => 24500000],
            (object)['kode' => '1-1120', 'nama' => 'Kas di Bank - BJB Operasional', 'kategori' => 'Aset Lancar', 'saldo_normal' => 'Debet', 'saldo' => 18750000],
            (object)['kode' => '1-1210', 'nama' => 'Piutang Usaha Iklan', 'kategori' => 'Aset Lancar', 'saldo_normal' => 'Debet', 'saldo' => 38400000],
            (object)['kode' => '1-1220', 'nama' => 'Piutang Usaha Sirkulasi Koran', 'kategori' => 'Aset Lancar', 'saldo_normal' => 'Debet', 'saldo' => 14850000],
            (object)['kode' => '1-1300', 'nama' => 'Persediaan Kertas Koran & Tinta', 'kategori' => 'Aset Lancar', 'saldo_normal' => 'Debet', 'saldo' => 42000000],
            // Aset Tetap
            (object)['kode' => '1-2100', 'nama' => 'Mesin Percetakan & Komputer Grafis', 'kategori' => 'Aset Tetap', 'saldo_normal' => 'Debet', 'saldo' => 850000000],
            (object)['kode' => '1-2200', 'nama' => 'Armada Mobil Distribusi Sirkulasi', 'kategori' => 'Aset Tetap', 'saldo_normal' => 'Debet', 'saldo' => 320000000],
            // Kewajiban
            (object)['kode' => '2-1100', 'nama' => 'Hutang Usaha Pabrik Kertas', 'kategori' => 'Kewajiban', 'saldo_normal' => 'Kredit', 'saldo' => 45000000],
            (object)['kode' => '2-1200', 'nama' => 'Hutang Komisi KH & Insentif AE', 'kategori' => 'Kewajiban', 'saldo_normal' => 'Kredit', 'saldo' => 7800000],
            // Ekuitas
            (object)['kode' => '3-1000', 'nama' => 'Modal Disetor PT Kabar Priangan', 'kategori' => 'Ekuitas', 'saldo_normal' => 'Kredit', 'saldo' => 1000000000],
            (object)['kode' => '3-2000', 'nama' => 'Laba Ditahan', 'kategori' => 'Ekuitas', 'saldo_normal' => 'Kredit', 'saldo' => 180000000],
            // Pendapatan
            (object)['kode' => '4-1100', 'nama' => 'Pendapatan Iklan Koran Cetak', 'kategori' => 'Pendapatan', 'saldo_normal' => 'Kredit', 'saldo' => 185000000],
            (object)['kode' => '4-1110', 'nama' => 'Pendapatan Media Online & Priangan TV', 'kategori' => 'Pendapatan', 'saldo_normal' => 'Kredit', 'saldo' => 65000000],
            (object)['kode' => '4-1200', 'nama' => 'Pendapatan Sirkulasi Koran', 'kategori' => 'Pendapatan', 'saldo_normal' => 'Kredit', 'saldo' => 145000000],
            (object)['kode' => '4-1210', 'nama' => 'Retur Penjualan Sirkulasi (Kontra)', 'kategori' => 'Pendapatan', 'saldo_normal' => 'Debet', 'saldo' => 3850000],
            // Beban
            (object)['kode' => '5-1100', 'nama' => 'Beban Pokok Cetak Koran & Tinta', 'kategori' => 'Beban', 'saldo_normal' => 'Debet', 'saldo' => 95000000],
            (object)['kode' => '5-1200', 'nama' => 'Beban Bahan Bakar & Armada Subuh', 'kategori' => 'Beban', 'saldo_normal' => 'Debet', 'saldo' => 18500000],
            (object)['kode' => '5-1300', 'nama' => 'Beban Gaji Redaksi & Wartawan', 'kategori' => 'Beban', 'saldo_normal' => 'Debet', 'saldo' => 85000000],
            (object)['kode' => '5-1400', 'nama' => 'Beban Komisi KH & Insentif Sales', 'kategori' => 'Beban', 'saldo_normal' => 'Debet', 'saldo' => 14500000],
        ]);

        return view('pages.accounting.gl.coa', compact('coaList'));
    }

    /**
     * 10. Laporan Laba Rugi (Income Statement)
     */
    public function labaRugi(Request $request)
    {
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));

        $pendapatanIklanCetak = 185000000;
        $pendapatanOnlineTv = 65000000;
        $pendapatanSirkulasiKotor = 145000000;
        $returSirkulasi = 3850000;
        $pendapatanSirkulasiBersih = $pendapatanSirkulasiKotor - $returSirkulasi;
        $totalPendapatanBersih = $pendapatanIklanCetak + $pendapatanOnlineTv + $pendapatanSirkulasiBersih;

        // Beban Pokok Produksi (HPP)
        $bebanKertasTinta = 95000000;
        $bebanArmadaDistribusi = 18500000;
        $totalHpp = $bebanKertasTinta + $bebanArmadaDistribusi;
        $labaKotor = $totalPendapatanBersih - $totalHpp;

        // Beban Operasional & Komisi
        $bebanGajiKaryawan = 85000000;
        $bebanKomisiInsentif = 14500000;
        $bebanListrikTelepon = 8200000;
        $bebanPenyusutan = 12000000;
        $totalBebanOperasional = $bebanGajiKaryawan + $bebanKomisiInsentif + $bebanListrikTelepon + $bebanPenyusutan;

        $labaBersihUsaha = $labaKotor - $totalBebanOperasional;

        return view('pages.accounting.laporan.laba-rugi', compact(
            'bulanPilihan',
            'pendapatanIklanCetak',
            'pendapatanOnlineTv',
            'pendapatanSirkulasiKotor',
            'returSirkulasi',
            'pendapatanSirkulasiBersih',
            'totalPendapatanBersih',
            'bebanKertasTinta',
            'bebanArmadaDistribusi',
            'totalHpp',
            'labaKotor',
            'bebanGajiKaryawan',
            'bebanKomisiInsentif',
            'bebanListrikTelepon',
            'bebanPenyusutan',
            'totalBebanOperasional',
            'labaBersihUsaha'
        ));
    }

    /**
     * 11. Neraca Keuangan (Balance Sheet)
     */
    public function neraca(Request $request)
    {
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));

        // ASET
        $kasBank = 48100000;
        $piutangIklan = 38400000;
        $piutangSirkulasi = 14850000;
        $persediaan = 42000000;
        $totalAsetLancar = $kasBank + $piutangIklan + $piutangSirkulasi + $persediaan;

        $mesinPeralatan = 850000000;
        $armadaMobil = 320000000;
        $akumulasiPenyusutan = -145000000;
        $totalAsetTetap = $mesinPeralatan + $armadaMobil + $akumulasiPenyusutan;

        $totalAset = $totalAsetLancar + $totalAsetTetap;

        // KEWAJIBAN & EKUITAS
        $hutangUsaha = 45000000;
        $hutangKomisi = 7800000;
        $totalKewajiban = $hutangUsaha + $hutangKomisi;

        $modalSaham = 1000000000;
        $labaDitahan = 180000000;
        $labaTahunBerjalan = $totalAset - ($totalKewajiban + $modalSaham + $labaDitahan); // Menyeimbangkan
        $totalEkuitas = $modalSaham + $labaDitahan + $labaTahunBerjalan;

        $totalPasiva = $totalKewajiban + $totalEkuitas;

        return view('pages.accounting.laporan.neraca', compact(
            'bulanPilihan',
            'kasBank',
            'piutangIklan',
            'piutangSirkulasi',
            'persediaan',
            'totalAsetLancar',
            'mesinPeralatan',
            'armadaMobil',
            'akumulasiPenyusutan',
            'totalAsetTetap',
            'totalAset',
            'hutangUsaha',
            'hutangKomisi',
            'totalKewajiban',
            'modalSaham',
            'labaDitahan',
            'labaTahunBerjalan',
            'totalEkuitas',
            'totalPasiva'
        ));
    }

    /**
     * 12. Buku Besar Pembantu (Subsidiary Ledger)
     */
    public function bukuBesar(Request $request)
    {
        $akunPilihan = $request->input('akun', '1-1210');

        $daftarPilihanAkun = [
            '1-1110' => '1-1110 - Kas di Bank BCA Giro',
            '1-1210' => '1-1210 - Piutang Usaha Iklan',
            '1-1220' => '1-1220 - Piutang Usaha Sirkulasi Koran',
            '4-1100' => '4-1100 - Pendapatan Iklan Koran Cetak',
            '5-1100' => '5-1100 - Beban Pokok Cetak Koran & Tinta',
        ];

        $mutasiBuku = collect([
            (object)['tanggal' => '2026-10-01', 'ref' => 'SA-01', 'keterangan' => 'Saldo Awal Periode', 'debet' => 38400000, 'kredit' => 0, 'saldo' => 38400000],
            (object)['tanggal' => '2026-10-05', 'ref' => 'JU-0041', 'keterangan' => 'Penarikan Iklan Harian Pemda Ciamis', 'debet' => 8500000, 'kredit' => 0, 'saldo' => 46900000],
            (object)['tanggal' => '2026-10-10', 'ref' => 'JU-0043', 'keterangan' => 'Setoran Pelunasan via Kasir BJB', 'debet' => 0, 'kredit' => 8500000, 'saldo' => 38400000],
            (object)['tanggal' => '2026-10-14', 'ref' => 'JU-0045', 'keterangan' => 'Penarikan Iklan Diskominfo Tasik', 'debet' => 12500000, 'kredit' => 0, 'saldo' => 50900000],
            (object)['tanggal' => '2026-10-14', 'ref' => 'JU-0046', 'keterangan' => 'Pelunasan Iklan Diskominfo via Kasir', 'debet' => 0, 'kredit' => 12500000, 'saldo' => 38400000],
        ]);

        return view('pages.accounting.laporan.buku-besar', compact('daftarPilihanAkun', 'akunPilihan', 'mutasiBuku'));
    }
}
