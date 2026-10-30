<?php

namespace App\Http\Controllers;

use App\Models\DailyCutoff;
use App\Models\KopKota;
use App\Models\OrderJadwalMuat;
use App\Models\SistemConfig;
use App\Models\TransaksiKoran;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RekapPracetakController extends Controller
{
    /**
     * Tampilkan antarmuka interaktif Rekap Dummy Pracetak harian
     */
    public function index(Request $request)
    {
        $data = $this->resolvePracetakData($request);

        return view('pages.iklan.rekap_pracetak.index', $data);
    }

    /**
     * Cetak dokumen lembar dummy pracetak resmi HU Kabar Priangan
     */
    public function cetak(Request $request)
    {
        $data = $this->resolvePracetakData($request);

        return view('pages.iklan.rekap_pracetak.cetak', $data);
    }

    /**
     * Pengolahan data pemuatan iklan per halaman edisi cetak
     */
    private function resolvePracetakData(Request $request): array
    {
        // Tanggal target: default terbit esok hari, atau yang dipilih operator
        $tanggalMuat = $request->input('tanggal_muat', now()->addDay()->toDateString());
        $halamanFilter = $request->input('kop_kota_id', 'all');

        $query = OrderJadwalMuat::with([
            'transaksi.iklankoran',
            'transaksi.pelanggan',
            'transaksi.salesAe',
            'transaksi.kopKota',
            'transaksi.kopIklan',
        ])
        ->where('tanggal_muat', $tanggalMuat)
        ->where('status_tayang', '!=', 'BATAL')
        ->whereHas('transaksi', function ($q) {
            $q->where('status_order', '!=', 'Batal');
        });

        if ($halamanFilter !== 'all' && $halamanFilter !== '') {
            $query->whereHas('transaksi', function ($q) use ($halamanFilter) {
                $q->where('kop_kota_id', $halamanFilter);
            });
        }

        $jadwalList = $query->get();

        // Pengelompokan Data per Halaman Koran / Penempatan Biro
        $groupedByHalaman = [];
        $flatItems = [];
        $totalIklan = $jadwalList->count();
        $totalNilai = 0;
        $totalMmk = 0;
        $totalBaris = 0;
        $totalKlm = 0;
        $totalDsp = 0;
        $totalMm = 0;
        $cbpWarnings = 0;

        $allHalaman = KopKota::where('is_active', true)->orderBy('nomor_halaman')->get();

        // Inisialisasi slot penempatan
        foreach ($allHalaman as $hal) {
            if ($halamanFilter === 'all' || $halamanFilter == $hal->id) {
                $groupedByHalaman[$hal->id] = [
                    'halaman_info'  => $hal,
                    'nama'          => $hal->nama_penempatan,
                    'wilayah'       => $hal->wilayah_biro,
                    'nomor_halaman' => $hal->nomor_halaman,
                    'items'         => [],
                    'total_mmk'     => 0,
                    'total_baris'   => 0,
                    'total_nominal' => 0,
                ];
            }
        }

        // Slot fallback untuk halaman tanpa relasi kop_kota spesifik
        $slotLainnya = 'LAINNYA';
        $groupedByHalaman[$slotLainnya] = [
            'halaman_info'  => null,
            'nama'          => 'Halaman Dalam / Reguler',
            'wilayah'       => 'Umum',
            'nomor_halaman' => 99,
            'items'         => [],
            'total_mmk'     => 0,
            'total_baris'   => 0,
            'total_nominal' => 0,
        ];

        foreach ($jadwalList as $item) {
            $trx = $item->transaksi;
            if (!$trx) continue;

            $halKey = $trx->kop_kota_id ?: $slotLainnya;
            if (!isset($groupedByHalaman[$halKey])) {
                $halKey = $slotLainnya;
            }

            $isBaris = ($trx->kategori_form === 'BARIS');
            $mmkItem = ($isBaris) ? 0 : (($trx->lebar_kolom ?? 1) * ($trx->tinggi_mm ?? 1));
            $barisItem = ($isBaris) ? ($trx->jumlah_baris ?? 2) : 0;
            $klmItem = ($isBaris) ? 0 : (int) ($trx->lebar_kolom ?? 1);
            $mmItem = ($isBaris) ? 0 : (int) ($trx->tinggi_mm ?? 0);
            $dspItem = ($isBaris) ? 0 : 1;
            $nominalItem = ($trx->total_muatkoran > 0) ? (int) round($trx->totaltagihan_transaksikoran / $trx->total_muatkoran) : $trx->totaltagihan_transaksikoran;
            $kopKotaNama = $trx->kopKota->nama_penempatan ?? ($trx->kopKota->wilayah_biro ?? ($trx->halaman_iklan ?? '-'));

            $isCbpBlocked = ($trx->jenis_pembayaran === 'Tunai' && $trx->piutang_transaksikoran > 0) || ($trx->status_order === 'Menunggu_Bayar');
            if ($isCbpBlocked) {
                $cbpWarnings++;
            }

            $itemData = [
                'jadwal_id'       => $item->id,
                'transaksi_id'    => $trx->id,
                'nofaktur'        => $trx->nofakturkoran,
                'oti'             => $trx->nofakturkoran,
                'edisi_ke'        => $item->edisi_ke,
                'pengiklan'       => $trx->nama_pemasangkoran,
                'produk'          => $trx->iklankoran->nama_jenis ?? $trx->produk_jasa ?? 'Iklan',
                'kategori_form'   => $trx->kategori_form,
                'warna'           => $trx->warna_iklan,
                'ukuran'          => $isBaris ? "{$barisItem} Baris" : "{$trx->lebar_kolom} kol × {$trx->tinggi_mm} mm ({$mmkItem} mmk)",
                'lebar_kolom'     => $klmItem,
                'tinggi_mm'       => $mmItem,
                'dsp_count'       => $dspItem,
                'mmk'             => $mmkItem,
                'jumlah_baris'    => $barisItem,
                'naskah_baris'    => $trx->naskah_iklan_baris,
                'rubrik_kop'      => $trx->kopIklan->nama_kop ?? '-',
                'kop_kota'        => $kopKotaNama,
                'status_materi'   => $trx->status_materi,
                'file_materi'     => $trx->file_materi,
                'keterangan'      => $trx->keterangan_materi,
                'sales'           => $trx->sales_iklankoran,
                'nominal_edisi'   => $nominalItem,
                'is_cbp_blocked'  => $isCbpBlocked,
                'jenis_bayar'     => $trx->jenis_pembayaran,
            ];

            $groupedByHalaman[$halKey]['items'][] = $itemData;
            $flatItems[] = $itemData;

            $groupedByHalaman[$halKey]['total_mmk']     += $mmkItem;
            $groupedByHalaman[$halKey]['total_baris']   += $barisItem;
            $groupedByHalaman[$halKey]['total_nominal'] += $nominalItem;

            $totalMmk   += $mmkItem;
            $totalBaris += $barisItem;
            $totalKlm   += $klmItem;
            $totalDsp   += $dspItem;
            $totalMm    += $mmItem;
            $totalNilai += $nominalItem;
        }

        // Hapus grup halaman yang kosong jika bukan filter spesifik
        if ($halamanFilter === 'all') {
            $groupedByHalaman = array_filter($groupedByHalaman, fn($g) => count($g['items']) > 0);
        }

        // Status Kunci Cut-Off pada tanggal ini
        $isDateLocked = DailyCutoff::isDateLocked($tanggalMuat);
        $cutoffData = DailyCutoff::where('tanggal_edisi', $tanggalMuat)->first();

        $carbonTgl = Carbon::parse($tanggalMuat);
        $hariNama = strtoupper($carbonTgl->locale('id')->isoFormat('dddd'));
        $tanggalDmy = $carbonTgl->format('d-m-Y');

        return [
            'tanggalMuat'      => $tanggalMuat,
            'tanggalFormatted' => $carbonTgl->translatedFormat('l, d F Y'),
            'hariNama'         => $hariNama,
            'tanggalDmy'       => $tanggalDmy,
            'hariTanggal'      => "{$hariNama} / {$tanggalDmy}",
            'halamanFilter'    => $halamanFilter,
            'allHalaman'       => $allHalaman,
            'groupedByHalaman' => $groupedByHalaman,
            'flatItems'        => $flatItems,
            'totalIklan'       => $totalIklan,
            'totalNilai'       => $totalNilai,
            'totalMmk'         => $totalMmk,
            'totalBaris'       => $totalBaris,
            'totalKlm'         => $totalKlm,
            'totalDsp'         => $totalDsp,
            'totalMm'          => $totalMm,
            'cbpWarnings'      => $cbpWarnings,
            'isDateLocked'     => $isDateLocked,
            'cutoffData'       => $cutoffData,
        ];
    }
}
