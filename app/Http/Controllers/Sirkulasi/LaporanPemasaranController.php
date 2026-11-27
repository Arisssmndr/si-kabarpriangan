<?php

namespace App\Http\Controllers\Sirkulasi;

use App\Http\Controllers\Controller;
use App\Models\KasMasuk;
use App\Models\SirkulasiFakturBulanan;
use App\Models\SirkulasiRetur;
use App\Services\SirkulasiDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class LaporanPemasaranController extends Controller
{
    public function __construct(protected SirkulasiDataService $sirkulasi)
    {
    }

    /**
     * Laporan Pemasaran (8 Format Resmi SIRK.EXE dengan Filter Periode Fleksibel & Database Real-time).
     */
    public function index(Request $request)
    {
        $data = $this->resolveReportData($request);

        return view('pages.sirkulasi.cetak.pemasaran', $data);
    }

    /**
     * Halaman Cetak Khusus Laporan Pemasaran Sirkulasi (Sesuai Hasil Filter Aktif).
     */
    public function print(Request $request)
    {
        $data = $this->resolveReportData($request);

        return view('pages.sirkulasi.cetak.pemasaran_print', $data);
    }

    /**
     * Resolver Periode Fleksibel (Harian, Mingguan, Bulanan, Tahunan, Rentang Tanggal).
     */
    protected function resolvePeriod(Request $request, string $tab): array
    {
        $now = Carbon::now();
        $defaultMode = $tab === 'oplah-tahun' ? 'tahunan' : 'bulanan';
        $rawMode = (string) $request->input('mode_periode', '');
        $rawBulanInput = (string) $request->input('bulan', '');
        $rawTanggal = (string) $request->input('tanggal', '');

        if ($rawMode !== '' && in_array($rawMode, ['harian', 'mingguan', 'bulanan', 'tahunan', 'custom'], true)) {
            $modePeriode = $rawMode;
        } elseif ($rawTanggal !== '' && !$request->filled('bulan') && !$request->filled('tahun')) {
            $modePeriode = 'harian';
        } elseif ($rawBulanInput !== '' && preg_match('/^\d{4}-\d{2}$/', $rawBulanInput)) {
            $modePeriode = 'bulanan';
        } else {
            $modePeriode = $defaultMode;
        }

        $bulan = (int) $now->month;
        $tahun = (int) $request->input('tahun', $now->year);
        if ($rawBulanInput !== '') {
            if (preg_match('/^(\d{4})-(\d{2})$/', $rawBulanInput, $m)) {
                $tahun = (int) $m[1];
                $bulan = (int) $m[2];
            } elseif (is_numeric($rawBulanInput)) {
                $bulan = (int) $rawBulanInput;
            }
        }
        $bulan = max(1, min(12, $bulan));
        $tahun = max(2020, min(2035, $tahun));

        $tanggal = $rawTanggal !== '' ? $rawTanggal : $now->toDateString();
        $dari = (string) $request->input('dari', '');
        $sampai = (string) $request->input('sampai', '');

        if ($modePeriode === 'harian') {
            $chosenDate = $rawTanggal !== '' ? $rawTanggal : ($dari !== '' ? $dari : $now->toDateString());
            $cTgl = Carbon::parse($chosenDate);
            $startDate = $cTgl->copy()->startOfDay();
            $endDate = $cTgl->copy()->endOfDay();
            $tanggal = $cTgl->toDateString();
            $dari = $tanggal;
            $sampai = $tanggal;
            $bulan = (int) $cTgl->month;
            $tahun = (int) $cTgl->year;
            $periodeLabel = 'Harian: ' . $cTgl->translatedFormat('l, d F Y');
            $periodeCetakHeader = strtoupper($cTgl->locale('id')->isoFormat('dddd')) . ' / ' . $cTgl->format('d-m-Y');
        } elseif ($modePeriode === 'mingguan') {
            $anchorDate = $rawTanggal !== '' ? Carbon::parse($rawTanggal) : ($sampai !== '' ? Carbon::parse($sampai) : $now->copy());
            $endDate = $anchorDate->copy()->endOfDay();
            $startDate = $anchorDate->copy()->subDays(6)->startOfDay();
            $tanggal = $anchorDate->toDateString();
            $dari = $startDate->toDateString();
            $sampai = $endDate->toDateString();
            $bulan = (int) $anchorDate->month;
            $tahun = (int) $anchorDate->year;
            $periodeLabel = 'Mingguan: ' . $startDate->translatedFormat('d M Y') . ' – ' . $endDate->translatedFormat('d M Y');
            $periodeCetakHeader = 'MINGGUAN: ' . $startDate->format('d-m-Y') . ' S/D ' . $endDate->format('d-m-Y');
        } elseif ($modePeriode === 'tahunan') {
            $startDate = Carbon::create($tahun, 1, 1)->startOfYear()->startOfDay();
            $endDate = $startDate->copy()->endOfYear()->endOfDay();
            $dari = $startDate->toDateString();
            $sampai = $endDate->toDateString();
            $periodeLabel = 'Tahunan: Tahun ' . $tahun;
            $periodeCetakHeader = 'TAHUN ' . $tahun . ' (JANUARI - DESEMBER ' . $tahun . ')';
        } elseif ($modePeriode === 'custom') {
            $dStr = $dari !== '' ? $dari : $now->copy()->startOfMonth()->toDateString();
            $sStr = $sampai !== '' ? $sampai : $now->toDateString();
            $startDate = Carbon::parse($dStr)->startOfDay();
            $endDate = Carbon::parse($sStr)->endOfDay();
            if ($startDate->gt($endDate)) {
                [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
            }
            $dari = $startDate->toDateString();
            $sampai = $endDate->toDateString();
            $bulan = (int) $endDate->month;
            $tahun = (int) $endDate->year;
            if ($startDate->isSameDay($endDate)) {
                $periodeLabel = 'Tanggal: ' . $startDate->translatedFormat('l, d F Y');
                $periodeCetakHeader = $startDate->format('d-m-Y');
            } else {
                $periodeLabel = 'Rentang: ' . $startDate->translatedFormat('d M Y') . ' s/d ' . $endDate->translatedFormat('d M Y');
                $periodeCetakHeader = $startDate->format('d-m-Y') . ' S/D ' . $endDate->format('d-m-Y');
            }
        } else {
            $modePeriode = 'bulanan';
            $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth()->startOfDay();
            $endDate = $startDate->copy()->endOfMonth()->endOfDay();
            $dari = $startDate->toDateString();
            $sampai = $endDate->toDateString();
            $periodeLabel = 'Bulanan: ' . $startDate->translatedFormat('F Y');
            $periodeCetakHeader = strtoupper($startDate->translatedFormat('F Y'));
        }

        $bulanYm = sprintf('%04d-%02d', $tahun, $bulan);
        $daysInRange = max(1, (int) $startDate->copy()->startOfDay()->diffInDays($endDate->copy()->startOfDay()) + 1);

        return compact(
            'modePeriode',
            'tanggal',
            'bulan',
            'tahun',
            'bulanYm',
            'dari',
            'sampai',
            'startDate',
            'endDate',
            'daysInRange',
            'periodeLabel',
            'periodeCetakHeader'
        );
    }

    /**
     * Query & agregasi terpusat Laporan Pemasaran Sirkulasi dari database.
     */
    protected function resolveReportData(Request $request): array
    {
        $tab = (string) $request->input('tab', 'agen-besar');
        if ($tab === 'bulan-wilayah') {
            $tab = 'rekap-bln-wilayah';
        }
        $allowedTabs = [
            'agen-besar',
            'sub-agen',
            'rekap-terbit-bulan',
            'rekap-wilayah',
            'rekap-jurusan',
            'penerimaan-koran',
            'oplah-tahun',
            'rekap-bln-wilayah',
        ];
        if (!in_array($tab, $allowedTabs, true)) {
            $tab = 'agen-besar';
        }

        $period = $this->resolvePeriod($request, $tab);
        $cfg = $this->sirkulasi->getConfig();
        $hargaBesar = (int) ($cfg['harga_agen_besar'] ?? 2800);
        $hargaEceran = (int) ($cfg['harga_eceran'] ?? 3000);
        $pctL = (float) ($cfg['pct_langganan'] ?? 0.60);
        $pctK = (float) ($cfg['pct_konsinyasi'] ?? 0.35);

        $wilayahFilter = (string) $request->input('wilayah', 'all');
        $jurusanFilter = (string) $request->input('jurusan', 'all');
        $statusBayarFilter = (string) $request->input('status_bayar', 'all');
        $searchQ = trim((string) $request->input('q', ''));

        $wilayahMaster = $this->sirkulasi->getDaftarWilayah();
        $jurusanMaster = $this->sirkulasi->getDaftarJurusan();
        $agenAll = $this->sirkulasi->getDaftarAgen();
        $agenAktifAll = $agenAll->where('status', 'aktif')->values();

        $filterAgenCollection = function (Collection $collection) use ($wilayahFilter, $jurusanFilter, $searchQ): Collection {
            if ($wilayahFilter !== '' && $wilayahFilter !== 'all') {
                $collection = $collection->where('wilayah_kode', $wilayahFilter);
            }
            if ($jurusanFilter !== '' && $jurusanFilter !== 'all') {
                $collection = $collection->where('jurusan_kode', $jurusanFilter);
            }
            if ($searchQ !== '') {
                $needle = mb_strtolower($searchQ);
                $collection = $collection->filter(function ($ag) use ($needle) {
                    return str_contains(mb_strtolower((string) ($ag->kode_agen ?? '')), $needle)
                        || str_contains(mb_strtolower((string) ($ag->nama_agen ?? '')), $needle)
                        || str_contains(mb_strtolower((string) ($ag->induk_agen ?? '')), $needle)
                        || str_contains(mb_strtolower((string) ($ag->wilayah_nama ?? '')), $needle)
                        || str_contains(mb_strtolower((string) ($ag->jurusan_nama ?? '')), $needle);
                });
            }
            return $collection->values();
        };

        $agenFiltered = $filterAgenCollection($agenAktifAll);
        $agenAllFiltered = $filterAgenCollection($agenAll);
        $filteredAgenIds = $agenAllFiltered->pluck('id')->all();

        $dates = [];
        $cursor = $period['startDate']->copy()->startOfDay();
        $endDay = $period['endDate']->copy()->startOfDay();
        $isDateTruncated = false;
        $totalDaysSelected = (int) $cursor->diffInDays($endDay) + 1;
        if ($cursor->diffInDays($endDay) > 370) {
            $cursor = $endDay->copy()->subDays(365);
            $isDateTruncated = true;
        }
        while ($cursor->lte($endDay)) {
            $dates[] = $cursor->toDateString();
            $cursor->addDay();
        }
        $cacheKeys = array_map(fn(string $d) => 'jatah_harian_' . $d, $dates);
        $dailyCacheMaps = !empty($cacheKeys) ? Cache::many($cacheKeys) : [];

        $returByAgen = [];
        $returByDate = [];
        if (Schema::hasTable('sirkulasi_returs')) {
            $returQuery = SirkulasiRetur::query()
                ->whereBetween('tanggal_retur', [$period['dari'], $period['sampai']])
                ->where('status', '!=', 'ditolak');
            if ($wilayahFilter !== 'all' || $jurusanFilter !== 'all' || $searchQ !== '') {
                $returQuery->whereIn('agen_id', !empty($filteredAgenIds) ? $filteredAgenIds : [-1]);
            }
            foreach ($returQuery->get() as $ret) {
                $qtyRet = (int) ($ret->jumlah_retur_disetujui > 0 ? $ret->jumlah_retur_disetujui : $ret->jumlah_retur_fisik);
                $agId = (int) $ret->agen_id;
                $tglKey = $ret->tanggal_retur ? $ret->tanggal_retur->toDateString() : '';

                $returByAgen[$agId] = ($returByAgen[$agId] ?? 0) + $qtyRet;
                if ($tglKey !== '') {
                    $returByDate[$tglKey] = ($returByDate[$tglKey] ?? 0) + $qtyRet;
                }
            }
        }

        $calcAgentDay = static function ($ag, array $cachedMap) use ($pctL, $pctK): array {
            $custom = $cachedMap[$ag->id] ?? null;
            if (is_array($custom)) {
                $l = (int) ($custom['langganan'] ?? 0);
                $k = (int) ($custom['konsinyasi'] ?? 0);
                $g = (int) ($custom['gratis'] ?? 0);
                $p = (int) ($custom['pengenalan'] ?? 0);
                $i = (int) ($custom['insidentil'] ?? 0);
                $tot = $l + $k + $g + $p + $i;
            } else {
                $tot = (int) ($ag->jatah_default ?? 0);
                $l = (int) round($tot * $pctL);
                $k = (int) round($tot * $pctK);
                $g = max(0, $tot - $l - $k);
                $p = 0;
                $i = 0;
            }
            return compact('l', 'k', 'g', 'p', 'i', 'tot');
        };

        $agentStats = [];
        foreach ($agenFiltered as $ag) {
            $agentStats[$ag->id] = [
                'langganan'  => 0,
                'konsinyasi' => 0,
                'gratis'     => 0,
                'pengenalan' => 0,
                'insidentil' => 0,
                'kirim'      => 0,
                'omset_kotor'=> 0,
                'retur'      => (int) ($returByAgen[$ag->id] ?? 0),
            ];
        }

        $dailyRows = [];
        $monthAgg = [];
        foreach ($dates as $dStr) {
            $cachedMap = $dailyCacheMaps['jatah_harian_' . $dStr] ?? [];
            if (!is_array($cachedMap)) {
                $cachedMap = [];
            }
            $dayHasSaved = !empty($cachedMap);
            $dL = 0; $dK = 0; $dG = 0; $dP = 0; $dI = 0; $dTot = 0; $dOmsetKotor = 0;

            foreach ($agenFiltered as $ag) {
                $alloc = $calcAgentDay($ag, $cachedMap);
                $harga = $ag->kategori === '11' ? $hargaBesar : $hargaEceran;

                $agentStats[$ag->id]['langganan'] += $alloc['l'];
                $agentStats[$ag->id]['konsinyasi'] += $alloc['k'];
                $agentStats[$ag->id]['gratis'] += $alloc['g'];
                $agentStats[$ag->id]['pengenalan'] += $alloc['p'];
                $agentStats[$ag->id]['insidentil'] += $alloc['i'];
                $agentStats[$ag->id]['kirim'] += $alloc['tot'];
                $agentStats[$ag->id]['omset_kotor'] += $alloc['tot'] * $harga;

                $dL += $alloc['l'];
                $dK += $alloc['k'];
                $dG += $alloc['g'];
                $dP += $alloc['p'];
                $dI += $alloc['i'];
                $dTot += $alloc['tot'];
                $dOmsetKotor += $alloc['tot'] * $harga;
            }

            $dRetur = (int) ($returByDate[$dStr] ?? 0);
            $dNett = max(0, $dTot - $dRetur);
            $cDate = Carbon::parse($dStr);
            $dailyRows[] = (object)[
                'tanggal'     => $dStr,
                'tanggal_fmt' => $cDate->translatedFormat('d M Y'),
                'hari'        => $cDate->translatedFormat('l'),
                'status_data' => $dayHasSaved ? 'Tersimpan' : 'Default Agen',
                'langganan'   => $dL,
                'konsinyasi'  => $dK,
                'gratis'      => $dG,
                'pengenalan'  => $dP,
                'insidentil'  => $dI,
                'kirim'       => $dTot,
                'retur'       => $dRetur,
                'nett'        => $dNett,
                'omset_kotor' => $dOmsetKotor,
            ];

            $mKey = substr($dStr, 0, 7);
            if (!isset($monthAgg[$mKey])) {
                $monthAgg[$mKey] = ['hari' => 0, 'kirim' => 0, 'omset_kotor' => 0];
            }
            $monthAgg[$mKey]['hari'] += 1;
            $monthAgg[$mKey]['kirim'] += $dTot;
            $monthAgg[$mKey]['omset_kotor'] += $dOmsetKotor;
        }

        $enrichAgentRows = function (Collection $agents) use ($agentStats, $hargaBesar, $hargaEceran, $period): Collection {
            return $agents->map(function ($ag) use ($agentStats, $hargaBesar, $hargaEceran, $period) {
                $st = $agentStats[$ag->id] ?? [
                    'langganan' => 0, 'konsinyasi' => 0, 'gratis' => 0, 'pengenalan' => 0,
                    'insidentil' => 0, 'kirim' => 0, 'omset_kotor' => 0, 'retur' => 0,
                ];
                $harga = $ag->kategori === '11' ? $hargaBesar : $hargaEceran;
                $nett = max(0, $st['kirim'] - $st['retur']);
                $nilaiNett = $nett * $harga;
                $daysCount = max(1, (int) $period['daysInRange']);

                return (object)[
                    'id'            => $ag->id,
                    'kode_agen'     => $ag->kode_agen,
                    'nama_agen'     => $ag->nama_agen,
                    'induk_agen'    => $ag->induk_agen ?? '-',
                    'kategori'      => $ag->kategori,
                    'wilayah_kode'  => $ag->wilayah_kode ?? '01',
                    'wilayah_nama'  => $ag->wilayah_nama ?? '-',
                    'jurusan_kode'  => $ag->jurusan_kode ?? '01',
                    'jurusan_nama'  => $ag->jurusan_nama ?? '-',
                    'harga_satuan'  => $harga,
                    'langganan'     => $st['langganan'],
                    'konsinyasi'    => $st['konsinyasi'],
                    'gratis'        => $st['gratis'],
                    'pengenalan'    => $st['pengenalan'],
                    'insidentil'    => $st['insidentil'],
                    'kirim'         => $st['kirim'],
                    'rata_harian'   => (int) round($st['kirim'] / $daysCount),
                    'retur'         => $st['retur'],
                    'nett'          => $nett,
                    'nilai_tagihan' => $nilaiNett,
                ];
            })->values();
        };

        $agenBesarRows = $enrichAgentRows($agenFiltered->where('kategori', '11'));
        $subAgenRows = $enrichAgentRows($agenFiltered->where('kategori', '99'));
        $allAgentRows = $enrichAgentRows($agenFiltered);

        $periodMonths = array_values(array_unique(array_map(fn(string $d) => substr($d, 0, 7), $dates)));
        $fakturBulananRows = collect([]);
        if (Schema::hasTable('sirkulasi_faktur_bulanans')) {
            $fbQ = SirkulasiFakturBulanan::query()
                ->whereIn('periode_bulan', !empty($periodMonths) ? $periodMonths : [$period['bulanYm']]);
            if ($wilayahFilter !== 'all' || $jurusanFilter !== 'all' || $searchQ !== '') {
                $fbQ->whereIn('agen_id', !empty($filteredAgenIds) ? $filteredAgenIds : [-1]);
            }
            if ($statusBayarFilter !== '' && $statusBayarFilter !== 'all') {
                $dbStatuses = ($statusBayarFilter === 'belum_bayar' || $statusBayarFilter === 'belum_lunas')
                    ? ['belum_lunas', 'belum_bayar']
                    : [$statusBayarFilter];
                $fbQ->whereIn('status_pembayaran', $dbStatuses);
            }
            $fakturBulananRows = $fbQ->orderBy('periode_bulan')->orderBy('kode_agen')->get();
        }

        $kasMasukSirkulasi = collect([]);
        if (Schema::hasTable('kas_masuks')) {
            $kasMasukSirkulasi = KasMasuk::query()
                ->where('tipe_transaksi', 'sirkulasi')
                ->whereBetween('tgl_transaksi', [$period['dari'], $period['sampai']])
                ->where(function ($q) {
                    $q->whereNull('status_void')->orWhere('status_void', '!=', 'void');
                })
                ->orderByDesc('tgl_transaksi')
                ->get();
        }

        $agenById = $agenAll->keyBy('id');
        $kasByAgenId = $kasMasukSirkulasi->groupBy('ref_id');
        $kasByKodeAgen = $kasMasukSirkulasi->groupBy('kode_agen_ref');

        if ($fakturBulananRows->isNotEmpty()) {
            $penerimaanRows = $fakturBulananRows->map(function (SirkulasiFakturBulanan $fb) use ($agenById, $wilayahMaster) {
                $ag = $agenById->get($fb->agen_id) ?? $agenById->firstWhere('kode_agen', $fb->kode_agen);
                $wilKode = $ag->wilayah_kode ?? null;
                if (!$wilKode && !empty($fb->wilayah)) {
                    $wilMatch = $wilayahMaster->first(function ($w) use ($fb) {
                        return strcasecmp($w->nama, $fb->wilayah) === 0 || strcasecmp($w->kode, $fb->wilayah) === 0;
                    });
                    $wilKode = $wilMatch?->kode;
                }
                $wilKode = $wilKode ?: '01';
                $wilNama = $ag->wilayah_nama ?? ($fb->wilayah ?? '-');
                $jurKode = $ag->jurusan_kode ?? '-';

                $statusBayarNorm = in_array((string)$fb->status_pembayaran, ['belum_lunas', 'belum_bayar'], true)
                    ? 'belum_bayar'
                    : (string)$fb->status_pembayaran;

                return (object)[
                    'kode_agen'       => $fb->kode_agen,
                    'nama_agen'       => $fb->nama_agen,
                    'no_faktur'       => $fb->no_faktur,
                    'periode_bulan'   => $fb->periode_bulan,
                    'wilayah_kode'    => $wilKode,
                    'wilayah_nama'    => $wilNama,
                    'jurusan_kode'    => $jurKode,
                    'kirim'           => (int) $fb->total_oplah_kirim,
                    'retur'           => (int) $fb->total_retur_eks,
                    'nett'            => (int) $fb->oplah_bersih,
                    'tagihan_kotor'   => (float) $fb->total_tagihan_kotor,
                    'potongan_retur'  => (float) $fb->total_potongan_retur,
                    'tagihan_bersih'  => (float) $fb->total_tagihan_bersih,
                    'terbayar'        => (float) $fb->total_terbayar,
                    'sisa_piutang'    => (float) $fb->sisa_piutang,
                    'status_bayar'    => $statusBayarNorm,
                    'sumber_data'     => 'Faktur Resmi',
                ];
            })->values();
        } else {
            $penerimaanRows = $allAgentRows->map(function ($row) use ($kasByAgenId, $kasByKodeAgen, $agenById, $period) {
                $ag = $agenById->get($row->id);
                $kmMatches = $kasByAgenId->get($row->id, collect())->merge($kasByKodeAgen->get($row->kode_agen, collect()))->unique('id');
                $terbayar = (float) $kmMatches->sum('jumlah_dibayar');
                $tagihanKotor = (float) ($row->kirim * $row->harga_satuan);
                $potonganRetur = (float) ($row->retur * $row->harga_satuan);
                $tagihanBersih = max(0, $tagihanKotor - $potonganRetur);
                $sisa = max(0, $tagihanBersih - $terbayar);
                $statusBayar = $terbayar >= $tagihanBersih && $tagihanBersih > 0
                    ? 'lunas'
                    : ($terbayar > 0 ? 'sebagian' : 'belum_bayar');

                return (object)[
                    'kode_agen'       => $row->kode_agen,
                    'nama_agen'       => $row->nama_agen,
                    'no_faktur'       => 'EST-' . str_replace('-', '', $period['bulanYm']) . '-' . $row->kode_agen,
                    'periode_bulan'   => $period['bulanYm'],
                    'wilayah_kode'    => $row->wilayah_kode,
                    'wilayah_nama'    => $row->wilayah_nama,
                    'jurusan_kode'    => $row->jurusan_kode,
                    'kirim'           => $row->kirim,
                    'retur'           => $row->retur,
                    'nett'            => $row->nett,
                    'tagihan_kotor'   => $tagihanKotor,
                    'potongan_retur'  => $potonganRetur,
                    'tagihan_bersih'  => $tagihanBersih,
                    'terbayar'        => $terbayar,
                    'sisa_piutang'    => $sisa > 0 ? $sisa : (float) ($ag->saldo_piutang ?? 0),
                    'status_bayar'    => $statusBayar,
                    'sumber_data'     => 'Realisasi Berjalan',
                ];
            });

            if ($statusBayarFilter !== '' && $statusBayarFilter !== 'all') {
                $normalizedFilter = ($statusBayarFilter === 'belum_lunas') ? 'belum_bayar' : $statusBayarFilter;
                $penerimaanRows = $penerimaanRows->where('status_bayar', $normalizedFilter);
            }
            $penerimaanRows = $penerimaanRows->values();
        }

        $penerimaanByWilayah = $penerimaanRows->groupBy('wilayah_kode');
        $rowsByWilayah = $allAgentRows->groupBy('wilayah_kode');
        $wilayahFilteredMaster = $wilayahFilter !== '' && $wilayahFilter !== 'all'
            ? $wilayahMaster->where('kode', $wilayahFilter)->values()
            : $wilayahMaster;

        $wilayahRekapRows = $wilayahFilteredMaster->map(function ($wil) use ($rowsByWilayah, $penerimaanByWilayah, $period) {
            $agRows = $rowsByWilayah->get($wil->kode, collect());
            $penRows = $penerimaanByWilayah->get($wil->kode, collect());
            $kirim = (int) $agRows->sum('kirim');
            $retur = (int) $agRows->sum('retur');
            $nett = (int) $agRows->sum('nett');
            $nilaiOmset = (float) $agRows->sum('nilai_tagihan');
            $tagihanBersih = $penRows->isNotEmpty() ? (float) $penRows->sum('tagihan_bersih') : $nilaiOmset;
            $terbayar = (float) $penRows->sum('terbayar');
            $sisaPiutang = (float) $penRows->sum('sisa_piutang');
            $agenCount = $agRows->count();

            return (object)[
                'kode'           => $wil->kode,
                'nama'           => $wil->nama,
                'tipe'           => $wil->tipe,
                'total_agen'     => $agenCount > 0 ? $agenCount : (int) ($wil->total_agen ?? 0),
                'agen_terfilter' => $agenCount,
                'rata_harian'    => (int) round($kirim / max(1, $period['daysInRange'])),
                'kirim'          => $kirim,
                'retur'          => $retur,
                'nett'           => $nett,
                'nilai_omset'    => $nilaiOmset,
                'tagihan_bersih' => $tagihanBersih,
                'terbayar'       => $terbayar,
                'sisa_piutang'   => $sisaPiutang,
            ];
        })->values();

        $rowsByJurusan = $allAgentRows->groupBy('jurusan_kode');
        $jurusanFilteredMaster = $jurusanFilter !== '' && $jurusanFilter !== 'all'
            ? $jurusanMaster->where('kode', $jurusanFilter)->values()
            : $jurusanMaster;

        $jurusanRekapRows = $jurusanFilteredMaster->map(function ($jur) use ($rowsByJurusan, $period) {
            $agRows = $rowsByJurusan->get($jur->kode, collect());
            $kirim = (int) $agRows->sum('kirim');

            return (object)[
                'kode'          => $jur->kode,
                'nama_jurusan'  => $jur->nama_jurusan,
                'titik_tujuan'  => $jur->titik_tujuan ?? '-',
                'armada'        => $jur->armada ?? '-',
                'sopir'         => $jur->sopir ?? '-',
                'jam_berangkat' => $jur->jam_berangkat ?? '02:30 WIB',
                'total_agen'    => $agRows->count(),
                'rata_harian'   => (int) round($kirim / max(1, $period['daysInRange'])),
                'kirim'         => $kirim,
                'retur'         => (int) $agRows->sum('retur'),
                'nett'          => (int) $agRows->sum('nett'),
                'nilai_omset'   => (float) $agRows->sum('nilai_tagihan'),
            ];
        })->values();

        $tahunAudit = (int) $period['tahun'];
        $namaBulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $retursYearlyByMonth = [];
        if (Schema::hasTable('sirkulasi_returs')) {
            $yRetQ = SirkulasiRetur::query()
                ->where('periode_bulan', 'like', $tahunAudit . '-%')
                ->where('status', '!=', 'ditolak');
            if ($wilayahFilter !== 'all' || $jurusanFilter !== 'all' || $searchQ !== '') {
                $yRetQ->whereIn('agen_id', !empty($filteredAgenIds) ? $filteredAgenIds : [-1]);
            }
            foreach ($yRetQ->get() as $yr) {
                $mk = $yr->periode_bulan ?: ($yr->tanggal_retur ? $yr->tanggal_retur->format('Y-m') : '');
                if ($mk !== '') {
                    $qty = (int) ($yr->jumlah_retur_disetujui > 0 ? $yr->jumlah_retur_disetujui : $yr->jumlah_retur_fisik);
                    $retursYearlyByMonth[$mk] = ($retursYearlyByMonth[$mk] ?? 0) + $qty;
                }
            }
        }

        $fakturYearlyByMonth = collect([]);
        if (Schema::hasTable('sirkulasi_faktur_bulanans')) {
            $yFbQ = SirkulasiFakturBulanan::query()->where('periode_bulan', 'like', $tahunAudit . '-%');
            if ($wilayahFilter !== 'all' || $jurusanFilter !== 'all' || $searchQ !== '') {
                $yFbQ->whereIn('agen_id', !empty($filteredAgenIds) ? $filteredAgenIds : [-1]);
            }
            $fakturYearlyByMonth = $yFbQ->get()->groupBy('periode_bulan');
        }

        $kasYearlyByMonth = collect([]);
        if (Schema::hasTable('kas_masuks')) {
            $kasYearlyByMonth = KasMasuk::query()
                ->where('tipe_transaksi', 'sirkulasi')
                ->whereYear('tgl_transaksi', $tahunAudit)
                ->where(function ($q) {
                    $q->whereNull('status_void')->orWhere('status_void', '!=', 'void');
                })
                ->get()
                ->groupBy(fn($km) => $km->tgl_transaksi ? Carbon::parse($km->tgl_transaksi)->format('Y-m') : '');
        }

        $baselineKirimHarian = (int) $agenFiltered->sum('jatah_default');
        $baselineOmsetHarian = (float) $agenFiltered->sum(fn($ag) => ((int) $ag->jatah_default) * ($ag->kategori === '11' ? $hargaBesar : $hargaEceran));

        $rowsTahunan = [];
        foreach ($namaBulanList as $mNum => $mLabel) {
            $mKey = sprintf('%04d-%02d', $tahunAudit, $mNum);
            $daysInMonth = Carbon::create($tahunAudit, $mNum, 1)->daysInMonth;
            $fbGroup = $fakturYearlyByMonth->get($mKey, collect());

            if (isset($monthAgg[$mKey])) {
                $kirimBulan = (int) $monthAgg[$mKey]['kirim'];
                $hariHitung = (int) $monthAgg[$mKey]['hari'];
                $omsetKotorBulan = (float) $monthAgg[$mKey]['omset_kotor'];
            } else {
                if ($fbGroup->isNotEmpty()) {
                    $kirimBulan = (int) $fbGroup->sum('total_oplah_kirim');
                    $omsetKotorBulan = (float) $fbGroup->sum('total_tagihan_kotor');
                } else {
                    $kirimBulan = $baselineKirimHarian * $daysInMonth;
                    $omsetKotorBulan = $baselineOmsetHarian * $daysInMonth;
                }
                $hariHitung = $daysInMonth;
            }

            $returBulan = isset($retursYearlyByMonth[$mKey])
                ? (int) $retursYearlyByMonth[$mKey]
                : (int) $fbGroup->sum('total_retur_eks');
            $nettBulan = max(0, $kirimBulan - $returBulan);
            $rasioRetur = $kirimBulan > 0 ? round(($returBulan / $kirimBulan) * 100, 1) : 0.0;
            $tagihanBulan = $fbGroup->isNotEmpty()
                ? (float) $fbGroup->sum('total_tagihan_bersih')
                : ($kirimBulan > 0 ? round($omsetKotorBulan * ($nettBulan / $kirimBulan)) : 0.0);
            $terbayarBulan = $fbGroup->isNotEmpty()
                ? (float) $fbGroup->sum('total_terbayar')
                : (float) $kasYearlyByMonth->get($mKey, collect())->sum('jumlah_dibayar');

            $rowsTahunan[] = (object)[
                'bulan_key'      => $mKey,
                'label'          => "{$mLabel} {$tahunAudit}",
                'jumlah_hari'    => $hariHitung,
                'kirim'          => $kirimBulan,
                'rata_harian'    => (int) round($kirimBulan / max(1, $hariHitung)),
                'retur'          => $returBulan,
                'rasio_retur'    => $rasioRetur,
                'nett'           => $nettBulan,
                'nilai_tagihan'  => $tagihanBulan,
                'terbayar'       => $terbayarBulan,
            ];
        }

        $sumberDataPenerimaan = $fakturBulananRows->isNotEmpty() ? 'Faktur Resmi' : 'Realisasi Berjalan';

        return array_merge($period, [
            'tab'                  => $tab,
            'cfg'                  => $cfg,
            'hargaBesar'           => $hargaBesar,
            'hargaEceran'          => $hargaEceran,
            'wilayahList'          => $wilayahMaster,
            'jurusanList'          => $jurusanMaster,
            'agenList'             => $agenFiltered,
            'wilayahFilter'        => $wilayahFilter,
            'jurusanFilter'        => $jurusanFilter,
            'statusBayarFilter'    => $statusBayarFilter,
            'searchQ'              => $searchQ,
            'agenBesarRows'        => $agenBesarRows,
            'subAgenRows'          => $subAgenRows,
            'dailyRows'            => $dailyRows,
            'wilayahRekapRows'     => $wilayahRekapRows,
            'jurusanRekapRows'     => $jurusanRekapRows,
            'penerimaanRows'       => $penerimaanRows,
            'rowsTahunan'          => $rowsTahunan,
            'tahunAudit'           => $tahunAudit,
            'sumberDataPenerimaan' => $sumberDataPenerimaan,
            'isDateTruncated'      => $isDateTruncated,
            'totalDaysSelected'    => $totalDaysSelected,
        ]);
    }
}
