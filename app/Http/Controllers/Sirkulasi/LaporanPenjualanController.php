<?php

namespace App\Http\Controllers\Sirkulasi;

use App\Http\Controllers\Controller;
use App\Services\SirkulasiDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanPenjualanController extends Controller
{
    public function __construct(protected SirkulasiDataService $sirkulasi)
    {
    }

    /**
     * Laporan Penjualan (Rekapitulasi bulanan ke Keuangan/Faktur).
     */
    public function index(Request $request)
    {
        $cfg = $this->sirkulasi->getConfig();
        $hargaBesar = $cfg['harga_agen_besar'];
        $hargaEceran = $cfg['harga_eceran'];

        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $daftarAgen = $this->sirkulasi->getDaftarAgen();
        $periodStr = str_replace('-', '', $bulanPilihan);
        $tanggalFaktur = Carbon::parse($bulanPilihan . '-01')->endOfMonth();
        $jatuhTempo = (clone $tanggalFaktur)->addDays(14);

        $rekapPenjualan = $daftarAgen->map(function ($agen) use ($periodStr, $tanggalFaktur, $jatuhTempo, $hargaBesar, $hargaEceran) {
            $kirim = $agen->jatah_default * 30;
            $retur = (int) ($kirim * 0.022);
            $nett = $kirim - $retur;
            $harga = $agen->kategori === '11' ? $hargaBesar : $hargaEceran;
            $nilaiTagihan = $nett * $harga;
            $noFaktur = sprintf('FKT-SRK-%s-%03d', $periodStr, $agen->id);

            return (object)[
                'id'             => $agen->id,
                'no_faktur'      => $noFaktur,
                'kode_agen'      => $agen->kode_agen,
                'nama_agen'      => $agen->nama_agen,
                'alamat'         => $agen->alamat,
                'telepon'        => $agen->telepon,
                'wilayah'        => $agen->wilayah_nama,
                'jurusan'        => $agen->jurusan_nama,
                'kategori'       => $agen->kategori,
                'kirim'          => $kirim,
                'retur'          => $retur,
                'nett_sales'     => $nett,
                'harga_satuan'   => $harga,
                'nilai_tagihan'  => $nilaiTagihan,
                'harga_bayar'    => $nilaiTagihan,
                'saldo_piutang'  => $agen->saldo_piutang,
                'status_bayar'   => $agen->status === 'aktif' ? 'Lancar' : 'Terkunci',
                'tanggal_faktur' => $tanggalFaktur,
                'jatuh_tempo'    => $jatuhTempo,
            ];
        });

        $totalKirim = $rekapPenjualan->sum('kirim');
        $totalRetur = $rekapPenjualan->sum('retur');
        $totalNett = $rekapPenjualan->sum('nett_sales');
        $totalTagihan = $rekapPenjualan->sum('nilai_tagihan');
        $totalSaldoPiutang = $rekapPenjualan->sum('saldo_piutang');

        return view('pages.sirkulasi.cetak.penjualan', compact(
            'bulanPilihan',
            'rekapPenjualan',
            'totalKirim',
            'totalRetur',
            'totalNett',
            'totalTagihan',
            'totalSaldoPiutang'
        ));
    }

    /**
     * Pengalihan: Penerbitan Faktur Penjualan Agen berada di Divisi Faktur (Keuangan).
     */
    public function faktur(Request $request, $kode_agen = null)
    {
        $user = auth()->user();
        if ($user && in_array($user->role, ['faktur', 'keuangan', 'admin_keuangan', 'administrator'], true)) {
            return redirect()->route('keuangan.faktur.sirkulasi.index');
        }

        return redirect()->route('sirkulasi.cetak.penjualan')
            ->with('error', 'Menu Faktur Agen berada di Divisi Faktur & Penagihan Piutang oleh Bagian Inkaso.');
    }
}
