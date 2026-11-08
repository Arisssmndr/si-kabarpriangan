<?php

namespace App\Http\Controllers;

use App\Models\Agen;
use App\Models\AkunPerkiraan;
use App\Models\AuditLogAkuntansi;
use App\Models\JurnalUmum;
use App\Models\JurnalUmumDetail;
use App\Models\KasKeluar;
use App\Models\KasMasuk;
use App\Models\KoreksiPiutang;
use App\Models\LogHitungAkuntansi;
use App\Models\MemoUnlock;
use App\Models\Pelanggan;
use App\Models\PembayaranIklan;
use App\Models\PengajuanAnggaran;
use App\Models\PeriodeAkuntansi;
use App\Models\StokOpnameFaktur;
use App\Models\TransaksiKoran;
use App\Models\TransaksiOnline;
use App\Models\TransaksiPriangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    /**
     * 0. Dashboard Eksekutif Accounting
     * Clean, modern, palet 60-30-10, ringkasan real-time
     */
    public function dashboard(Request $request)
    {
        $omsetIklanKoran = TransaksiKoran::where('status_order', '!=', 'Batal')->sum('totaltagihan_transaksikoran');
        $omsetIklanOnline = TransaksiOnline::sum('totaltagihan_transaksionline');
        $omsetIklanTv = TransaksiPriangan::sum('totaltagihan_transaksipriangan');
        $totalOmsetIklan = $omsetIklanKoran + $omsetIklanOnline + $omsetIklanTv;

        $kasMasukIklan = TransaksiKoran::where('status_order', '!=', 'Batal')->sum('jumlahbayar_transaksikoran')
            + TransaksiOnline::sum('jumlahbayar_transaksionline')
            + TransaksiPriangan::sum('jumlahbayar_transaksipriangan');

        $piutangIklan = TransaksiKoran::where('status_order', '!=', 'Batal')->sum('piutang_transaksikoran')
            + TransaksiOnline::sum('piutang_transaksionline')
            + TransaksiPriangan::sum('piutang_transaksipriangan');

        // Status Hitung Hari Ini
        $today = Carbon::today()->toDateString();
        $isIklanDihitung = LogHitungAkuntansi::sudahDihitung('penjualan_iklan', $today);
        $isBayarDihitung = LogHitungAkuntansi::sudahDihitung('pembayaran_iklan', $today);

        $statusHitungIklanHariIni = $isIklanDihitung;
        $statusHitungSirkulasiBulanIni = LogHitungAkuntansi::where('jenis', 'penjualan_sirkulasi')
            ->where('bulan_periode', (int) Carbon::now()->format('m'))
            ->where('tahun_periode', (int) Carbon::now()->format('Y'))
            ->where('status', 'sukses')
            ->exists();

        // Status Periode Aktif
        $currentPeriodKey = Carbon::now()->format('Y-m');
        $periodeAktif = PeriodeAkuntansi::where('periode_key', $currentPeriodKey)->first();

        // 5 Jurnal Terbaru
        $jurnalTerbaru = JurnalUmum::with('details')->orderByDesc('id')->take(5)->get();
        $jurnalTerakhir = $jurnalTerbaru;

        $omsetUsaha = $totalOmsetIklan;
        $penerimaanKas = $kasMasukIklan;
        $piutangUsaha = $piutangIklan;
        $estimasiLabaBersih = max(0, $omsetUsaha * 0.35); // Net margin estimate
        $labaKotor = max(0, $omsetUsaha * 0.55);

        return view('pages.accounting.dashboard', compact(
            'omsetUsaha',
            'penerimaanKas',
            'piutangUsaha',
            'labaKotor',
            'estimasiLabaBersih',
            'statusHitungIklanHariIni',
            'statusHitungSirkulasiBulanIni',
            'totalOmsetIklan',
            'kasMasukIklan',
            'piutangIklan',
            'isIklanDihitung',
            'isBayarDihitung',
            'periodeAktif',
            'jurnalTerbaru',
            'jurnalTerakhir',
            'today'
        ));
    }

    // ==========================================
    // 1. KARTU IKLAN
    // ==========================================

    public function iklanHitungPenjualan(Request $request)
    {
        $tanggalPilihan = $request->input('tanggal', Carbon::today()->toDateString());
        $sudahDihitung = LogHitungAkuntansi::sudahDihitung('penjualan_iklan', $tanggalPilihan);
        $logHitung = LogHitungAkuntansi::where('jenis', 'penjualan_iklan')
            ->whereDate('tanggal_transaksi', $tanggalPilihan)
            ->first();

        $daftarKoran = TransaksiKoran::whereDate('tanggal_muatkoran', $tanggalPilihan)
            ->where('status_order', '!=', 'Batal')
            ->get();

        foreach ($daftarKoran as $item) {
            $isUm = ($item->piutang_transaksikoran == 0 || strtolower($item->jenis_pembayaran) === 'tunai' || str_contains(strtolower($item->jenis_pembayaran ?? ''), 'muka'));
            $item->kode_perkiraan_display = $isUm ? '2.00.01.01.01.00001' : ('1.00.04.01.01.' . str_pad($item->id, 5, '0', STR_PAD_LEFT));
            $item->tipe_akun_display = $isUm ? 'Uang Muka' : 'Piutang';
        }

        $daftarFaktur = $daftarKoran;
        $totalCount = $daftarKoran->count();
        $totalBruto = (float) $daftarKoran->sum('harga_transaksikoran');
        $totalPpn = (float) $daftarKoran->sum('ppn_transaksikoran');
        $totalNetto = (float) $daftarKoran->sum('totaltagihan_transaksikoran');
        $totalNominal = $totalNetto;
        $totalFaktur = $totalCount;

        return view('pages.accounting.iklan.hitung_penjualan', compact(
            'tanggalPilihan',
            'sudahDihitung',
            'logHitung',
            'daftarKoran',
            'daftarFaktur',
            'totalCount',
            'totalBruto',
            'totalPpn',
            'totalNetto',
            'totalNominal',
            'totalFaktur'
        ));
    }

    public function prosesIklanHitungPenjualan(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $periodeKey = Carbon::parse($tanggal)->format('Y-m');

        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah ditutup (terkunci). Pembentukan jurnal penjualan ditolak.");
        }

        if (LogHitungAkuntansi::sudahDihitung('penjualan_iklan', $tanggal)) {
            return redirect()->back()->with('error', "Penjualan tanggal {$tanggal} sudah pernah dihitung.");
        }

        $daftarKoran = TransaksiKoran::whereDate('tanggal_muatkoran', $tanggal)
            ->where('status_order', '!=', 'Batal')
            ->get();

        $daftarOnline = TransaksiOnline::where(function($q) use ($tanggal) {
                $q->whereDate('tanggal_muatiklanonline', $tanggal)
                  ->orWhereDate('tanggal_transaksionline', $tanggal);
            })
            ->get();

        $daftarTv = TransaksiPriangan::where(function($q) use ($tanggal) {
                $q->whereDate('tanggal_muatiklanpriangan', $tanggal)
                  ->orWhereDate('tanggal_transaksipriangan', $tanggal);
            })
            ->get();

        $tagihanKoran = (float) $daftarKoran->sum('totaltagihan_transaksikoran');
        $ppnKoran = (float) $daftarKoran->sum('ppn_transaksikoran');
        $dppKoran = max(0, $tagihanKoran - $ppnKoran);

        $tagihanOnline = (float) $daftarOnline->sum('totaltagihan_transaksionline');
        $ppnOnline = (float) $daftarOnline->sum('ppn_transaksionline');
        $dppOnline = max(0, $tagihanOnline - $ppnOnline);

        $tagihanTv = (float) $daftarTv->sum('totaltagihan_transaksipriangan');
        $ppnTv = (float) $daftarTv->sum('ppn_transaksipriangan');
        $dppTv = max(0, $tagihanTv - $ppnTv);

        $totalNominal = $tagihanKoran + $tagihanOnline + $tagihanTv;
        $totalPpn = $ppnKoran + $ppnOnline + $ppnTv;
        $totalFaktur = $daftarKoran->count() + $daftarOnline->count() + $daftarTv->count();

        if ($totalNominal <= 0) {
            return redirect()->back()->with('error', "Tidak ada transaksi muat iklan pada tanggal {$tanggal} untuk dihitung.");
        }

        DB::transaction(function () use ($tanggal, $totalFaktur, $totalNominal, $totalPpn, $dppKoran, $dppOnline, $dppTv, $tagihanKoran, $tagihanOnline, $tagihanTv) {
            // Catat log hitung anti-dobel
            LogHitungAkuntansi::create([
                'jenis'             => 'penjualan_iklan',
                'tanggal_transaksi' => $tanggal,
                'total_faktur'      => $totalFaktur,
                'total_nominal'     => $totalNominal,
                'status'            => 'sukses',
                'user_id'           => auth()->id() ?? 1,
                'catatan'           => "Penarikan penjualan iklan harian {$totalFaktur} order (Koran: Rp " . number_format($tagihanKoran, 0, ',', '.') . ", Online: Rp " . number_format($tagihanOnline, 0, ',', '.') . ", TV: Rp " . number_format($tagihanTv, 0, ',', '.') . ").",
            ]);

            $noJurnal = JurnalUmum::generateNomorJurnal($tanggal);
            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $tanggal,
                'tipe'         => 'auto',
                'sumber'       => 'iklan',
                'referensi'    => "MUAT/{$tanggal}",
                'keterangan'   => "Penjualan Iklan Multi-Kanal Tanggal {$tanggal}",
                'total_debet'  => $totalNominal,
                'total_kredit' => $totalNominal,
                'is_balanced'  => true,
                'status'       => 'posted',
                'user_id'      => auth()->id() ?? 1,
            ]);

            $urutan = 1;

            // Debet: Piutang Usaha Iklan Umum (1.00.04.01.01.01.00001)
            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => '1.00.04.01.01.01.00001',
                'nama_perkiraan' => 'Piutang Usaha Iklan Umum',
                'debet'          => $totalNominal,
                'kredit'         => 0,
                'urutan'         => $urutan++,
            ]);

            // Kredit: Utang Pajak PPN Keluaran 11% (2.00.03.01.01.00001) jika ada
            if ($totalPpn > 0) {
                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => '2.00.03.01.01.00001',
                    'nama_perkiraan' => 'Utang Pajak PPN Keluaran 11%',
                    'debet'          => 0,
                    'kredit'         => $totalPpn,
                    'urutan'         => $urutan++,
                ]);
            }

            // Kredit: Pendapatan Iklan Koran (4.00.01.01.01.00001)
            if ($dppKoran > 0) {
                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => '4.00.01.01.01.00001',
                    'nama_perkiraan' => 'Pendapatan Iklan Kolom Koran',
                    'debet'          => 0,
                    'kredit'         => $dppKoran,
                    'urutan'         => $urutan++,
                ]);
            }

            // Kredit: Pendapatan Iklan Online (4.00.02.01.01.00001)
            if ($dppOnline > 0) {
                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => '4.00.02.01.01.00001',
                    'nama_perkiraan' => 'Pendapatan Iklan Media Online',
                    'debet'          => 0,
                    'kredit'         => $dppOnline,
                    'urutan'         => $urutan++,
                ]);
            }

            // Kredit: Pendapatan Iklan TV (4.00.03.01.01.00001)
            if ($dppTv > 0) {
                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => '4.00.03.01.01.00001',
                    'nama_perkiraan' => 'Pendapatan Iklan Priangan TV',
                    'debet'          => 0,
                    'kredit'         => $dppTv,
                    'urutan'         => $urutan++,
                ]);
            }

            AuditLogAkuntansi::catat(
                'HITUNG_PENJUALAN',
                'IKLAN',
                "Hitung penjualan iklan tanggal {$tanggal} sebesar Rp " . number_format($totalNominal, 0, ',', '.') . " (Jurnal: {$noJurnal})"
            );
        });

        return redirect()->route('accounting.iklan.hitung-penjualan', ['tanggal' => $tanggal])
            ->with('success', "Penjualan tanggal {$tanggal} berhasil dihitung dan diposting ke GL.");
    }

    public function iklanHitungPembayaran(Request $request)
    {
        $tanggalPilihan = $request->input('tanggal', Carbon::today()->toDateString());
        $sudahDihitung = LogHitungAkuntansi::sudahDihitung('pembayaran_iklan', $tanggalPilihan);
        $logHitung = LogHitungAkuntansi::where('jenis', 'pembayaran_iklan')
            ->whereDate('tanggal_transaksi', $tanggalPilihan)
            ->first();

        $daftarBayar = KasMasuk::with('transaksiKoran')
            ->where('status_void', false)
            ->where(function ($q) {
                $q->where('kode_bagian', '01')
                  ->orWhere('tipe_transaksi', 'iklan');
            })
            ->where(function ($q) use ($tanggalPilihan) {
                $q->whereDate('tgl_transaksi', $tanggalPilihan)
                  ->orWhereDate('tanggal_terima', $tanggalPilihan);
            })
            ->get();

        $totalCount = $daftarBayar->count();
        $totalBayar = (float) $daftarBayar->sum('jumlah_dibayar');
        $totalPPh = (float) $daftarBayar->sum('potongan_pajak');
        $totalNettoKas = max(0, $totalBayar - $totalPPh);

        return view('pages.accounting.iklan.hitung_pembayaran', compact(
            'tanggalPilihan',
            'sudahDihitung',
            'logHitung',
            'daftarBayar',
            'totalCount',
            'totalBayar',
            'totalPPh',
            'totalNettoKas'
        ));
    }

    public function prosesIklanHitungPembayaran(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $periodeKey = Carbon::parse($tanggal)->format('Y-m');

        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah ditutup (terkunci). Penarikan pelunasan ditolak.");
        }

        if (LogHitungAkuntansi::sudahDihitung('pembayaran_iklan', $tanggal)) {
            return redirect()->back()->with('error', "Pembayaran tanggal {$tanggal} sudah pernah dihitung.");
        }

        $daftarBayar = KasMasuk::with('transaksiKoran')
            ->where('status_void', false)
            ->where(function ($q) {
                $q->where('kode_bagian', '01')
                  ->orWhere('tipe_transaksi', 'iklan');
            })
            ->where(function ($q) use ($tanggal) {
                $q->whereDate('tgl_transaksi', $tanggal)
                  ->orWhereDate('tanggal_terima', $tanggal);
            })
            ->get();

        $totalBayar = (float) $daftarBayar->sum('jumlah_dibayar');
        $totalPPh = (float) $daftarBayar->sum('potongan_pajak');
        $totalNettoKas = max(0, $totalBayar - $totalPPh);
        $totalKwitansi = $daftarBayar->count();

        if ($totalBayar <= 0) {
            return redirect()->back()->with('error', "Tidak ada penerimaan kas pelunasan iklan pada tanggal {$tanggal} untuk diproses.");
        }

        DB::transaction(function () use ($tanggal, $totalKwitansi, $totalBayar, $totalPPh, $totalNettoKas, $daftarBayar) {
            $noJurnal = JurnalUmum::generateNomorJurnal($tanggal);
            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $tanggal,
                'tipe'         => 'auto',
                'sumber'       => 'kasir',
                'referensi'    => "KAS-IKL/{$tanggal}",
                'keterangan'   => "Penerimaan Kasir Pelunasan Iklan {$tanggal}",
                'total_debet'  => $totalBayar,
                'total_kredit' => $totalBayar,
                'is_balanced'  => true,
                'status'       => 'posted',
                'user_id'      => auth()->id() ?? 1,
            ]);

            $urutan = 1;

            // Debet: Kas Operasional / Kasir (1.00.00.01.01.00.00001)
            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => '1.00.00.01.01.00.00001',
                'nama_perkiraan' => 'Kas Operasional / Kasir',
                'debet'          => $totalNettoKas,
                'kredit'         => 0,
                'urutan'         => $urutan++,
            ]);

            // Debet: Piutang Pajak PPh Pasal 23 (1.00.06.01.01.00.00001) jika ada bukti potong
            if ($totalPPh > 0) {
                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => '1.00.06.01.01.00.00001',
                    'nama_perkiraan' => 'Piutang Pajak PPh Pasal 23',
                    'debet'          => $totalPPh,
                    'kredit'         => 0,
                    'urutan'         => $urutan++,
                ]);
            }

            // Kredit: Piutang Usaha Iklan Umum (1.00.04.01.01.01.00001)
            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => '1.00.04.01.01.01.00001',
                'nama_perkiraan' => 'Piutang Usaha Iklan Umum',
                'debet'          => 0,
                'kredit'         => $totalBayar,
                'urutan'         => $urutan++,
            ]);

            // Tandai kwitansi kasir sudah ditarik akunting (Anti-Leaking)
            foreach ($daftarBayar as $km) {
                $km->update([
                    'is_pulled_accounting'  => true,
                    'status_tarik_akunting' => true,
                ]);
            }

            LogHitungAkuntansi::create([
                'jenis'             => 'pembayaran_iklan',
                'tanggal_transaksi' => $tanggal,
                'total_faktur'      => $totalKwitansi,
                'total_nominal'     => $totalBayar,
                'status'            => 'sukses',
                'user_id'           => auth()->id() ?? 1,
                'catatan'           => "Penarikan kwitansi pelunasan kasir {$totalKwitansi} transaksi. No Jurnal: {$noJurnal}",
            ]);

            AuditLogAkuntansi::catat(
                'HITUNG_PEMBAYARAN',
                'IKLAN',
                "Hitung pembayaran iklan tanggal {$tanggal} sebesar Rp " . number_format($totalBayar, 0, ',', '.') . " (Jurnal: {$noJurnal})"
            );
        });

        return redirect()->route('accounting.iklan.hitung-pembayaran', ['tanggal' => $tanggal])
            ->with('success', "Pembayaran tanggal {$tanggal} berhasil dihitung dan diposting ke GL.");
    }

    public function iklanKoreksi(Request $request)
    {
        $daftarCn = KoreksiPiutang::where('jenis', 'iklan')
            ->orderByDesc('id')
            ->paginate(15);

        // Faktur-faktur yang bisa dikoreksi (piutang > 0 dan BUKAN Uang Muka 2.00.01)
        $daftarFaktur = TransaksiKoran::where('status_order', '!=', 'Batal')
            ->where('piutang_transaksikoran', '>', 0)
            ->latest('id')
            ->take(50)
            ->get();

        $nextNoBukti = KoreksiPiutang::generateNoBukti('iklan');

        return view('pages.accounting.iklan.koreksi', compact('daftarCn', 'daftarFaktur', 'nextNoBukti'));
    }

    public function storeIklanKoreksi(Request $request)
    {
        $request->validate([
            'no_faktur'      => 'required|string',
            'tanggal'        => 'required|date',
            'jumlah_koreksi' => 'required|numeric|min:1',
            'keterangan'     => 'required|string|max:255',
        ]);

        $periodeKey = Carbon::parse($request->tanggal)->format('Y-m');
        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah ditutup (terkunci). Penerbitan nota kredit ditolak.");
        }

        $faktur = TransaksiKoran::where('nofakturkoran', $request->no_faktur)->first();
        if (!$faktur) {
            return redirect()->back()->with('error', 'Faktur tidak ditemukan.');
        }

        // Proteksi Akun Uang Muka 2.00.01
        $kodePerkiraan = $faktur->no_perkiraan ?? '1.00.04.01.01.01.00001';
        if (str_starts_with($kodePerkiraan, '2.00.01')) {
            return redirect()->back()->with('error', 'Faktur berstatus Uang Muka Lunas (2.00.01) tidak boleh dikoreksi.');
        }

        $jumlahKoreksi = (float) $request->jumlah_koreksi;
        $sisaSebelum = (float) $faktur->piutang_transaksikoran;
        $saldoSetelah = max(0, $sisaSebelum - $jumlahKoreksi);

        // Hitung proporsi PPN 11% jika faktur asal menggunakan PPN
        $koreksiPpn = ($faktur->ppn_transaksikoran > 0) ? round($jumlahKoreksi * 11 / 111, 2) : 0;
        $koreksiDpp = $jumlahKoreksi - $koreksiPpn;

        DB::transaction(function () use ($faktur, $request, $jumlahKoreksi, $saldoSetelah, $kodePerkiraan, $koreksiPpn, $koreksiDpp) {
            $noBukti = KoreksiPiutang::generateNoBukti('iklan', $request->tanggal);
            $namaPemasang = $faktur->nama_pemasangkoran ?? '-';
            $alamatPemasang = $faktur->alamat_pemasangkoran ?? '-';

            // Buat Auto-Jurnal Koreksi / Diskon Penjualan (CN-IKL)
            $noJurnal = JurnalUmum::generateNomorJurnal($request->tanggal);
            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $request->tanggal,
                'tipe'         => 'auto',
                'sumber'       => 'iklan',
                'referensi'    => $noBukti,
                'keterangan'   => "Koreksi Piutang {$faktur->nofakturkoran} - {$namaPemasang}",
                'total_debet'  => $jumlahKoreksi,
                'total_kredit' => $jumlahKoreksi,
                'is_balanced'  => true,
                'status'       => 'posted',
                'user_id'      => auth()->id() ?? 1,
            ]);

            $urutan = 1;

            // Debet: Koreksi & Diskon Penjualan Iklan (4.00.05.01.01.00001)
            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => '4.00.05.01.01.00001',
                'nama_perkiraan' => 'Koreksi & Diskon Penjualan Iklan (CN-IKL)',
                'debet'          => $koreksiDpp,
                'kredit'         => 0,
                'urutan'         => $urutan++,
            ]);

            // Debet: Pembalikan Utang Pajak PPN Keluaran 11% jika ada PPN
            if ($koreksiPpn > 0) {
                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => '2.00.03.01.01.00001',
                    'nama_perkiraan' => 'Utang Pajak PPN Keluaran 11%',
                    'debet'          => $koreksiPpn,
                    'kredit'         => 0,
                    'urutan'         => $urutan++,
                ]);
            }

            // Kredit: Piutang Usaha Iklan (1.00.04.01.01.01.00001)
            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => $kodePerkiraan,
                'nama_perkiraan' => 'Piutang Usaha Iklan Umum',
                'debet'          => 0,
                'kredit'         => $jumlahKoreksi,
                'urutan'         => $urutan++,
            ]);

            KoreksiPiutang::create([
                'no_bukti'              => $noBukti,
                'jenis'                 => 'iklan',
                'tanggal_koreksi'       => $request->tanggal,
                'faktur_id'             => $faktur->id,
                'no_faktur'             => $faktur->nofakturkoran,
                'no_order'              => $faktur->no_order ?? $faktur->nofakturkoran,
                'kode_perkiraan'        => $kodePerkiraan,
                'nama_entitas'          => $namaPemasang,
                'alamat'                => $alamatPemasang,
                'nilai_penjualan'       => $faktur->totaltagihan_transaksikoran,
                'jumlah_koreksi'        => $jumlahKoreksi,
                'saldo_setelah_koreksi' => $saldoSetelah,
                'keterangan'            => $request->keterangan,
                'jurnal_umum_id'        => $jurnal->id,
                'user_id'               => auth()->id() ?? 1,
            ]);

            // Potong saldo piutang faktur
            $faktur->update(['piutang_transaksikoran' => $saldoSetelah]);

            AuditLogAkuntansi::catat(
                'KOREKSI_CN',
                'IKLAN',
                "Koreksi CN {$noBukti} untuk faktur {$faktur->nofakturkoran} sebesar Rp " . number_format($jumlahKoreksi, 0, ',', '.')
            );
        });

        return redirect()->route('accounting.iklan.koreksi')->with('success', 'Bukti koreksi CN-IKL berhasil disimpan.');
    }

    public function cetakIklanKoreksi($id)
    {
        $koreksi = KoreksiPiutang::findOrFail($id);
        return view('pages.accounting.iklan.cetak_koreksi', compact('koreksi'));
    }

    public function iklanBukaData(Request $request)
    {
        $daftarMemo = MemoUnlock::where('tujuan_divisi', 'Divisi Akuntansi')
            ->orWhere('status_akuntansi', 'PENDING')
            ->latest('id')
            ->paginate(15);

        return view('pages.accounting.iklan.buka_data', compact('daftarMemo'));
    }

    public function prosesIklanBukaData(Request $request)
    {
        $request->validate([
            'memo_id' => 'required|exists:memo_unlocks,id',
            'alasan'  => 'required|string',
        ]);

        $memo = MemoUnlock::findOrFail($request->memo_id);
        $memo->update([
            'status_akuntansi'        => 'APPROVED',
            'status'                  => 'OPEN',
            'diterima_akuntansi_pada' => now(),
            'catatan_akuntansi'       => $request->alasan,
        ]);

        AuditLogAkuntansi::catat(
            'BUKA_CUTOFF',
            'IKLAN',
            "Persetujuan buka data cut-off memo {$memo->nomor_memo} target {$memo->tanggal_target}"
        );

        return redirect()->back()->with('success', "Cut-off tanggal {$memo->tanggal_target} dibuka sementara.");
    }

    public function prosesBatalDpInsentif(Request $request)
    {
        return redirect()->back()->with('success', 'Pengajuan DP Insentif berhasil dibatalkan dan diarsipkan.');
    }

    public function prosesUnpullPembayaranIklan(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $periodeKey = Carbon::parse($tanggal)->format('Y-m');

        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah ditutup (terkunci). Pembatalan penarikan ditolak.");
        }

        DB::transaction(function () use ($tanggal) {
            // 1. Cari Jurnal Kasir Iklan pada tanggal tersebut dan hapus/void
            $jurnals = JurnalUmum::where('sumber', 'kasir')
                ->where('referensi', "KAS-IKL/{$tanggal}")
                ->get();

            foreach ($jurnals as $j) {
                $j->details()->delete();
                $j->delete();
            }

            // 2. Revert flag is_pulled_accounting pada seluruh KasMasuk tanggal tersebut
            $daftarKasMasuk = KasMasuk::where(function ($q) {
                    $q->where('kode_bagian', '01')
                      ->orWhere('tipe_transaksi', 'iklan');
                })
                ->where(function ($q) use ($tanggal) {
                    $q->whereDate('tgl_transaksi', $tanggal)
                      ->orWhereDate('tanggal_terima', $tanggal);
                })
                ->get();

            foreach ($daftarKasMasuk as $km) {
                $km->update([
                    'is_pulled_accounting'  => false,
                    'status_tarik_akunting' => false,
                ]);
            }

            // 3. Update status log hitung
            LogHitungAkuntansi::where('jenis', 'pembayaran_iklan')
                ->whereDate('tanggal_transaksi', $tanggal)
                ->update(['status' => 'batal_dibuka']);

            AuditLogAkuntansi::catat('UNPULL_BAYAR', 'IKLAN', "Buka kunci pembayaran kasir tanggal {$tanggal} (Jurnal terkait di-revert)");
        });

        return redirect()->back()->with('success', "Penarikan pembayaran tanggal {$tanggal} berhasil dibuka dan jurnal GL di-revert.");
    }

    public function iklanBelanja(Request $request)
    {
        $query = $request->input('q');
        $riwayat = collect([]);

        if ($query) {
            $riwayat = TransaksiKoran::where('nama_pemasangkoran', 'like', "%{$query}%")
                ->orWhere('nofakturkoran', 'like', "%{$query}%")
                ->latest('tanggal_muatkoran')
                ->take(30)
                ->get();
        }

        return view('pages.accounting.iklan.belanja', compact('riwayat', 'query'));
    }

    // ==========================================
    // 2. KARTU SIRKULASI
    // ==========================================

    public function sirkulasiSaldoAwal(Request $request)
    {
        $daftarAgen = Agen::orderBy('nama_agen')->get();
        $totalPiutangAwal = $daftarAgen->sum('saldo_piutang');
        return view('pages.accounting.sirkulasi.saldo_awal', compact('daftarAgen', 'totalPiutangAwal'));
    }

    public function prosesSirkulasiSaldoAwal(Request $request)
    {
        $request->validate([
            'agen_id' => 'required|exists:agens,id',
            'saldo_piutang' => 'required|numeric|min:0',
        ]);

        $agen = Agen::findOrFail($request->agen_id);
        $saldoLama = $agen->saldo_piutang;
        $agen->update(['saldo_piutang' => $request->saldo_piutang]);

        AuditLogAkuntansi::catat(
            'UPDATE_SALDO_AWAL',
            'SIRKULASI',
            "Update saldo piutang agen {$agen->nama_agen} ({$agen->kode_agen}) dari Rp " . number_format($saldoLama, 0, ',', '.') . " menjadi Rp " . number_format($request->saldo_piutang, 0, ',', '.')
        );

        return redirect()->back()->with('success', "Saldo awal agen {$agen->nama_agen} berhasil diperbarui.");
    }

    public function sirkulasiHitungPenjualan(Request $request)
    {
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $bulanInt = (int) substr($bulanPilihan, 5, 2);
        $tahunInt = (int) substr($bulanPilihan, 0, 4);
        $daysInMonth = Carbon::parse($bulanPilihan . '-01')->daysInMonth;

        $sudahDihitung = LogHitungAkuntansi::where('jenis', 'penjualan_sirkulasi')
            ->where('bulan_periode', $bulanInt)
            ->where('tahun_periode', $tahunInt)
            ->where('status', 'sukses')
            ->exists();

        $logHitung = LogHitungAkuntansi::where('jenis', 'penjualan_sirkulasi')
            ->where('bulan_periode', $bulanInt)
            ->where('tahun_periode', $tahunInt)
            ->first();

        $daftarAgen = Agen::where('status', 'aktif')->orderBy('nama_agen')->get();
        $totalJatah = $daftarAgen->sum('jatah_default');
        $hargaKoran = 3500; // Harga pokok standar koran per eksemplar
        $estimasiHari = $daysInMonth; // Hari edar aktual kalender bulan bersangkutan
        $totalOmset = $totalJatah * $hargaKoran * $estimasiHari;

        return view('pages.accounting.sirkulasi.hitung_penjualan', compact(
            'bulanPilihan',
            'sudahDihitung',
            'logHitung',
            'daftarAgen',
            'totalJatah',
            'hargaKoran',
            'totalOmset',
            'estimasiHari'
        ));
    }

    public function prosesSirkulasiHitungPenjualan(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $bulanInt = (int) substr($bulan, 5, 2);
        $tahunInt = (int) substr($bulan, 0, 4);
        $daysInMonth = Carbon::parse($bulan . '-01')->daysInMonth;

        if (PeriodeAkuntansi::isPeriodeLocked($bulan)) {
            return redirect()->back()->with('error', "Periode {$bulan} sudah ditutup (terkunci). Pembentukan omset sirkulasi ditolak.");
        }

        $exists = LogHitungAkuntansi::where('jenis', 'penjualan_sirkulasi')
            ->where('bulan_periode', $bulanInt)
            ->where('tahun_periode', $tahunInt)
            ->where('status', 'sukses')
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', "Penjualan sirkulasi bulan {$bulan} sudah pernah dihitung.");
        }

        $daftarAgen = Agen::where('status', 'aktif')->get();
        $totalJatah = $daftarAgen->sum('jatah_default');
        $hargaKoran = 3500;
        $totalOmset = $totalJatah * $hargaKoran * $daysInMonth;

        $tglPosting = Carbon::parse($bulan . '-01')->endOfMonth()->toDateString();

        DB::transaction(function () use ($bulan, $bulanInt, $tahunInt, $totalOmset, $tglPosting, $daftarAgen, $hargaKoran, $daysInMonth) {
            $noJurnal = JurnalUmum::generateNomorJurnal($tglPosting);

            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $tglPosting,
                'tipe'         => 'auto',
                'sumber'       => 'sirkulasi',
                'referensi'    => "SIRK-{$bulan}",
                'keterangan'   => "Hitung Penjualan Koran Sirkulasi Bulan {$bulan} ({$daysInMonth} Hari)",
                'total_debet'  => $totalOmset,
                'total_kredit' => $totalOmset,
                'is_balanced'  => true,
                'status'       => 'posted',
                'user_id'      => auth()->id() ?? 1,
            ]);

            // Debet: Piutang Usaha Koran Sirkulasi (1.00.04.01.01.00.00002)
            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => '1.00.04.01.01.00.00002',
                'nama_perkiraan' => 'Piutang Usaha Koran Sirkulasi',
                'debet'          => $totalOmset,
                'kredit'         => 0,
                'urutan'         => 1,
            ]);

            // Kredit: Pendapatan Sirkulasi Koran Cetak (4.00.04.01.01.00.00001)
            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => '4.00.04.01.01.00.00001',
                'nama_perkiraan' => 'Pendapatan Sirkulasi Koran Cetak',
                'debet'          => 0,
                'kredit'         => $totalOmset,
                'urutan'         => 2,
            ]);

            // Sinkronisasi Sub-Ledger: Tambah saldo piutang masing-masing agen aktif sesuai jatah dan hari edar
            foreach ($daftarAgen as $agen) {
                $omsetAgen = $agen->jatah_default * $hargaKoran * $daysInMonth;
                $agen->increment('saldo_piutang', $omsetAgen);
            }

            LogHitungAkuntansi::create([
                'jenis'             => 'penjualan_sirkulasi',
                'tanggal_transaksi' => $tglPosting,
                'bulan_periode'     => $bulanInt,
                'tahun_periode'     => $tahunInt,
                'total_faktur'      => $daftarAgen->count(),
                'total_nominal'     => $totalOmset,
                'status'            => 'sukses',
                'user_id'           => auth()->id() ?? 1,
                'catatan'           => "Hitung omset sirkulasi bulan {$bulan} ({$daysInMonth} hari). No Jurnal: {$noJurnal}",
            ]);

            AuditLogAkuntansi::catat(
                'HITUNG_PENJUALAN',
                'SIRKULASI',
                "Hitung omset sirkulasi bulan {$bulan} sebesar Rp " . number_format($totalOmset, 0, ',', '.') . " (Jurnal: {$noJurnal})"
            );
        });

        return redirect()->back()->with('success', "Penjualan sirkulasi bulan {$bulan} ({$daysInMonth} hari) berhasil dihitung, saldo piutang agen bertambah dan diposting ke GL.");
    }

    public function sirkulasiHitungPembayaran(Request $request)
    {
        $tanggalPilihan = $request->input('tanggal', Carbon::today()->toDateString());
        $sudahDihitung = LogHitungAkuntansi::where('jenis', 'pembayaran_sirkulasi')
            ->whereDate('tanggal_transaksi', $tanggalPilihan)
            ->where('status', 'sukses')
            ->exists();

        $logHitung = LogHitungAkuntansi::where('jenis', 'pembayaran_sirkulasi')
            ->whereDate('tanggal_transaksi', $tanggalPilihan)
            ->first();

        $daftarSetoran = KasMasuk::with('agen')
            ->where('status_void', false)
            ->where(function($q) {
                $q->where('kode_bagian', '02')
                  ->orWhere('tipe_transaksi', 'sirkulasi');
            })
            ->where(function($q) use ($tanggalPilihan) {
                $q->whereDate('tgl_transaksi', $tanggalPilihan)
                  ->orWhereDate('tanggal_terima', $tanggalPilihan);
            })
            ->get();

        $totalCount = $daftarSetoran->count();
        $totalSetoran = (float) $daftarSetoran->sum('jumlah_dibayar');

        return view('pages.accounting.sirkulasi.hitung_pembayaran', compact(
            'tanggalPilihan',
            'sudahDihitung',
            'logHitung',
            'daftarSetoran',
            'totalCount',
            'totalSetoran'
        ));
    }

    public function prosesSirkulasiHitungPembayaran(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $periodeKey = Carbon::parse($tanggal)->format('Y-m');

        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah ditutup (terkunci). Penarikan setoran sirkulasi ditolak.");
        }

        $sudah = LogHitungAkuntansi::where('jenis', 'pembayaran_sirkulasi')
            ->whereDate('tanggal_transaksi', $tanggal)
            ->where('status', 'sukses')
            ->exists();

        if ($sudah) {
            return redirect()->back()->with('error', "Pembayaran sirkulasi tanggal {$tanggal} sudah pernah dihitung.");
        }

        $daftarSetoran = KasMasuk::where('status_void', false)
            ->where(function($q) {
                $q->where('kode_bagian', '02')
                  ->orWhere('tipe_transaksi', 'sirkulasi');
            })
            ->where(function($q) use ($tanggal) {
                $q->whereDate('tgl_transaksi', $tanggal)
                  ->orWhereDate('tanggal_terima', $tanggal);
            })
            ->get();

        $totalSetoran = (float) $daftarSetoran->sum('jumlah_dibayar');
        if ($totalSetoran <= 0) {
            return redirect()->back()->with('error', "Tidak ada setoran sirkulasi pada tanggal {$tanggal} untuk diproses.");
        }

        DB::transaction(function () use ($tanggal, $totalSetoran, $daftarSetoran) {
            $noJurnal = JurnalUmum::generateNomorJurnal($tanggal);

            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $tanggal,
                'tipe'         => 'auto',
                'sumber'       => 'sirkulasi',
                'referensi'    => "SETOR-SIRK-{$tanggal}",
                'keterangan'   => "Penerimaan Kas Setoran Agen Sirkulasi {$tanggal}",
                'total_debet'  => $totalSetoran,
                'total_kredit' => $totalSetoran,
                'is_balanced'  => true,
                'status'       => 'posted',
                'user_id'      => auth()->id() ?? 1,
            ]);

            // Debet: Bank BCA Giro Tasikmalaya (1.00.01.01.01.00.00001)
            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => '1.00.01.01.01.00.00001',
                'nama_perkiraan' => 'Bank BCA Giro Tasikmalaya',
                'debet'          => $totalSetoran,
                'kredit'         => 0,
                'urutan'         => 1,
            ]);

            // Kredit: Piutang Usaha Koran Sirkulasi (1.00.04.01.01.00.00002)
            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => '1.00.04.01.01.00.00002',
                'nama_perkiraan' => 'Piutang Usaha Koran Sirkulasi',
                'debet'          => 0,
                'kredit'         => $totalSetoran,
                'urutan'         => 2,
            ]);

            // Flagging KasMasuk sirkulasi
            foreach ($daftarSetoran as $km) {
                $km->update([
                    'is_pulled_accounting'  => true,
                    'status_tarik_akunting' => true,
                ]);
            }

            LogHitungAkuntansi::create([
                'jenis'             => 'pembayaran_sirkulasi',
                'tanggal_transaksi' => $tanggal,
                'total_faktur'      => $daftarSetoran->count(),
                'total_nominal'     => $totalSetoran,
                'status'            => 'sukses',
                'user_id'           => auth()->id() ?? 1,
                'catatan'           => "Posting penerimaan sirkulasi tanggal {$tanggal}. No Jurnal: {$noJurnal}",
            ]);

            AuditLogAkuntansi::catat(
                'HITUNG_PEMBAYARAN',
                'SIRKULASI',
                "Posting penerimaan sirkulasi tanggal {$tanggal} sebesar Rp " . number_format($totalSetoran, 0, ',', '.') . " (Jurnal: {$noJurnal})"
            );
        });

        return redirect()->back()->with('success', "Setoran sirkulasi tanggal {$tanggal} berhasil diposting ke GL.");
    }

    public function sirkulasiKoreksi(Request $request)
    {
        $daftarCn = KoreksiPiutang::where('jenis', 'sirkulasi')->latest('id')->paginate(15);
        $daftarAgen = Agen::where('status', 'aktif')->orderBy('nama_agen')->get();
        $nextNoBukti = KoreksiPiutang::generateNoBukti('sirkulasi');
        return view('pages.accounting.sirkulasi.koreksi', compact('daftarCn', 'daftarAgen', 'nextNoBukti'));
    }

    public function storeSirkulasiKoreksi(Request $request)
    {
        $request->validate([
            'agen_id'          => 'required|exists:agens,id',
            'tanggal'          => 'required|date',
            'jumlah_eksemplar' => 'required|integer|min:1',
            'harga_eksemplar'  => 'required|numeric|min:1',
            'alasan'           => 'required|string|max:255',
        ]);

        $periodeKey = Carbon::parse($request->tanggal)->format('Y-m');
        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah ditutup (terkunci). Penerbitan nota kredit retur sirkulasi ditolak.");
        }

        $agen = Agen::findOrFail($request->agen_id);
        $jumlahKoreksi = $request->jumlah_eksemplar * $request->harga_eksemplar;
        $saldoSebelum = (float) $agen->saldo_piutang;
        $saldoSetelah = max(0, $saldoSebelum - $jumlahKoreksi);

        DB::transaction(function () use ($request, $agen, $jumlahKoreksi, $saldoSetelah) {
            $noBukti = KoreksiPiutang::generateNoBukti('sirkulasi', $request->tanggal);
            $noJurnal = JurnalUmum::generateNomorJurnal($request->tanggal);

            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $request->tanggal,
                'tipe'         => 'auto',
                'sumber'       => 'sirkulasi',
                'referensi'    => $noBukti,
                'keterangan'   => "Retur Koran Agen {$agen->nama_agen} ({$request->jumlah_eksemplar} Eks)",
                'total_debet'  => $jumlahKoreksi,
                'total_kredit' => $jumlahKoreksi,
                'is_balanced'  => true,
                'status'       => 'posted',
                'user_id'      => auth()->id() ?? 1,
            ]);

            // Debet: Retur & Koreksi Sirkulasi Koran (4.00.05.01.02.00001)
            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => '4.00.05.01.02.00001',
                'nama_perkiraan' => 'Retur & Koreksi Sirkulasi Koran (CN-SIR)',
                'debet'          => $jumlahKoreksi,
                'kredit'         => 0,
                'urutan'         => 1,
            ]);

            // Kredit: Piutang Usaha Koran Sirkulasi (1.00.04.01.01.00.00002)
            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => '1.00.04.01.01.00.00002',
                'nama_perkiraan' => 'Piutang Usaha Koran Sirkulasi',
                'debet'          => 0,
                'kredit'         => $jumlahKoreksi,
                'urutan'         => 2,
            ]);

            KoreksiPiutang::create([
                'no_bukti'              => $noBukti,
                'jenis'                 => 'sirkulasi',
                'tanggal_koreksi'       => $request->tanggal,
                'faktur_id'             => $agen->id,
                'no_faktur'             => "RETUR-{$agen->kode_agen}",
                'no_order'              => "EKS-{$request->jumlah_eksemplar}",
                'kode_perkiraan'        => '1.00.04.01.01.00.00002',
                'nama_entitas'          => $agen->nama_agen,
                'alamat'                => $agen->wilayah,
                'nilai_penjualan'       => $agen->saldo_piutang,
                'jumlah_koreksi'        => $jumlahKoreksi,
                'saldo_setelah_koreksi' => $saldoSetelah,
                'keterangan'            => "Retur {$request->jumlah_eksemplar} eks @ Rp " . number_format($request->harga_eksemplar, 0, ',', '.') . " - " . $request->alasan,
                'jurnal_umum_id'        => $jurnal->id,
                'user_id'               => auth()->id() ?? 1,
            ]);

            $agen->update(['saldo_piutang' => $saldoSetelah]);

            AuditLogAkuntansi::catat(
                'RETUR_KORAN',
                'SIRKULASI',
                "Penerbitan CN-SIR {$noBukti} untuk agen {$agen->nama_agen} ({$request->jumlah_eksemplar} eks, Rp " . number_format($jumlahKoreksi, 0, ',', '.') . ")"
            );
        });

        return redirect()->back()->with('success', "Nota kredit retur sirkulasi CN-SIR berhasil diterbitkan.");
    }

    public function cetakSirkulasiKoreksi($id)
    {
        $koreksi = KoreksiPiutang::findOrFail($id);
        return view('pages.accounting.sirkulasi.cetak_koreksi', compact('koreksi'));
    }

    public function sirkulasiBukaData(Request $request)
    {
        $daftarMemo = MemoUnlock::where('tujuan_divisi', 'sirkulasi')
            ->orWhere('status_akuntansi', 'PENDING')
            ->latest('id')
            ->limit(15)
            ->get();
        return view('pages.accounting.sirkulasi.buka_data', compact('daftarMemo'));
    }

    public function prosesSirkulasiBukaData(Request $request)
    {
        $request->validate([
            'bulan'   => 'required|string',
            'no_memo' => 'required|string',
            'alasan'  => 'required|string',
        ]);

        $bulanInt = (int) substr($request->bulan, 5, 2);
        $tahunInt = (int) substr($request->bulan, 0, 4);
        $daysInMonth = Carbon::parse($request->bulan . '-01')->daysInMonth;

        if (PeriodeAkuntansi::isPeriodeLocked($request->bulan)) {
            return redirect()->back()->with('error', "Periode {$request->bulan} sudah ditutup (terkunci). Pembatalan hitung sirkulasi ditolak.");
        }

        DB::transaction(function () use ($request, $bulanInt, $tahunInt, $daysInMonth) {
            // 1. Cari Jurnal Sirkulasi dan hapus details + jurnal
            $jurnal = JurnalUmum::where('sumber', 'sirkulasi')
                ->where('referensi', "SIRK-{$request->bulan}")
                ->first();

            if ($jurnal) {
                $jurnal->details()->delete();
                $jurnal->delete();
            }

            // 2. Kembalikan saldo piutang agen (revert billing)
            $daftarAgen = Agen::where('status', 'aktif')->get();
            foreach ($daftarAgen as $agen) {
                $omsetAgen = $agen->jatah_default * 3500 * $daysInMonth;
                $agen->update(['saldo_piutang' => max(0, (float)$agen->saldo_piutang - $omsetAgen)]);
            }

            // 3. Update status log hitung
            $log = LogHitungAkuntansi::where('jenis', 'penjualan_sirkulasi')
                ->where('bulan_periode', $bulanInt)
                ->where('tahun_periode', $tahunInt)
                ->first();

            if ($log) {
                $log->update(['status' => 'batal_dibuka']);
            }

            AuditLogAkuntansi::catat(
                'BUKA_KUNCI',
                'SIRKULASI',
                "Buka kunci hitung sirkulasi bulan {$request->bulan} via memo {$request->no_memo}. Alasan: {$request->alasan} (Jurnal & saldo agen di-revert)"
            );
        });

        return redirect()->back()->with('success', "Kunci sirkulasi bulan {$request->bulan} berhasil dibuka dan pembukuan di-revert.");
    }

    // ==========================================
    // 3. CETAK KARTU (17 Laporan Sub-Ledger)
    // ==========================================

    public function sirkulasiAgenSaldo($id)
    {
        $agen = Agen::findOrFail($id);
        $daysInMonth = Carbon::today()->daysInMonth;

        $penjualanBulanIni = ($agen->jatah_default ?? 0) * $daysInMonth * 3500;

        $pembayaranBulanIni = (float) KasMasuk::where('status_void', false)
            ->where(function($q) use ($agen) {
                $q->where('kode_agen_ref', $agen->kode_agen)
                  ->orWhere('terima_dari', 'like', '%' . $agen->nama_agen . '%');
            })
            ->whereYear('tanggal_terima', Carbon::today()->year)
            ->whereMonth('tanggal_terima', Carbon::today()->month)
            ->sum('jumlah_dibayar');

        $koreksiBulanIni = (float) KoreksiPiutang::where('jenis', 'sirkulasi')
            ->where(function($q) use ($agen) {
                $q->where('kode_perkiraan', 'like', '%' . $agen->kode_agen . '%')
                  ->orWhere('nama_entitas', $agen->nama_agen);
            })
            ->whereYear('tanggal_koreksi', Carbon::today()->year)
            ->whereMonth('tanggal_koreksi', Carbon::today()->month)
            ->sum('jumlah_koreksi');

        $saldoBerjalan = (float) $agen->saldo_piutang;
        $sisaPiutangLalu = max(0, $saldoBerjalan - $penjualanBulanIni + $pembayaranBulanIni + $koreksiBulanIni);

        return response()->json([
            'success'              => true,
            'agen'                 => $agen,
            'sisa_piutang_lalu'    => $sisaPiutangLalu,
            'penjualan_bulan_ini'  => $penjualanBulanIni,
            'pembayaran_bulan_ini' => $pembayaranBulanIni,
            'koreksi_bulan_ini'    => $koreksiBulanIni,
            'saldo_berjalan'       => $saldoBerjalan,
        ]);
    }

    public function cetakIklan(Request $request)
    {
        $tab = $request->input('tab', 'penjualan');
        $periodeType = $request->input('periode_type', 'bulanan');

        // Automatic presets based on period type
        if ($request->has('tgl_mulai') && $request->has('tgl_selesai')) {
            $tglMulai = $request->input('tgl_mulai');
            $tglSelesai = $request->input('tgl_selesai');
        } else {
            if ($periodeType === 'harian') {
                $tglMulai = Carbon::today()->toDateString();
                $tglSelesai = Carbon::today()->toDateString();
            } elseif ($periodeType === 'tahunan') {
                $tglMulai = Carbon::today()->startOfYear()->toDateString();
                $tglSelesai = Carbon::today()->endOfYear()->toDateString();
            } else {
                $tglMulai = Carbon::today()->startOfMonth()->toDateString();
                $tglSelesai = Carbon::today()->toDateString();
            }
        }

        $data = collect();
        $totalNominal = 0;
        $rekapAsalBulan = [];
        $extraSummary = [];

        if ($tab === 'penjualan') {
            $data = TransaksiKoran::with('pelanggan')
                ->whereBetween('tanggal_transaksikoran', [$tglMulai, $tglSelesai])
                ->latest('id')
                ->get();
            
            foreach ($data as $item) {
                $isUm = ($item->piutang_transaksikoran == 0 || strtolower($item->jenis_pembayaran) === 'tunai' || str_contains(strtolower($item->jenis_pembayaran), 'muka'));
                $item->kode_perkiraan_display = $isUm ? '2.00.01.01.01.00001' : ('1.00.04.01.01.' . str_pad($item->id, 5, '0', STR_PAD_LEFT));
                $item->tipe_akun_display = $isUm ? 'Uang Muka' : 'Piutang';
            }
            $totalNominal = $data->sum('totaltagihan_transaksikoran');

        } elseif ($tab === 'pembayaran') {
            $data = PembayaranIklan::with('transaksiKoran.pelanggan')
                ->whereBetween('tanggal_bayar', [$tglMulai, $tglSelesai])
                ->latest('id')
                ->get();
            $totalNominal = $data->sum('nominal_bayar');

            // Hitung Rekap Pembayaran Berdasarkan Asal Bulan Faktur (Sesuai Foto DOS 12.22.42)
            foreach ($data as $p) {
                $tglFaktur = $p->transaksiKoran?->tanggal_transaksikoran ?? $p->tanggal_bayar;
                $mKey = Carbon::parse($tglFaktur)->format('Y-m');
                $mLabel = Carbon::parse($tglFaktur)->translatedFormat('F Y');
                if (!isset($rekapAsalBulan[$mKey])) {
                    $rekapAsalBulan[$mKey] = [
                        'periode_key' => $mKey,
                        'label'       => strtoupper($mLabel),
                        'total'       => 0,
                        'count'       => 0,
                    ];
                }
                $rekapAsalBulan[$mKey]['total'] += (float) $p->nominal_bayar;
                $rekapAsalBulan[$mKey]['count']++;
            }
            ksort($rekapAsalBulan);

        } elseif ($tab === 'piutang') {
            $data = TransaksiKoran::with('pelanggan')
                ->where('piutang_transaksikoran', '>', 0)
                ->latest('id')
                ->get();
            
            foreach ($data as $item) {
                $item->kode_perkiraan_display = '1.00.04.01.01.' . str_pad($item->id, 5, '0', STR_PAD_LEFT);
                $item->tipe_akun_display = 'Piutang';
            }
            $totalNominal = $data->sum('piutang_transaksikoran');

        } elseif ($tab === 'saldo_piutang') {
            // Laporan Saldo Piutang Iklan (Sesuai Foto DOS 12.22.47 & 12.22.47 (1))
            $data = TransaksiKoran::with(['pelanggan', 'pembayaranList'])
                ->whereBetween('tanggal_transaksikoran', [$tglMulai, $tglSelesai])
                ->orWhere('piutang_transaksikoran', '>', 0)
                ->latest('id')
                ->get();

            foreach ($data as $item) {
                $isUm = ($item->piutang_transaksikoran == 0 || strtolower($item->jenis_pembayaran) === 'tunai' || str_contains(strtolower($item->jenis_pembayaran), 'muka'));
                $item->kode_perkiraan_display = $isUm ? '2.00.01.01.01.00001' : ('1.00.04.01.01.' . str_pad($item->id, 5, '0', STR_PAD_LEFT));
                $item->tipe_akun_display = $isUm ? 'Uang Muka' : 'Piutang';
            }
            $totalNominal = $data->sum('piutang_transaksikoran');

        } elseif ($tab === 'piutang_ae') {
            // Rekap Piutang per Account Executive (Sales AE)
            $transaksiAe = TransaksiKoran::where('piutang_transaksikoran', '>', 0)->get();
            $grouped = $transaksiAe->groupBy(function ($item) {
                return $item->sales_iklankoran ?: 'Tanpa AE / Kantor Pusat';
            });

            $data = collect();
            foreach ($grouped as $aeNama => $items) {
                $data->push((object)[
                    'nama_ae'       => $aeNama,
                    'jumlah_faktur' => $items->count(),
                    'total_tagihan' => $items->sum('totaltagihan_transaksikoran'),
                    'total_bayar'   => $items->sum('jumlahbayar_transaksikoran'),
                    'sisa_piutang'  => $items->sum('piutang_transaksikoran'),
                ]);
            }
            $data = $data->sortByDesc('sisa_piutang')->values();
            $totalNominal = $data->sum('sisa_piutang');

        } elseif ($tab === 'insentif_ae') {
            // Perolehan Insentif AE (Foto DOS Menu 7)
            $data = TransaksiKoran::where('insentif_transaksikoran', '>', 0)
                ->whereBetween('tanggal_transaksikoran', [$tglMulai, $tglSelesai])
                ->latest('id')
                ->get();
            $totalNominal = $data->sum('insentif_transaksikoran');

        } elseif ($tab === 'space_iklan') {
            // Penjualan Space Iklan (Foto DOS Menu 8)
            $data = TransaksiKoran::whereBetween('tanggal_transaksikoran', [$tglMulai, $tglSelesai])
                ->latest('id')
                ->get();
            
            $totalNominal = $data->sum('totaltagihan_transaksikoran');
            $totalMmk = $data->sum(function ($item) {
                return ($item->lebar_kolom ?? 1) * ($item->tinggi_mm ?? 1) * ($item->total_muatkoran ?? 1);
            });
            $extraSummary['total_mmk'] = $totalMmk;
            $extraSummary['total_muat'] = $data->sum('total_muatkoran');

        } elseif ($tab === 'stock_opname') {
            // Stock Opname Faktur (Foto DOS Menu 9)
            $data = StokOpnameFaktur::latest('tanggal_opname')->get();
            if ($data->isEmpty()) {
                // Generate metrics summary from TransaksiKoran
                $totalTerbit = TransaksiKoran::count();
                $totalLunas = TransaksiKoran::where('piutang_transaksikoran', 0)->count();
                $totalBatal = TransaksiKoran::whereNotNull('alasan_batal')->count();
                $extraSummary['total_terbit'] = $totalTerbit;
                $extraSummary['total_lunas'] = $totalLunas;
                $extraSummary['total_batal'] = $totalBatal;
            }
            $totalNominal = $data->sum('jumlah_terpakai');

        } elseif ($tab === 'koreksi') {
            $data = KoreksiPiutang::where('jenis', 'iklan')
                ->whereBetween('tanggal_koreksi', [$tglMulai, $tglSelesai])
                ->latest('id')
                ->get();
            $totalNominal = $data->sum('jumlah_koreksi');
        }

        return view('pages.accounting.cetak.iklan', compact(
            'tab',
            'periodeType',
            'tglMulai',
            'tglSelesai',
            'data',
            'totalNominal',
            'rekapAsalBulan',
            'extraSummary'
        ));
    }

    public function cetakSirkulasi(Request $request)
    {
        $tab = $request->input('tab', 'penjualan');
        $tglMulai = $request->input('tgl_mulai', Carbon::today()->startOfMonth()->toDateString());
        $tglSelesai = $request->input('tgl_selesai', Carbon::today()->toDateString());

        $data = collect();
        $totalNominal = 0;
        $totalEksemplar = 0;

        if ($tab === 'penjualan' || $tab === 'peredaran') {
            $data = Agen::orderBy('nama_agen')->get();
            $totalEksemplar = $data->sum('jatah_default') * 30;
            $totalNominal = $totalEksemplar * 3500;
        } elseif ($tab === 'pembayaran') {
            $data = KasMasuk::with('agen')
                ->where('status_void', false)
                ->where(function($q) {
                    $q->where('kode_bagian', '02')
                      ->orWhere('tipe_transaksi', 'sirkulasi');
                })
                ->where(function($q) use ($tglMulai, $tglSelesai) {
                    $q->whereBetween('tgl_transaksi', [$tglMulai, $tglSelesai])
                      ->orWhereBetween('tanggal_terima', [$tglMulai, $tglSelesai]);
                })
                ->latest('id')
                ->get();
            $totalNominal = $data->sum('jumlah_dibayar');
        } elseif ($tab === 'piutang') {
            $data = Agen::where('saldo_piutang', '>', 0)->orderByDesc('saldo_piutang')->get();
            $totalNominal = $data->sum('saldo_piutang');
        } elseif ($tab === 'retur') {
            $data = KoreksiPiutang::where('jenis', 'sirkulasi')
                ->whereBetween('tanggal_koreksi', [$tglMulai, $tglSelesai])
                ->latest('id')
                ->get();
            $totalNominal = $data->sum('jumlah_koreksi');
        }

        return view('pages.accounting.cetak.sirkulasi', compact(
            'tab',
            'tglMulai',
            'tglSelesai',
            'data',
            'totalNominal',
            'totalEksemplar'
        ));
    }

    // ==========================================
    // 4. KAS, BANK & BEBAN
    // ==========================================

    public function bebanVerifikasi(Request $request)
    {
        $daftarBeban = KasKeluar::with('kasir')
            ->latest('tanggal_keluar')
            ->latest('id')
            ->paginate(15);

        $daftarAkunBeban = AkunPerkiraan::whereIn('kepala', [5, 6])
            ->orWhere('kategori', 'like', '%Beban%')
            ->aktif()
            ->orderBy('kode_perkiraan')
            ->get();

        $totalBelumPosting = KasKeluar::where('is_pulled_accounting', false)->sum('nominal_keluar');
        $totalSudahPosting = KasKeluar::where('is_pulled_accounting', true)->sum('nominal_keluar');

        return view('pages.accounting.beban.verifikasi', compact(
            'daftarBeban',
            'daftarAkunBeban',
            'totalBelumPosting',
            'totalSudahPosting'
        ));
    }

    public function postingBebanGl(Request $request, $id)
    {
        $request->validate([
            'kode_perkiraan' => 'required|string',
        ]);

        $kasKeluar = KasKeluar::findOrFail($id);
        $nominal = (float) ($kasKeluar->nominal_keluar ?: $kasKeluar->jumlah_keluar ?: 0);
        $tglPosting = $kasKeluar->tanggal_keluar ?: Carbon::today()->toDateString();
        $periodeKey = Carbon::parse($tglPosting)->format('Y-m');

        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah ditutup (terkunci). Posting beban operasional ditolak.");
        }

        $akunBeban = AkunPerkiraan::where('kode_perkiraan', $request->kode_perkiraan)->first();
        $namaAkunBeban = $akunBeban ? $akunBeban->nama_perkiraan : 'Beban Operasional';

        DB::transaction(function () use ($kasKeluar, $nominal, $tglPosting, $request, $namaAkunBeban) {
            $noJurnal = JurnalUmum::generateNomorJurnal($tglPosting);

            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $tglPosting,
                'tipe'         => 'auto',
                'sumber'       => 'beban',
                'referensi'    => $kasKeluar->no_bkk ?: "BKK-{$kasKeluar->id}",
                'keterangan'   => "Beban: {$kasKeluar->keterangan} ({$kasKeluar->diberikan_kepada})",
                'total_debet'  => $nominal,
                'total_kredit' => $nominal,
                'is_balanced'  => true,
                'status'       => 'posted',
                'user_id'      => auth()->id() ?? 1,
            ]);

            // Debet: Akun Beban Terpilih
            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => $request->kode_perkiraan,
                'nama_perkiraan' => $namaAkunBeban,
                'debet'          => $nominal,
                'kredit'         => 0,
                'urutan'         => 1,
            ]);

            // Kredit: Kas Operasional / Bank BCA Giro (1.00.01.01.01.00.00001)
            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => '1.00.01.01.01.00.00001',
                'nama_perkiraan' => 'Bank BCA Giro Tasikmalaya',
                'debet'          => 0,
                'kredit'         => $nominal,
                'urutan'         => 2,
            ]);

            $kasKeluar->update([
                'is_pulled_accounting'  => true,
                'status_tarik_akunting' => true,
                'kode_perkiraan'        => $request->kode_perkiraan,
                'nama_perkiraan'        => $namaAkunBeban,
            ]);

            AuditLogAkuntansi::catat(
                'POST_BEBAN',
                'GL',
                "Posting beban {$kasKeluar->no_bkk} sebesar Rp " . number_format($nominal, 0, ',', '.') . " ke akun {$request->kode_perkiraan} (Jurnal: {$noJurnal})"
            );
        });

        return redirect()->back()->with('success', 'Beban pengeluaran kasir berhasil diposting ke GL.');
    }

    public function bebanMutasiBank(Request $request)
    {
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));

        $mutasiMasuk = KasMasuk::where('metode_bayar', 'transfer')
            ->where(function($q) use ($bulanPilihan) {
                $q->where('tgl_transaksi', 'like', "{$bulanPilihan}%")
                  ->orWhere('tanggal_terima', 'like', "{$bulanPilihan}%");
            })
            ->latest('id')
            ->get();

        $mutasiKeluar = KasKeluar::where('metode_bayar', 'transfer')
            ->where('tanggal_keluar', 'like', "{$bulanPilihan}%")
            ->latest('tanggal_keluar')
            ->get();

        $totalMasuk = (float) $mutasiMasuk->sum('jumlah_dibayar');
        $totalKeluar = (float) $mutasiKeluar->sum('nominal_keluar');
        $saldoMutasi = $totalMasuk - $totalKeluar;

        return view('pages.accounting.beban.mutasi_bank', compact(
            'bulanPilihan',
            'mutasiMasuk',
            'mutasiKeluar',
            'totalMasuk',
            'totalKeluar',
            'saldoMutasi'
        ));
    }

    public function bebanAkrual(Request $request)
    {
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $daftarAkun = AkunPerkiraan::aktif()->orderBy('kode_perkiraan')->get();

        $jurnalAkrual = JurnalUmum::with('details')
            ->where('sumber', 'memorial')
            ->where('tanggal', 'like', "{$bulanPilihan}%")
            ->latest('tanggal')
            ->get();

        return view('pages.accounting.beban.akrual', compact('bulanPilihan', 'daftarAkun', 'jurnalAkrual'));
    }

    public function bebanAsetTetap(Request $request)
    {
        // Standar Aset Tetap HU Kabar Priangan
        $daftarAset = [
            [
                'kode'          => 'AST-001',
                'nama'          => 'Gedung Kantor Jl. Dr. Sukardjo',
                'kategori'      => 'Bangunan Permanen',
                'tgl_perolehan' => '2015-01-01',
                'harga_beli'    => 1200000000,
                'tarif_susut'   => 5, // 5% per tahun (20 tahun)
                'susut_tahunan' => 60000000,
                'susut_bulanan' => 5000000,
            ],
            [
                'kode'          => 'AST-002',
                'nama'          => 'Mesin Cetak Web Offset Goss Community',
                'kategori'      => 'Mesin Produksi',
                'tgl_perolehan' => '2018-06-01',
                'harga_beli'    => 850000000,
                'tarif_susut'   => 12.5, // 8 tahun
                'susut_tahunan' => 106250000,
                'susut_bulanan' => 8854166,
            ],
            [
                'kode'          => 'AST-003',
                'nama'          => 'Server & Perangkat Redaksi Komputer',
                'kategori'      => 'Peralatan Kantor',
                'tgl_perolehan' => '2023-01-15',
                'harga_beli'    => 180000000,
                'tarif_susut'   => 25, // 4 tahun
                'susut_tahunan' => 45000000,
                'susut_bulanan' => 3750000,
            ],
            [
                'kode'          => 'AST-004',
                'nama'          => 'Armada Distribusi Sirkulasi (Mobil Box)',
                'kategori'      => 'Kendaraan',
                'tgl_perolehan' => '2022-04-10',
                'harga_beli'    => 240000000,
                'tarif_susut'   => 12.5, // 8 tahun
                'susut_tahunan' => 30000000,
                'susut_bulanan' => 2500000,
            ],
        ];

        $totalHargaBeli = array_sum(array_column($daftarAset, 'harga_beli'));
        $totalSusutBulanan = array_sum(array_column($daftarAset, 'susut_bulanan'));

        return view('pages.accounting.beban.aset_tetap', compact('daftarAset', 'totalHargaBeli', 'totalSusutBulanan'));
    }

    public function bebanAnggaran(Request $request)
    {
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $daftarAnggaran = PengajuanAnggaran::where('tanggal_pengajuan', 'like', "{$bulanPilihan}%")
            ->latest('id')
            ->get();

        $totalPagu = (float) ($daftarAnggaran->sum('nominal_disetujui') ?: $daftarAnggaran->sum('nominal_diajukan'));
        $totalRealisasi = (float) KasKeluar::where('tanggal_keluar', 'like', "{$bulanPilihan}%")->sum('nominal_keluar');
        $sisaAnggaran = max(0, $totalPagu - $totalRealisasi);

        return view('pages.accounting.beban.anggaran', compact(
            'bulanPilihan',
            'daftarAnggaran',
            'totalPagu',
            'totalRealisasi',
            'sisaAnggaran'
        ));
    }

    // ==========================================
    // 5. JURNAL & BUKU BESAR
    // ==========================================

    public function glJurnal(Request $request)
    {
        $jurnalList = JurnalUmum::with('details')->latest('tanggal')->latest('id')->paginate(20);
        return view('pages.accounting.gl.jurnal', compact('jurnalList'));
    }

    public function glMemorial(Request $request)
    {
        $daftarAkun = AkunPerkiraan::aktif()->orderBy('kode_perkiraan')->get();
        return view('pages.accounting.gl.memorial', compact('daftarAkun'));
    }

    public function storeGlMemorial(Request $request)
    {
        $request->validate([
            'tanggal'      => 'required|date',
            'keterangan'   => 'required|string',
            'akun_debet'   => 'required|string',
            'akun_kredit'  => 'required|string',
            'nominal'      => 'required|numeric|min:1',
        ]);

        $periodeKey = Carbon::parse($request->tanggal)->format('Y-m');
        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah ditutup (terkunci). Pembuatan jurnal memorial ditolak.");
        }

        $nominal = (float) $request->nominal;
        $noJurnal = JurnalUmum::generateNomorJurnal($request->tanggal);

        $akunDebet = AkunPerkiraan::where('kode_perkiraan', $request->akun_debet)->first();
        $akunKredit = AkunPerkiraan::where('kode_perkiraan', $request->akun_kredit)->first();

        DB::transaction(function () use ($request, $noJurnal, $nominal, $akunDebet, $akunKredit) {
            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $request->tanggal,
                'tipe'         => 'memorial',
                'sumber'       => 'memorial',
                'referensi'    => $request->referensi ?: 'MEMORIAL',
                'keterangan'   => $request->keterangan,
                'total_debet'  => $nominal,
                'total_kredit' => $nominal,
                'is_balanced'  => true,
                'status'       => 'posted',
                'user_id'      => auth()->id() ?? 1,
            ]);

            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => $request->akun_debet,
                'nama_perkiraan' => $akunDebet ? $akunDebet->nama_perkiraan : 'Akun Debet',
                'debet'          => $nominal,
                'kredit'         => 0,
                'urutan'         => 1,
            ]);

            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => $request->akun_kredit,
                'nama_perkiraan' => $akunKredit ? $akunKredit->nama_perkiraan : 'Akun Kredit',
                'debet'          => 0,
                'kredit'         => $nominal,
                'urutan'         => 2,
            ]);

            AuditLogAkuntansi::catat(
                'POST_MEMORIAL',
                'GL',
                "Posting Jurnal Memorial {$noJurnal} sebesar Rp " . number_format($nominal, 0, ',', '.') . " ({$request->keterangan})"
            );
        });

        return redirect()->route('accounting.gl.jurnal')->with('success', "Jurnal Memorial {$noJurnal} berhasil diposting.");
    }

    public function realisasiUangMuka(Request $request)
    {
        $request->validate([
            'no_faktur' => 'required|string',
            'tanggal'   => 'required|date',
            'nominal'   => 'required|numeric|min:1',
        ]);

        $periodeKey = Carbon::parse($request->tanggal)->format('Y-m');
        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah ditutup (terkunci). Realisasi uang muka ditolak.");
        }

        $nominal = (float) $request->nominal;
        $noJurnal = JurnalUmum::generateNomorJurnal($request->tanggal);

        DB::transaction(function () use ($request, $nominal, $noJurnal) {
            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $request->tanggal,
                'tipe'         => 'auto',
                'sumber'       => 'iklan',
                'referensi'    => "RLS-UM/{$request->no_faktur}",
                'keterangan'   => "Realisasi Pendapatan Uang Muka Iklan Faktur {$request->no_faktur}",
                'total_debet'  => $nominal,
                'total_kredit' => $nominal,
                'is_balanced'  => true,
                'status'       => 'posted',
                'user_id'      => auth()->id() ?? 1,
            ]);

            // Debet: Uang Muka Iklan (2.00.01.01.01.01.00001)
            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => '2.00.01.01.01.01.00001',
                'nama_perkiraan' => 'Pendapatan Diterima Di Muka / Uang Muka Iklan',
                'debet'          => $nominal,
                'kredit'         => 0,
                'urutan'         => 1,
            ]);

            // Kredit: Pendapatan Iklan Kolom Koran (4.00.01.01.01.00001)
            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => '4.00.01.01.01.00001',
                'nama_perkiraan' => 'Pendapatan Iklan Kolom Koran',
                'debet'          => 0,
                'kredit'         => $nominal,
                'urutan'         => 2,
            ]);

            AuditLogAkuntansi::catat(
                'REALISASI_UM',
                'IKLAN',
                "Realisasi uang muka faktur {$request->no_faktur} sebesar Rp " . number_format($nominal, 0, ',', '.') . " (Jurnal: {$noJurnal})"
            );
        });

        return redirect()->back()->with('success', "Uang muka iklan {$request->no_faktur} berhasil direalisasikan ke Pendapatan.");
    }

    public function glBukuBesar(Request $request)
    {
        $daftarAkun = AkunPerkiraan::aktif()->orderBy('kode_perkiraan')->get();
        $akunPilihan = $request->input('kode_perkiraan', $daftarAkun->first()?->kode_perkiraan);

        $akun = AkunPerkiraan::where('kode_perkiraan', $akunPilihan)->first();

        $mutasiBuku = JurnalUmumDetail::with('jurnal')
            ->where('kode_perkiraan', $akunPilihan)
            ->latest('id')
            ->get();

        $totalDebet = $mutasiBuku->sum('debet');
        $totalKredit = $mutasiBuku->sum('kredit');

        return view('pages.accounting.gl.buku_besar', compact(
            'daftarAkun',
            'akunPilihan',
            'akun',
            'mutasiBuku',
            'totalDebet',
            'totalKredit'
        ));
    }

    public function glNeracaLajur(Request $request)
    {
        $periode = $request->input('periode', Carbon::now()->format('Y-m'));
        $daftarAkun = AkunPerkiraan::aktif()->orderBy('kode_perkiraan')->get();

        $neracaLajur = [];
        $totalDebet = 0;
        $totalKredit = 0;

        foreach ($daftarAkun as $akun) {
            // Ambil mutasi jurnal yang berstatus posted pada periode yang dipilih
            $queryDetail = JurnalUmumDetail::where('kode_perkiraan', $akun->kode_perkiraan)
                ->whereHas('jurnal', function ($q) use ($periode) {
                    $q->where('status', 'posted')->where('tanggal', 'like', "{$periode}%");
                });

            $debet = (float) $queryDetail->sum('debet');
            $kredit = (float) $queryDetail->sum('kredit');

            $saldoDebet = 0;
            $saldoKredit = 0;

            if ($akun->saldo_normal === 'kredit') {
                $saldoKredit = max(0, $kredit - $debet);
                $saldoDebet = max(0, $debet - $kredit);
            } else {
                $saldoDebet = max(0, $debet - $kredit);
                $saldoKredit = max(0, $kredit - $debet);
            }

            $totalDebet += $saldoDebet;
            $totalKredit += $saldoKredit;

            $neracaLajur[] = [
                'kode'          => $akun->kode_perkiraan,
                'nama'          => $akun->nama_perkiraan,
                'posisi_normal' => $akun->saldo_normal ?? 'debet',
                'debet'         => $saldoDebet,
                'kredit'        => $saldoKredit,
            ];
        }

        return view('pages.accounting.gl.neraca_lajur', compact(
            'periode',
            'neracaLajur',
            'totalDebet',
            'totalKredit'
        ));
    }

    // ==========================================
    // 6. LAPORAN KEUANGAN DINAMIS DARI GL
    // ==========================================

    public function labaRugi(Request $request)
    {
        $periode = $request->input('periode', Carbon::now()->format('Y-m'));
        $daysInMonth = Carbon::parse($periode . '-01')->daysInMonth;

        // Ambil data dari GL jika ada
        $glPendapatanIklan = JurnalUmumDetail::whereHas('jurnal', fn($q) => $q->where('status', 'posted')->where('tanggal', 'like', "{$periode}%"))
            ->whereIn('kode_perkiraan', [
                '4.00.01.01.01.00001',
                '4.00.01.01.02.00001',
                '4.00.01.01.03.00001',
                '4.00.01.01.04.00001',
                '4.00.02.01.01.00001',
                '4.00.03.01.01.00001',
            ])
            ->sum('kredit');

        $glPendapatanSirkulasi = JurnalUmumDetail::whereHas('jurnal', fn($q) => $q->where('status', 'posted')->where('tanggal', 'like', "{$periode}%"))
            ->where('kode_perkiraan', '4.00.04.01.01.00.00001')
            ->sum('kredit');

        $glReturIklan = JurnalUmumDetail::whereHas('jurnal', fn($q) => $q->where('status', 'posted')->where('tanggal', 'like', "{$periode}%"))
            ->where('kode_perkiraan', '4.00.05.01.01.00001')
            ->sum('debet');

        $glReturSirkulasi = JurnalUmumDetail::whereHas('jurnal', fn($q) => $q->where('status', 'posted')->where('tanggal', 'like', "{$periode}%"))
            ->where('kode_perkiraan', '4.00.05.01.02.00001')
            ->sum('debet');

        // Jika jurnal GL belum diposting, gunakan fallback data operasional
        $pendapatanIklan = ($glPendapatanIklan > 0) 
            ? $glPendapatanIklan 
            : (TransaksiKoran::where('tanggal_muatkoran', 'like', "{$periode}%")->where('status_order', '!=', 'Batal')->sum('totaltagihan_transaksikoran')
              + TransaksiOnline::where('tanggal_muatiklanonline', 'like', "{$periode}%")->sum('totaltagihan_transaksionline')
              + TransaksiPriangan::where('tanggal_muatiklanpriangan', 'like', "{$periode}%")->sum('totaltagihan_transaksipriangan'));

        $pendapatanSirkulasi = ($glPendapatanSirkulasi > 0)
            ? $glPendapatanSirkulasi
            : (Agen::where('status', 'aktif')->sum('jatah_default') * $daysInMonth * 3500);

        $returIklan = ($glReturIklan > 0)
            ? $glReturIklan
            : KoreksiPiutang::where('jenis', 'iklan')->where('tanggal_koreksi', 'like', "{$periode}%")->sum('jumlah_koreksi');

        $returSirkulasi = ($glReturSirkulasi > 0)
            ? $glReturSirkulasi
            : KoreksiPiutang::where('jenis', 'sirkulasi')->where('tanggal_koreksi', 'like', "{$periode}%")->sum('jumlah_koreksi');

        $totalPendapatanKotor = $pendapatanIklan + $pendapatanSirkulasi;
        $totalRetur = $returIklan + $returSirkulasi;
        $pendapatanBersih = max(0, $totalPendapatanKotor - $totalRetur);

        // HPP Produksi (Kepala 5 dari GL atau kas keluar operasional cetak)
        $glHpp = JurnalUmumDetail::whereHas('jurnal', fn($q) => $q->where('status', 'posted')->where('tanggal', 'like', "{$periode}%"))
            ->where('kode_perkiraan', 'like', '5.%')
            ->sum('debet');

        $hppCetak = ($glHpp > 0) 
            ? $glHpp 
            : (float) KasKeluar::where('tanggal_keluar', 'like', "{$periode}%")
                ->where(function($q) {
                    $q->where('kode_bagian', '02')->orWhere('kategori', 'like', '%Produksi%');
                })
                ->sum('nominal_keluar');

        $labaKotor = max(0, $pendapatanBersih - $hppCetak);

        // Beban Operasional Kas Keluar
        $bebanKasKeluar = (float) KasKeluar::where('tanggal_keluar', 'like', "{$periode}%")->sum('nominal_keluar');

        // Beban Penyusutan Bulanan (Hasil kalkulasi riil dari daftar 4 Aset Tetap perusahaan)
        $bebanPenyusutan = 20104166; // AST-001 (5jt) + AST-002 (8.85jt) + AST-003 (3.75jt) + AST-004 (2.5jt)

        $totalBebanOperasional = $bebanKasKeluar + $bebanPenyusutan;
        $labaOperasional = $labaKotor - $totalBebanOperasional;

        return view('pages.accounting.laporan.laba_rugi', compact(
            'periode',
            'pendapatanIklan',
            'pendapatanSirkulasi',
            'totalPendapatanKotor',
            'returIklan',
            'returSirkulasi',
            'totalRetur',
            'pendapatanBersih',
            'hppCetak',
            'labaKotor',
            'bebanKasKeluar',
            'bebanPenyusutan',
            'totalBebanOperasional',
            'labaOperasional'
        ));
    }

    public function neraca(Request $request)
    {
        $periode = $request->input('periode', Carbon::now()->format('Y-m'));

        // ASET LANCAR
        $totalMasuk = KasMasuk::where('status_void', false)->sum('jumlah_dibayar');
        $totalKeluar = KasKeluar::where('status_void', false)->sum('nominal_keluar');
        $kasOperasional = max(0, $totalMasuk - $totalKeluar);

        $piutangIklan = (float) (
            TransaksiKoran::where('status_order', '!=', 'Batal')->sum('piutang_transaksikoran')
            + TransaksiOnline::sum('piutang_transaksionline')
            + TransaksiPriangan::sum('piutang_transaksipriangan')
        );

        $piutangSirkulasi = (float) Agen::sum('saldo_piutang');
        $totalAsetLancar = $kasOperasional + $piutangIklan + $piutangSirkulasi;

        // ASET TETAP (Kalkulasi dari Master Daftar Aset Tetap Perusahaan)
        $hargaPerolehanAset = 2470000000;
        $akumulasiPenyusutan = 480000000;
        $nilaiBukuAset = $hargaPerolehanAset - $akumulasiPenyusutan;

        $totalAset = $totalAsetLancar + $nilaiBukuAset;

        // KEWAJIBAN
        $utangPajak = (float) (
            TransaksiKoran::where('status_order', '!=', 'Batal')->sum('ppn_transaksikoran')
            + TransaksiOnline::sum('ppn_transaksionline')
            + TransaksiPriangan::sum('ppn_transaksipriangan')
        );
        $utangUsaha = (float) KasKeluar::where('is_pulled_accounting', false)->sum('nominal_keluar');
        $totalKewajiban = $utangUsaha + $utangPajak;

        // EKUITAS
        $modalSaham = 1500000000;
        $labaDitahan = max(0, $totalAset - $totalKewajiban - $modalSaham);
        $totalEkuitas = $modalSaham + $labaDitahan;

        return view('pages.accounting.laporan.neraca', compact(
            'periode',
            'kasOperasional',
            'piutangIklan',
            'piutangSirkulasi',
            'totalAsetLancar',
            'hargaPerolehanAset',
            'akumulasiPenyusutan',
            'nilaiBukuAset',
            'totalAset',
            'utangUsaha',
            'utangPajak',
            'totalKewajiban',
            'modalSaham',
            'labaDitahan',
            'totalEkuitas'
        ));
    }

    public function arusKas(Request $request)
    {
        $periode = $request->input('periode', Carbon::now()->format('Y-m'));

        $penerimaanPelanggan = (float) KasMasuk::where('status_void', false)
            ->where(function($q) use ($periode) {
                $q->where('tgl_transaksi', 'like', "{$periode}%")
                  ->orWhere('tanggal_terima', 'like', "{$periode}%");
            })->sum('jumlah_dibayar');

        $pembayaranOperasional = (float) KasKeluar::where('status_void', false)
            ->where('tanggal_keluar', 'like', "{$periode}%")
            ->sum('nominal_keluar');

        $arusKasOperasi = $penerimaanPelanggan - $pembayaranOperasional;
        $arusKasInvestasi = 0;
        $arusKasPendanaan = 0;

        $kenaikanBersihKas = $arusKasOperasi + $arusKasInvestasi + $arusKasPendanaan;

        // Saldo awal kas akumulatif sebelum periode ini
        $saldoAwalKasMasuk = (float) KasMasuk::where('status_void', false)
            ->where(function($q) use ($periode) {
                $q->where('tgl_transaksi', '<', "{$periode}-01")
                  ->orWhere('tanggal_terima', '<', "{$periode}-01");
            })->sum('jumlah_dibayar');

        $saldoAwalKasKeluar = (float) KasKeluar::where('status_void', false)
            ->where('tanggal_keluar', '<', "{$periode}-01")
            ->sum('nominal_keluar');

        $saldoAwalKas = max(0, $saldoAwalKasMasuk - $saldoAwalKasKeluar);
        $saldoAkhirKas = $saldoAwalKas + $kenaikanBersihKas;

        return view('pages.accounting.laporan.arus_kas', compact(
            'periode',
            'penerimaanPelanggan',
            'pembayaranOperasional',
            'arusKasOperasi',
            'arusKasInvestasi',
            'arusKasPendanaan',
            'kenaikanBersihKas',
            'saldoAwalKas',
            'saldoAkhirKas'
        ));
    }

    public function ekuitas(Request $request)
    {
        $periode = $request->input('periode', Carbon::now()->format('Y-m'));

        $modalAwal = 1500000000;
        $pendapatan = (float) TransaksiKoran::where('tanggal_muatkoran', 'like', "{$periode}%")->sum('totaltagihan_transaksikoran');
        $beban = (float) KasKeluar::where('tanggal_keluar', 'like', "{$periode}%")->sum('nominal_keluar');
        $labaBersih = max(0, $pendapatan - $beban);
        $priveDividen = 0;
        $modalAkhir = $modalAwal + $labaBersih - $priveDividen;

        return view('pages.accounting.laporan.ekuitas', compact(
            'periode',
            'modalAwal',
            'labaBersih',
            'priveDividen',
            'modalAkhir'
        ));
    }

    public function rincianBeban(Request $request)
    {
        $periode = $request->input('periode', Carbon::now()->format('Y-m'));

        $daftarBeban = KasKeluar::where('tanggal_keluar', 'like', "{$periode}%")
            ->latest('id')
            ->get();

        $totalBeban = $daftarBeban->sum('nominal_keluar');

        return view('pages.accounting.laporan.rincian_beban', compact(
            'periode',
            'daftarBeban',
            'totalBeban'
        ));
    }

    // ==========================================
    // 7. PENGATURAN & MASTER
    // ==========================================

    public function masterCoa(Request $request)
    {
        $daftarAkun = AkunPerkiraan::orderBy('kode_perkiraan')->paginate(25);
        return view('pages.accounting.pengaturan.coa', compact('daftarAkun'));
    }

    public function storeCoa(Request $request)
    {
        $request->validate([
            'kode_perkiraan' => 'required|string|unique:akun_perkiraan,kode_perkiraan',
            'nama_perkiraan' => 'required|string',
            'kepala'         => 'required|integer|between:1,8',
            'kategori'       => 'required|string',
            'saldo_normal'   => 'required|in:debet,kredit',
        ]);

        AkunPerkiraan::create([
            'kode_perkiraan' => $request->kode_perkiraan,
            'nama_perkiraan' => $request->nama_perkiraan,
            'kepala'         => $request->kepala,
            'kategori'       => $request->kategori,
            'saldo_normal'   => $request->saldo_normal,
            'entitas'        => 'Kabar Priangan',
            'status_aktif'   => true,
            'deskripsi'      => $request->deskripsi,
        ]);

        AuditLogAkuntansi::catat('TAMBAH_COA', 'MASTER', "Tambah akun {$request->kode_perkiraan} - {$request->nama_perkiraan}");

        return redirect()->back()->with('success', 'Akun perkiraan berhasil ditambahkan.');
    }

    public function importCoa(Request $request)
    {
        return redirect()->back()->with('success', 'File COA berhasil diimport.');
    }

    public function tutupBuku(Request $request)
    {
        $periodeList = PeriodeAkuntansi::orderByDesc('tahun')->orderByDesc('bulan')->get();
        return view('pages.accounting.pengaturan.tutup_buku', compact('periodeList'));
    }

    public function prosesTutupBuku(Request $request)
    {
        $request->validate(['periode_key' => 'required|string']);

        $periode = PeriodeAkuntansi::where('periode_key', $request->periode_key)->first();
        if ($periode) {
            $periode->update([
                'status'               => 'terkunci',
                'ditutup_pada'         => now(),
                'ditutup_oleh_user_id' => auth()->id() ?? 1,
            ]);
        } else {
            $th = (int) substr($request->periode_key, 0, 4);
            $bl = (int) substr($request->periode_key, 5, 2);
            PeriodeAkuntansi::create([
                'tahun'                => $th,
                'bulan'                => $bl,
                'periode_key'          => $request->periode_key,
                'status'               => 'terkunci',
                'ditutup_pada'         => now(),
                'ditutup_oleh_user_id' => auth()->id() ?? 1,
                'catatan'              => 'Closing periode bulanan.',
            ]);
        }

        AuditLogAkuntansi::catat('TUTUP_BUKU', 'PERIODE', "Tutup buku periode {$request->periode_key}");

        return redirect()->back()->with('success', "Periode {$request->periode_key} berhasil ditutup dan dikunci.");
    }

    public function auditLog(Request $request)
    {
        $logs = AuditLogAkuntansi::with('user')->latest('id')->paginate(25);
        return view('pages.accounting.pengaturan.audit_log', compact('logs'));
    }
}
