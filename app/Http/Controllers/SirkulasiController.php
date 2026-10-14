<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SirkulasiController extends Controller
{
    /**
     * Helper dummy data untuk Agen & Sub-Agen Kabar Priangan.
     * Kategori:
     * - 00: Agen Non-Aktif (Dilarang hard-delete karena terhubung kartu piutang akuntansi)
     * - 11: Agen Besar (Distribusi langsung & induk sub-agen)
     * - 11-9: Sub-Agen (Pengecer di bawah Agen Besar)
     */
    private function getDaftarAgen(): Collection
    {
        return collect([
            (object)[
                'id' => 1,
                'kode_agen' => 'AG-11-001',
                'nama_agen' => 'Kios Koran H. Dadang (Pasar Wetan)',
                'kategori' => '11',
                'kategori_label' => 'Agen Besar (11)',
                'induk_agen' => '-',
                'rute_kode' => 'R-01',
                'rute_nama' => 'Tasik Kota & Singaparna',
                'wilayah' => 'Kota Tasikmalaya',
                'telepon' => '0812-2345-6789',
                'jatah_default' => 450,
                'plafon_piutang' => 15000000,
                'saldo_piutang' => 3850000,
                'status' => 'aktif',
                'status_label' => 'Aktif',
            ],
            (object)[
                'id' => 2,
                'kode_agen' => 'AG-11-9-011',
                'nama_agen' => 'Loper Mamat Cihideung',
                'kategori' => '11-9',
                'kategori_label' => 'Sub-Agen (11-9)',
                'induk_agen' => 'Kios Koran H. Dadang (AG-11-001)',
                'rute_kode' => 'R-01',
                'rute_nama' => 'Tasik Kota & Singaparna',
                'wilayah' => 'Kota Tasikmalaya',
                'telepon' => '0852-9876-1234',
                'jatah_default' => 80,
                'plafon_piutang' => 2000000,
                'saldo_piutang' => 640000,
                'status' => 'aktif',
                'status_label' => 'Aktif',
            ],
            (object)[
                'id' => 3,
                'kode_agen' => 'AG-11-002',
                'nama_agen' => 'Agen Berkah Ciamis Kota',
                'kategori' => '11',
                'kategori_label' => 'Agen Besar (11)',
                'induk_agen' => '-',
                'rute_kode' => 'R-03',
                'rute_nama' => 'Ciamis Kota & Kawali',
                'wilayah' => 'Kab. Ciamis',
                'telepon' => '0813-8899-7711',
                'jatah_default' => 600,
                'plafon_piutang' => 20000000,
                'saldo_piutang' => 5200000,
                'status' => 'aktif',
                'status_label' => 'Aktif',
            ],
            (object)[
                'id' => 4,
                'kode_agen' => 'AG-11-9-021',
                'nama_agen' => 'Kios Alun-alun Kawali',
                'kategori' => '11-9',
                'kategori_label' => 'Sub-Agen (11-9)',
                'induk_agen' => 'Agen Berkah Ciamis Kota (AG-11-002)',
                'rute_kode' => 'R-03',
                'rute_nama' => 'Ciamis Kota & Kawali',
                'wilayah' => 'Kab. Ciamis',
                'telepon' => '0877-1122-3344',
                'jatah_default' => 120,
                'plafon_piutang' => 3000000,
                'saldo_piutang' => 890000,
                'status' => 'aktif',
                'status_label' => 'Aktif',
            ],
            (object)[
                'id' => 5,
                'kode_agen' => 'AG-11-003',
                'nama_agen' => 'Agen Tarogong Prima Garut',
                'kategori' => '11',
                'kategori_label' => 'Agen Besar (11)',
                'induk_agen' => '-',
                'rute_kode' => 'R-06',
                'rute_nama' => 'Garut & Pameungpeuk',
                'wilayah' => 'Kab. Garut',
                'telepon' => '0812-4455-6677',
                'jatah_default' => 550,
                'plafon_piutang' => 18000000,
                'saldo_piutang' => 4100000,
                'status' => 'aktif',
                'status_label' => 'Aktif',
            ],
            (object)[
                'id' => 6,
                'kode_agen' => 'AG-11-9-031',
                'nama_agen' => 'Kios Subur Pameungpeuk Pesisir',
                'kategori' => '11-9',
                'kategori_label' => 'Sub-Agen (11-9)',
                'induk_agen' => 'Agen Tarogong Prima Garut (AG-11-003)',
                'rute_kode' => 'R-06',
                'rute_nama' => 'Garut & Pameungpeuk',
                'wilayah' => 'Kab. Garut Selatan',
                'telepon' => '0821-3344-5566',
                'jatah_default' => 90,
                'plafon_piutang' => 2500000,
                'saldo_piutang' => 720000,
                'status' => 'aktif',
                'status_label' => 'Aktif',
            ],
            (object)[
                'id' => 7,
                'kode_agen' => 'AG-11-004',
                'nama_agen' => 'Kios Sinar Pagi Banjar Patroman',
                'kategori' => '11',
                'kategori_label' => 'Agen Besar (11)',
                'induk_agen' => '-',
                'rute_kode' => 'R-05',
                'rute_nama' => 'Banjar & Pangandaran',
                'wilayah' => 'Kota Banjar',
                'telepon' => '0853-2211-9988',
                'jatah_default' => 380,
                'plafon_piutang' => 12000000,
                'saldo_piutang' => 2950000,
                'status' => 'aktif',
                'status_label' => 'Aktif',
            ],
            (object)[
                'id' => 8,
                'kode_agen' => 'AG-11-005',
                'nama_agen' => 'Agen Karangnunggal Sejahtera (Tasela)',
                'kategori' => '11',
                'kategori_label' => 'Agen Besar (11)',
                'induk_agen' => '-',
                'rute_kode' => 'R-02',
                'rute_nama' => 'Tasik Selatan & Cipatujah',
                'wilayah' => 'Kab. Tasikmalaya Selatan',
                'telepon' => '0813-7766-5544',
                'jatah_default' => 320,
                'plafon_piutang' => 10000000,
                'saldo_piutang' => 2100000,
                'status' => 'aktif',
                'status_label' => 'Aktif',
            ],
            (object)[
                'id' => 9,
                'kode_agen' => 'AG-00-991',
                'nama_agen' => 'Ex-Kios Terminal Ciawi (Tutup Usaha)',
                'kategori' => '00',
                'kategori_label' => 'Agen Non-Aktif (00)',
                'induk_agen' => '-',
                'rute_kode' => 'R-01',
                'rute_nama' => 'Tasik Kota & Singaparna',
                'wilayah' => 'Kab. Tasikmalaya Utara',
                'telepon' => '0811-9988-7766',
                'jatah_default' => 0,
                'plafon_piutang' => 5000000,
                'saldo_piutang' => 1450000,
                'status' => 'non-aktif',
                'status_label' => 'Non-Aktif (Terkunci)',
            ],
            (object)[
                'id' => 10,
                'kode_agen' => 'AG-00-992',
                'nama_agen' => 'Ex-Loper Cipatujah Pesisir',
                'kategori' => '00',
                'kategori_label' => 'Agen Non-Aktif (00)',
                'induk_agen' => 'Agen Karangnunggal Sejahtera (AG-11-005)',
                'rute_kode' => 'R-02',
                'rute_nama' => 'Tasik Selatan & Cipatujah',
                'wilayah' => 'Kab. Tasikmalaya Selatan',
                'telepon' => '0822-4433-2211',
                'jatah_default' => 0,
                'plafon_piutang' => 2000000,
                'saldo_piutang' => 450000,
                'status' => 'non-aktif',
                'status_label' => 'Non-Aktif (Terkunci)',
            ],
        ]);
    }

    /**
     * Helper dummy data Rute & Jurusan Armada Mobil Distribusi Kabar Priangan.
     */
    private function getDaftarRute(): Collection
    {
        return collect([
            (object)[
                'kode' => 'R-01',
                'kode_rute' => 'R-01',
                'nama_rute' => 'Tasikmalaya Kota & Singaparna',
                'nama_jurusan' => 'Tasikmalaya Kota & Singaparna',
                'kendaraan' => 'Mobil Carry Box 01 (Z 8812 HP)',
                'armada' => 'Mobil Carry Box 01 (Z 8812 HP)',
                'driver' => 'Kang Asep Kurniawan',
                'sopir' => 'Kang Asep Kurniawan',
                'kontak_sopir' => '0812-9988-1122',
                'jam_berangkat' => '02:30 WIB',
                'titik_tujuan' => 'Mitra Batik, Pasar Wetan, Cihideung, Alun-alun Singaparna, Kudang',
                'total_agen' => 24,
                'alokasi_oplah' => 4200,
                'status' => 'Siap Jalan',
            ],
            (object)[
                'kode' => 'R-02',
                'kode_rute' => 'R-02',
                'nama_rute' => 'Tasikmalaya Selatan & Cipatujah',
                'nama_jurusan' => 'Tasikmalaya Selatan & Cipatujah',
                'kendaraan' => 'Mobil L300 Box 02 (Z 8190 HN)',
                'armada' => 'Mobil L300 Box 02 (Z 8190 HN)',
                'driver' => 'Pak Dedi Sutisna',
                'sopir' => 'Pak Dedi Sutisna',
                'kontak_sopir' => '0852-7711-2233',
                'jam_berangkat' => '02:00 WIB',
                'titik_tujuan' => 'Sukawening, Karangnunggal, Cibalong, Pamijahan, Sindangkerta, Cipatujah',
                'total_agen' => 14,
                'alokasi_oplah' => 1600,
                'status' => 'Siap Jalan',
            ],
            (object)[
                'kode' => 'R-03',
                'kode_rute' => 'R-03',
                'nama_rute' => 'Ciamis Kota & Kawali',
                'nama_jurusan' => 'Ciamis Kota & Kawali',
                'kendaraan' => 'Mobil Luxio Box 03 (Z 1450 TA)',
                'armada' => 'Mobil Luxio Box 03 (Z 1450 TA)',
                'driver' => 'Kang Yana Suryana',
                'sopir' => 'Kang Yana Suryana',
                'kontak_sopir' => '0813-2233-4455',
                'jam_berangkat' => '02:45 WIB',
                'titik_tujuan' => 'Alun-alun Ciamis, Pasar Manis, Baregbeg, Kawali, Lumbung',
                'total_agen' => 18,
                'alokasi_oplah' => 2400,
                'status' => 'Siap Jalan',
            ],
            (object)[
                'kode' => 'R-04',
                'kode_rute' => 'R-04',
                'nama_rute' => 'Panjalu & Rajapolah',
                'nama_jurusan' => 'Panjalu & Rajapolah',
                'kendaraan' => 'Mobil Gran Max 04 (Z 8762 HB)',
                'armada' => 'Mobil Gran Max 04 (Z 8762 HB)',
                'driver' => 'Kang Iwan Ridwan',
                'sopir' => 'Kang Iwan Ridwan',
                'kontak_sopir' => '0877-6655-4433',
                'jam_berangkat' => '03:00 WIB',
                'titik_tujuan' => 'Rajapolah, Jamanis, Cihaurbeuti, Sukamantri, Situ Panjalu',
                'total_agen' => 11,
                'alokasi_oplah' => 1250,
                'status' => 'Siap Jalan',
            ],
            (object)[
                'kode' => 'R-05',
                'kode_rute' => 'R-05',
                'nama_rute' => 'Banjar Patroman & Pangandaran',
                'nama_jurusan' => 'Banjar Patroman & Pangandaran',
                'kendaraan' => 'Mobil L300 Box 05 (Z 8344 KC)',
                'armada' => 'Mobil L300 Box 05 (Z 8344 KC)',
                'driver' => 'Pak Tatang Supriatna',
                'sopir' => 'Pak Tatang Supriatna',
                'kontak_sopir' => '0813-9080-7060',
                'jam_berangkat' => '02:00 WIB',
                'titik_tujuan' => 'Terminal Banjar, Banjarsari, Kalipucang, Pantai Barat Pangandaran, Parigi',
                'total_agen' => 16,
                'alokasi_oplah' => 2100,
                'status' => 'Siap Jalan',
            ],
            (object)[
                'kode' => 'R-06',
                'kode_rute' => 'R-06',
                'nama_rute' => 'Garut Kota & Pameungpeuk',
                'nama_jurusan' => 'Garut Kota & Pameungpeuk',
                'kendaraan' => 'Mobil Gran Max Box 06 (Z 8621 DA)',
                'armada' => 'Mobil Gran Max Box 06 (Z 8621 DA)',
                'driver' => 'Kang Agus Mulyadi',
                'sopir' => 'Kang Agus Mulyadi',
                'kontak_sopir' => '0821-3456-7890',
                'jam_berangkat' => '02:15 WIB',
                'titik_tujuan' => 'Malangbong, Cibatu, Tarogong, Garut Kota, Cikajang, Pameungpeuk',
                'total_agen' => 15,
                'alokasi_oplah' => 2200,
                'status' => 'Siap Jalan',
            ],
            (object)[
                'kode' => 'R-07',
                'kode_rute' => 'R-07',
                'nama_rute' => 'Sumedang & Kuningan / Cirebon',
                'nama_jurusan' => 'Sumedang & Kuningan / Cirebon',
                'kendaraan' => 'Mobil Isuzu Elf 07 (Z 7720 EA)',
                'armada' => 'Mobil Isuzu Elf 07 (Z 7720 EA)',
                'driver' => 'Pak Hendra Gunawan',
                'sopir' => 'Pak Hendra Gunawan',
                'kontak_sopir' => '0812-6543-2109',
                'jam_berangkat' => '01:45 WIB',
                'titik_tujuan' => 'Wado, Darmaraja, Sumedang Kota, Majalengka, Cilimus Kuningan',
                'total_agen' => 12,
                'alokasi_oplah' => 1350,
                'status' => 'Siap Jalan',
            ],
        ]);
    }

    /**
     * 1. Overview / Dashboard Sirkulasi
     */
    public function dashboard(Request $request)
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $oplahHarian = 12500;
        $wilayah = [
            ['nama' => 'Kota & Kab. Tasikmalaya', 'oplah' => 5200, 'agen' => 32, 'retur_pct' => 1.8],
            ['nama' => 'Kabupaten Ciamis', 'oplah' => 2800, 'agen' => 18, 'retur_pct' => 2.1],
            ['nama' => 'Kota Banjar', 'oplah' => 1400, 'agen' => 10, 'retur_pct' => 1.5],
            ['nama' => 'Kabupaten Garut', 'oplah' => 1900, 'agen' => 12, 'retur_pct' => 2.6],
            ['nama' => 'Kabupaten Pangandaran', 'oplah' => 1200, 'agen' => 8, 'retur_pct' => 2.0],
        ];

        $daftarAgen = $this->getDaftarAgen();
        $totalAgenAktif = $daftarAgen->where('status', 'aktif')->count();
        $totalAgenNonAktif = $daftarAgen->where('status', 'non-aktif')->count();
        $totalAgen = $totalAgenAktif ?: 80;
        $totalRetur = 275;
        $efektivitasDistribusi = 97.8;

        return view('pages.sirkulasi.dashboard', compact(
            'oplahHarian',
            'wilayah',
            'totalAgen',
            'totalAgenAktif',
            'totalAgenNonAktif',
            'totalRetur',
            'efektivitasDistribusi',
            'today',
            'tomorrow'
        ));
    }

    /**
     * 2. Master Data Agen & Sub-Agen
     */
    public function masterAgen(Request $request)
    {
        $search = $request->input('search');
        $kategoriFilter = $request->input('kategori', 'all');
        $statusFilter = $request->input('status', 'all');
        $ruteFilter = $request->input('rute', 'all');

        $agenList = $this->getDaftarAgen();

        if ($search) {
            $agenList = $agenList->filter(function ($item) use ($search) {
                return str_contains(strtolower($item->nama_agen), strtolower($search))
                    || str_contains(strtolower($item->kode_agen), strtolower($search))
                    || str_contains(strtolower($item->wilayah), strtolower($search));
            });
        }

        if ($kategoriFilter !== 'all') {
            $agenList = $agenList->where('kategori', $kategoriFilter);
        }

        if ($statusFilter !== 'all') {
            $agenList = $agenList->where('status', $statusFilter);
        }

        if ($ruteFilter !== 'all') {
            $agenList = $agenList->where('rute_kode', $ruteFilter);
        }

        $ruteList = $this->getDaftarRute();
        $agenBesarList = $this->getDaftarAgen()->where('kategori', '11');

        return view('pages.sirkulasi.master.agen', compact('agenList', 'ruteList', 'agenBesarList', 'search', 'kategoriFilter', 'statusFilter', 'ruteFilter'));
    }

    public function storeAgen(Request $request)
    {
        return redirect()->route('sirkulasi.master.agen')
            ->with('success', 'Data Agen/Sub-Agen berhasil didaftarkan. Master kartu piutang tersinkronisasi dengan Kasir & Akuntansi.');
    }

    public function toggleStatusAgen($id)
    {
        return redirect()->route('sirkulasi.master.agen')
            ->with('success', 'Status keagenan berhasil diperbarui. Data tetap diarsipkan untuk riwayat buku besar piutang.');
    }

    /**
     * 3. Master Wilayah & Rute Jurusan Armada
     */
    public function masterRute(Request $request)
    {
        $ruteList = $this->getDaftarRute();
        $totalOplahArmada = $ruteList->sum('alokasi_oplah');
        $totalAgenTerlayani = $ruteList->sum('total_agen');

        return view('pages.sirkulasi.master.rute', compact('ruteList', 'totalOplahArmada', 'totalAgenTerlayani'));
    }

    /**
     * 4. Master Kalender Terbit & Target Oplah
     */
    public function masterKalender(Request $request)
    {
        $bulanIni = Carbon::now();
        $daysInMonth = $bulanIni->daysInMonth;
        
        $kalender = collect();
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = Carbon::create($bulanIni->year, $bulanIni->month, $i);
            $isSunday = $date->isSunday();
            $targetOplah = $isSunday ? 14000 : 12500;
            $statusTerbit = 'Terbit Reguler';
            
            $kalender->push((object)[
                'tanggal' => $date->toDateString(),
                'hari' => $date->translatedFormat('l'),
                'tgl_format' => $date->translatedFormat('d F Y'),
                'target_oplah' => $targetOplah,
                'edisi' => $isSunday ? 'Edisi Minggu (Khusus)' : 'Edisi Reguler',
                'status' => $statusTerbit,
            ]);
        }

        return view('pages.sirkulasi.master.kalender', compact('kalender', 'bulanIni'));
    }

    /**
     * 5. Master Tarif & Skema Jatah Gratis
     */
    public function masterTarif(Request $request)
    {
        $skemaTarif = [
            'harga_eceran_resmi' => 4000,
            'harga_agen_besar' => 2800,
            'harga_sub_agen' => 3200,
            'maks_retur_agen_besar' => 10,
            'maks_retur_sub_agen' => 15,
        ];

        $skemaGratis = [
            ['kategori' => 'Koran Sampel Pengiklan', 'kuota' => 150, 'keterangan' => 'Kompensasi bukti pemuatan iklan cetak'],
            ['kategori' => 'Redaksi & Arsip Legal', 'kuota' => 50, 'keterangan' => 'Kantor redaksi, arsip perpustakaan pers & dewan pers'],
            ['kategori' => 'Relasi Forkopimda & Mitra Dinas', 'kuota' => 120, 'keterangan' => 'Bupati/Walikota, Kapolres, Dandim, Kejaksaan se-Priangan'],
            ['kategori' => 'Karyawan & Sirkulasi Internal', 'kuota' => 80, 'keterangan' => 'Alokasi inventaris operasional internal'],
        ];

        return view('pages.sirkulasi.master.tarif', compact('skemaTarif', 'skemaGratis'));
    }

    /**
     * 6. Entri Jatah Harian H+1 (Siklus Harian)
     */
    public function jatahHarian(Request $request)
    {
        $today = Carbon::today();
        $targetTanggal = $request->input('tanggal', Carbon::tomorrow()->toDateString());
        $rutePilihan = $request->input('rute', 'all');

        $daftarAgen = $this->getDaftarAgen()->where('status', 'aktif');
        if ($rutePilihan !== 'all') {
            $daftarAgen = $daftarAgen->where('rute_kode', $rutePilihan);
        }

        $matrixJatah = $daftarAgen->map(function ($agen) {
            $langganan = (int) ($agen->jatah_default * 0.65);
            $eceran = (int) ($agen->jatah_default * 0.30);
            $insidentil = 0;
            $gratis = (int) ($agen->jatah_default * 0.05);
            $totalJatah = $langganan + $eceran + $insidentil + $gratis;

            return (object)[
                'agen_id' => $agen->id,
                'kode_agen' => $agen->kode_agen,
                'nama_agen' => $agen->nama_agen,
                'rute_kode' => $agen->rute_kode,
                'rute_nama' => $agen->rute_nama,
                'jatah_kemarin' => $agen->jatah_default,
                'langganan' => $langganan,
                'eceran' => $eceran,
                'insidentil' => $insidentil,
                'gratis' => $gratis,
                'total_jatah' => $totalJatah,
                'keterangan' => 'Normal Rutin',
            ];
        });

        $totalLangganan = $matrixJatah->sum('langganan');
        $totalEceran = $matrixJatah->sum('eceran');
        $totalInsidentil = $matrixJatah->sum('insidentil');
        $totalGratis = $matrixJatah->sum('gratis');
        $grandTotalJatah = $matrixJatah->sum('total_jatah');

        $ruteList = $this->getDaftarRute();

        return view('pages.sirkulasi.jatah.harian', compact(
            'matrixJatah',
            'targetTanggal',
            'rutePilihan',
            'ruteList',
            'totalLangganan',
            'totalEceran',
            'totalInsidentil',
            'totalGratis',
            'grandTotalJatah',
            'today'
        ));
    }

    public function storeJatahHarian(Request $request)
    {
        return redirect()->route('sirkulasi.jatah.harian', ['tanggal' => $request->input('tanggal_jatah')])
            ->with('success', 'Jatah Harian H+1 berhasil disimpan dan divalidasi untuk perintah cetak pabrik/percetakan malam ini.');
    }

    /**
     * 7. Salin Pola Jatah (Copy Data)
     */
    public function copyJatah(Request $request)
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $lastWeekSameDay = Carbon::tomorrow()->subDays(7);

        return view('pages.sirkulasi.jatah.copy', compact('today', 'tomorrow', 'lastWeekSameDay'));
    }

    public function processCopyJatah(Request $request)
    {
        $sumberTanggal = $request->input('sumber_tanggal');
        $targetTanggal = $request->input('target_tanggal');

        return redirect()->route('sirkulasi.jatah.harian', ['tanggal' => $targetTanggal])
            ->with('success', "Pola jatah dari tanggal {$sumberTanggal} berhasil disalin ke tanggal distribusi {$targetTanggal}. Silakan periksa atau lakukan penyesuaian bila ada pesanan tambahan.");
    }

    /**
     * 8. Perubahan Mendadak (Quick Edit)
     */
    public function quickEditJatah(Request $request)
    {
        $targetTanggal = $request->input('tanggal', Carbon::tomorrow()->toDateString());
        $ruteList = $this->getDaftarRute();
        $agenList = $this->getDaftarAgen()->where('status', 'aktif');

        return view('pages.sirkulasi.jatah.quick-edit', compact('targetTanggal', 'ruteList', 'agenList'));
    }

    public function updateQuickEditJatah(Request $request)
    {
        return redirect()->route('sirkulasi.jatah.quick-edit', ['tanggal' => $request->input('tanggal_distribusi')])
            ->with('success', 'Revisi kuota mendadak berhasil diperbarui dan surat jalan packing otomatis disesuaikan.');
    }

    /**
     * 9. Logistik & Surat Jalan / Nota Pengepakan (Packing)
     */
    public function suratJalan(Request $request)
    {
        $tanggalDistribusi = $request->input('tanggal', Carbon::tomorrow()->toDateString());
        $selectedRuteKode = $request->input('rute', 'R-01');

        $ruteList = $this->getDaftarRute();
        $activeRute = $ruteList->firstWhere('kode_rute', $selectedRuteKode) ?? $ruteList->first();

        $agenRute = $this->getDaftarAgen()
            ->where('status', 'aktif')
            ->where('rute_kode', $activeRute->kode_rute);

        $packingItems = $agenRute->map(function ($agen) {
            $jatah = $agen->jatah_default;
            $bendel = intdiv($jatah, 50);
            $eceran = $jatah % 50;

            return (object)[
                'kode_agen' => $agen->kode_agen,
                'nama_agen' => $agen->nama_agen,
                'wilayah' => $agen->wilayah,
                'telepon' => $agen->telepon,
                'kategori' => $agen->kategori,
                'total_eks' => $jatah,
                'bendel_50' => $bendel,
                'sisa_ecer' => $eceran,
                'kemasan' => "{$bendel} Bendel (" . ($bendel * 50) . " eks) + {$eceran} eks lepas",
            ];
        });

        $totalEksemplarRute = $packingItems->sum('total_eks');
        $totalBendelRute = $packingItems->sum('bendel_50');
        $totalEceranRute = $packingItems->sum('sisa_ecer');

        return view('pages.sirkulasi.logistik.surat-jalan', compact(
            'tanggalDistribusi',
            'ruteList',
            'activeRute',
            'packingItems',
            'totalEksemplarRute',
            'totalBendelRute',
            'totalEceranRute'
        ));
    }

    /**
     * 10. Logistik: Berita Acara Penerimaan Retur (BAPR)
     */
    public function returBapr(Request $request)
    {
        $today = Carbon::today();
        $tanggalBapr = $request->input('tanggal', $today->toDateString());

        $baprList = collect([
            (object)[
                'no_bapr' => 'BAPR/KP/2026/10/0142',
                'tanggal' => $today->toDateString(),
                'kode_agen' => 'AG-11-001',
                'nama_agen' => 'Kios Koran H. Dadang (Pasar Wetan)',
                'rute' => 'R-01 - Tasik Kota',
                'edisi_retur' => $today->copy()->subDay()->toDateString(),
                'jumlah_kirim' => 450,
                'retur_eks' => 15,
                'persentase' => 3.33,
                'harga_satuan' => 2800,
                'nilai_rupiah' => 42000,
                'status_verifikasi' => 'Terverifikasi Fisik',
                'status_akuntansi' => 'Sudah Masuk Pengurang Piutang',
            ],
            (object)[
                'no_bapr' => 'BAPR/KP/2026/10/0143',
                'tanggal' => $today->toDateString(),
                'kode_agen' => 'AG-11-002',
                'nama_agen' => 'Agen Berkah Ciamis Kota',
                'rute' => 'R-03 - Ciamis Kota',
                'edisi_retur' => $today->copy()->subDay()->toDateString(),
                'jumlah_kirim' => 600,
                'retur_eks' => 22,
                'persentase' => 3.67,
                'harga_satuan' => 2800,
                'nilai_rupiah' => 61600,
                'status_verifikasi' => 'Terverifikasi Fisik',
                'status_akuntansi' => 'Sudah Masuk Pengurang Piutang',
            ],
            (object)[
                'no_bapr' => 'BAPR/KP/2026/10/0144',
                'tanggal' => $today->toDateString(),
                'kode_agen' => 'AG-11-003',
                'nama_agen' => 'Agen Tarogong Prima Garut',
                'rute' => 'R-06 - Garut Kota',
                'edisi_retur' => $today->copy()->subDay()->toDateString(),
                'jumlah_kirim' => 550,
                'retur_eks' => 18,
                'persentase' => 3.27,
                'harga_satuan' => 2800,
                'nilai_rupiah' => 50400,
                'status_verifikasi' => 'Terverifikasi Fisik',
                'status_akuntansi' => 'Menunggu Sinkronisasi Kasir',
            ],
            (object)[
                'no_bapr' => 'BAPR/KP/2026/10/0145',
                'tanggal' => $today->toDateString(),
                'kode_agen' => 'AG-11-005',
                'nama_agen' => 'Agen Karangnunggal Sejahtera (Tasela)',
                'rute' => 'R-02 - Tasik Selatan',
                'edisi_retur' => $today->copy()->subDay()->toDateString(),
                'jumlah_kirim' => 320,
                'retur_eks' => 12,
                'persentase' => 3.75,
                'harga_satuan' => 2800,
                'nilai_rupiah' => 33600,
                'status_verifikasi' => 'Menunggu Fisik',
                'status_akuntansi' => 'Draft',
            ],
        ]);

        $totalReturEks = $baprList->sum('retur_eks');
        $totalNilaiRetur = $baprList->sum('nilai_rupiah');
        $agenList = $this->getDaftarAgen()->where('status', 'aktif');

        return view('pages.sirkulasi.logistik.retur', compact('baprList', 'tanggalBapr', 'totalReturEks', 'totalNilaiRetur', 'agenList'));
    }

    public function storeReturBapr(Request $request)
    {
        return redirect()->route('sirkulasi.logistik.retur')
            ->with('success', 'BAPR Koran Retur berhasil dicatat. Nilai pengurang piutang resmi telah dikirim ke modul Kasir & Akuntansi.');
    }

    /**
     * 11. Tutup Data Bulanan (Closing) & Kunci Status ke Akuntansi
     */
    public function closingBulanan(Request $request)
    {
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $carbonBulan = Carbon::parse($bulanPilihan . '-01');

        $riwayatClosing = collect([
            (object)[
                'periode' => 'September 2026',
                'periode_key' => '2026-09',
                'total_oplah_cetak' => 375000,
                'total_retur_eks' => 7420,
                'rasio_retur' => 1.98,
                'total_omset_bersih' => 1029224000,
                'status' => 'LOCKED',
                'status_label' => 'Ready to Pull Akunting',
                'closed_at' => '2026-09-30 23:59:00',
                'closed_by' => 'Manager Sirkulasi',
            ],
            (object)[
                'periode' => 'Agustus 2026',
                'periode_key' => '2026-08',
                'total_oplah_cetak' => 387500,
                'total_retur_eks' => 8150,
                'rasio_retur' => 2.10,
                'total_omset_bersih' => 1062180000,
                'status' => 'PULLED',
                'status_label' => 'Telah Diproses Akuntansi',
                'closed_at' => '2026-08-31 23:59:00',
                'closed_by' => 'Manager Sirkulasi',
            ],
        ]);

        $periodeSaatIni = (object)[
            'periode' => $carbonBulan->translatedFormat('F Y'),
            'periode_key' => $bulanPilihan,
            'total_oplah_cetak' => 175000,
            'total_terjual' => 171150,
            'total_retur_eks' => 3850,
            'rasio_retur' => 2.2,
            'omset_kotor' => 490000000,
            'potongan_retur' => 10780000,
            'omset_bersih' => 479220000,
            'is_locked' => false,
            'status' => 'OPEN',
            'status_label' => 'Operasional Aktif (Belum Tutup Buku)',
        ];

        return view('pages.sirkulasi.laporan.closing', compact('riwayatClosing', 'periodeSaatIni', 'bulanPilihan'));
    }

    public function lockDataBulanan(Request $request)
    {
        $bulan = $request->input('periode');
        return redirect()->route('sirkulasi.laporan.closing', ['bulan' => $bulan])
            ->with('success', "Data Penjualan & Distribusi Sirkulasi Periode {$bulan} berhasil DIKUNCI (Status: READY TO PULL AKUNTING). Data jatah dan retur terkunci dari perubahan.");
    }

    /**
     * 12. Rekap Pemasaran (Jatah Fisik Oplah)
     */
    public function rekapPemasaran(Request $request)
    {
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $ruteList = $this->getDaftarRute();

        $rekapRute = $ruteList->map(function ($rute) {
            $cetak = $rute->alokasi_oplah * 30;
            $retur = (int) ($cetak * 0.021);
            $gratis = (int) ($cetak * 0.025);
            $terjual = $cetak - $retur - $gratis;

            return (object)[
                'kode_rute' => $rute->kode_rute,
                'nama_jurusan' => $rute->nama_jurusan,
                'cetak' => $cetak,
                'gratis' => $gratis,
                'retur' => $retur,
                'terjual' => $terjual,
                'retur_pct' => round(($retur / $cetak) * 100, 2),
            ];
        });

        $totalCetak = $rekapRute->sum('cetak');
        $totalGratis = $rekapRute->sum('gratis');
        $totalRetur = $rekapRute->sum('retur');
        $totalTerjual = $rekapRute->sum('terjual');

        return view('pages.sirkulasi.laporan.pemasaran', compact('rekapRute', 'totalCetak', 'totalGratis', 'totalRetur', 'totalTerjual', 'bulanPilihan'));
    }

    /**
     * 13. Rekapitulasi Penjualan Sirkulasi (Finansial)
     */
    public function rekapPenjualan(Request $request)
    {
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $daftarAgen = $this->getDaftarAgen();

        $rekapAgen = $daftarAgen->map(function ($agen) {
            $rataJatah = $agen->jatah_default;
            $totalKirim = $rataJatah * 30;
            $returEks = (int) ($totalKirim * 0.025);
            $terjualEks = $totalKirim - $returEks;
            $hargaSatuan = $agen->kategori === '11' ? 2800 : 3200;
            
            $tagihanKotor = $totalKirim * $hargaSatuan;
            $potonganRetur = $returEks * $hargaSatuan;
            $tagihanBersih = $tagihanKotor - $potonganRetur;

            return (object)[
                'kode_agen' => $agen->kode_agen,
                'nama_agen' => $agen->nama_agen,
                'kategori' => $agen->kategori,
                'kategori_label' => $agen->kategori_label,
                'wilayah' => $agen->wilayah,
                'total_kirim' => $totalKirim,
                'retur_eks' => $returEks,
                'terjual_eks' => $terjualEks,
                'tagihan_kotor' => $tagihanKotor,
                'potongan_retur' => $potonganRetur,
                'tagihan_bersih' => $tagihanBersih,
                'saldo_piutang' => $agen->saldo_piutang,
                'status' => $agen->status,
            ];
        });

        $grandKotor = $rekapAgen->sum('tagihan_kotor');
        $grandRetur = $rekapAgen->sum('potongan_retur');
        $grandBersih = $rekapAgen->sum('tagihan_bersih');
        $grandPiutang = $rekapAgen->sum('saldo_piutang');

        return view('pages.sirkulasi.laporan.penjualan', compact('rekapAgen', 'grandKotor', 'grandRetur', 'grandBersih', 'grandPiutang', 'bulanPilihan'));
    }

    /**
     * 14. Menu Tambahan Khas Sirkulasi Kabar Priangan:
     * Database Pelanggan Koran Langganan (Dinas/Kantor/Perorangan)
     */
    public function pelanggan(Request $request)
    {
        $pelangganList = collect([
            (object)[
                'kode' => 'PLG-TSM-001',
                'nama' => 'Setda Kab. Tasikmalaya (Bag. Humas/Prokopim)',
                'kategori' => 'Instansi Pemerintah',
                'alamat' => 'Kompleks Perkantoran Pemkab Tasikmalaya, Bojongkoneng Singaparna',
                'loper' => 'Mamat Cihideung',
                'jumlah_eks' => 15,
                'tarif_bulanan' => 1500000,
                'status' => 'Aktif',
            ],
            (object)[
                'kode' => 'PLG-TSM-002',
                'nama' => 'Bale Kota Tasikmalaya (Sekretariat Daerah)',
                'kategori' => 'Instansi Pemerintah',
                'alamat' => 'Jl. Letnan Harun No. 1 Kota Tasikmalaya',
                'loper' => 'Kang Aep Dadaha',
                'jumlah_eks' => 25,
                'tarif_bulanan' => 2500000,
                'status' => 'Aktif',
            ],
            (object)[
                'kode' => 'PLG-CMS-003',
                'nama' => 'DPRD Kabupaten Ciamis',
                'kategori' => 'Instansi Pemerintah',
                'alamat' => 'Jl. Ir. H. Juanda No. 162 Ciamis',
                'loper' => 'Kios Alun-alun Ciamis',
                'jumlah_eks' => 20,
                'tarif_bulanan' => 2000000,
                'status' => 'Aktif',
            ],
            (object)[
                'kode' => 'PLG-BJR-004',
                'nama' => 'Polres Banjar Patroman',
                'kategori' => 'TNI/Polri',
                'alamat' => 'Jl. Siliwangi No. 145 Kota Banjar',
                'loper' => 'Kios Sinar Pagi Banjar',
                'jumlah_eks' => 10,
                'tarif_bulanan' => 1000000,
                'status' => 'Aktif',
            ],
            (object)[
                'kode' => 'PLG-GRT-005',
                'nama' => 'Bank BJB Cabang Garut',
                'kategori' => 'BUMN / Perbankan',
                'alamat' => 'Jl. Ahmad Yani No. 38 Garut Kota',
                'loper' => 'Agen Tarogong Prima',
                'jumlah_eks' => 8,
                'tarif_bulanan' => 800000,
                'status' => 'Aktif',
            ],
        ]);

        return view('pages.sirkulasi.master.pelanggan', compact('pelangganList'));
    }

    /**
     * 15. Menu Tambahan Khas Sirkulasi Kabar Priangan:
     * SPK Order Percetakan (Surat Perintah Kerja Cetak Harian)
     */
    public function orderCetak(Request $request)
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $orderCetak = (object)[
            'no_spk' => 'SPK/CETAK/' . $today->format('Y/m') . '/0189',
            'tanggal_cetak' => $today->translatedFormat('d F Y'),
            'edisi_terbit' => $tomorrow->translatedFormat('l, d F Y'),
            'jam_cetak_mulai' => '22:30 WIB',
            'jam_cetak_selesai' => '01:30 WIB',
            'pabrik_percetakan' => 'Percetakan PT Priangan Intermedia Grafika',
            'oplah_reguler' => 11250,
            'oplah_gratis' => 450,
            'oplah_cadangan' => 800,
            'total_oplah_cetak' => 12500,
            'jumlah_halaman' => 16,
            'tipe_kertas' => 'Newsprint 45 gsm',
            'status' => 'Siap Cetak Malam Ini',
        ];

        return view('pages.sirkulasi.logistik.order-cetak', compact('orderCetak', 'today', 'tomorrow'));
    }
}
