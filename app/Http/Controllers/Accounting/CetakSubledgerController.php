<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Agen;
use App\Models\KoreksiPiutang;
use App\Models\PembayaranIklan;
use App\Models\StokOpnameFaktur;
use App\Models\TransaksiKoran;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CetakSubledgerController extends Controller
{
    public function __construct(protected AccountingService $accountingService)
    {
    }

    public function cetakIklan(Request $request)
    {
        $tab = $request->input('tab', 'penjualan');
        $periodeType = $request->input('periode_type', 'bulanan');

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
                $isUm = ($item->piutang_transaksikoran == 0 || strtolower((string) $item->jenis_pembayaran) === 'tunai' || str_contains(strtolower((string) $item->jenis_pembayaran), 'muka'));
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
            $data = TransaksiKoran::with(['pelanggan', 'pembayaranList'])
                ->whereBetween('tanggal_transaksikoran', [$tglMulai, $tglSelesai])
                ->orWhere('piutang_transaksikoran', '>', 0)
                ->latest('id')
                ->get();

            foreach ($data as $item) {
                $isUm = ($item->piutang_transaksikoran == 0 || strtolower((string) $item->jenis_pembayaran) === 'tunai' || str_contains(strtolower((string) $item->jenis_pembayaran), 'muka'));
                $item->kode_perkiraan_display = $isUm ? '2.00.01.01.01.00001' : ('1.00.04.01.01.' . str_pad($item->id, 5, '0', STR_PAD_LEFT));
                $item->tipe_akun_display = $isUm ? 'Uang Muka' : 'Piutang';
            }
            $totalNominal = $data->sum('piutang_transaksikoran');
        } elseif ($tab === 'piutang_ae') {
            $transaksiAe = TransaksiKoran::where('piutang_transaksikoran', '>', 0)->get();
            $grouped = $transaksiAe->groupBy(fn ($item) => $item->sales_iklankoran ?: 'Tanpa AE / Kantor Pusat');

            $data = collect();
            foreach ($grouped as $aeNama => $items) {
                $data->push((object) [
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
            $data = TransaksiKoran::where('insentif_transaksikoran', '>', 0)
                ->whereBetween('tanggal_transaksikoran', [$tglMulai, $tglSelesai])
                ->latest('id')
                ->get();
            $totalNominal = $data->sum('insentif_transaksikoran');
        } elseif ($tab === 'space_iklan') {
            $data = TransaksiKoran::whereBetween('tanggal_transaksikoran', [$tglMulai, $tglSelesai])
                ->latest('id')
                ->get();

            $totalNominal = $data->sum('totaltagihan_transaksikoran');
            $totalMmk = $data->sum(fn ($item) => ($item->lebar_kolom ?? 1) * ($item->tinggi_mm ?? 1) * ($item->total_muatkoran ?? 1));
            $extraSummary['total_mmk'] = $totalMmk;
            $extraSummary['total_muat'] = $data->sum('total_muatkoran');
        } elseif ($tab === 'stock_opname') {
            $data = StokOpnameFaktur::latest('tanggal_opname')->get();
            if ($data->isEmpty()) {
                $extraSummary['total_terbit'] = TransaksiKoran::count();
                $extraSummary['total_lunas'] = TransaksiKoran::where('piutang_transaksikoran', 0)->count();
                $extraSummary['total_batal'] = TransaksiKoran::whereNotNull('alasan_batal')->count();
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

    public function cetakSubledgerIklan(Request $request)
    {
        $response = $this->cetakIklan($request);
        return view('pages.accounting.cetak.cetak_iklan', $response->getData());
    }

    public function cetakSirkulasi(Request $request)
    {
        $tab = $request->input('tab', 'penjualan');
        $tglMulai = $request->input('tgl_mulai', Carbon::today()->startOfMonth()->toDateString());
        $tglSelesai = $request->input('tgl_selesai', Carbon::today()->toDateString());

        $data = collect();
        $totalNominal = 0;
        $totalEksemplar = 0;
        $perAgenCalc = [];
        $pricing = $this->accountingService->getSirkulasiPricing();
        $hargaKoran = $pricing['harga_eceran'];
        $estimasiHari = max(1, Carbon::parse($tglMulai)->diffInDays(Carbon::parse($tglSelesai)) + 1);

        if ($tab === 'penjualan' || $tab === 'peredaran') {
            $calc = $this->accountingService->hitungOmsetSirkulasiRange($tglMulai, $tglSelesai);
            $data = $calc['daftar_agen'];
            $perAgenCalc = $calc['per_agen'];
            $totalEksemplar = $calc['total_eks'];
            $totalNominal = $calc['total_nominal'];
            $hargaKoran = $calc['harga_rata'];
            $estimasiHari = $calc['hari'];
        } elseif ($tab === 'pembayaran') {
            $data = $this->accountingService->kasMasukAktif()
                ->with('agen')
                ->where(function ($q) {
                    $q->where('kode_bagian', '02')
                      ->orWhere('tipe_transaksi', 'sirkulasi');
                })
                ->where(function ($q) use ($tglMulai, $tglSelesai) {
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
            'totalEksemplar',
            'perAgenCalc',
            'hargaKoran',
            'estimasiHari'
        ));
    }

    public function cetakSubledgerSirkulasi(Request $request)
    {
        $response = $this->cetakSirkulasi($request);
        return view('pages.accounting.cetak.cetak_sirkulasi', $response->getData());
    }
}
