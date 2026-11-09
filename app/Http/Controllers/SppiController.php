<?php

namespace App\Http\Controllers;

use App\Models\SistemConfig;
use App\Models\Sppi;
use App\Models\TransaksiKoran;
use App\Models\TransaksiOnline;
use App\Models\TransaksiPriangan;
use Illuminate\Http\Request;

class SppiController extends Controller
{
    /**
     * Tampilan cetak Surat Perintah Pemuatan Iklan (SPPI) - Koran
     */
    public function cetak($transaksiId)
    {
        $transaksi = TransaksiKoran::with(['pelanggan', 'iklankoran', 'salesAe', 'inputOleh'])->findOrFail($transaksiId);

        $sppi = Sppi::firstOrCreate(
            [
                'media_type'   => 'koran',
                'transaksi_id' => $transaksi->id,
            ],
            [
                'nomor_sppi'   => Sppi::createCode('KRN'),
                'nofaktur'     => $transaksi->nofakturkoran,
                'tanggal_sppi' => $transaksi->tanggal_transaksikoran ?? now()->toDateString(),
                'status_sppi'  => 'Belum_Ditandatangani',
            ]
        );

        $bruto = (int) ($transaksi->harga_transaksikoran * max(1, (int) $transaksi->total_muatkoran));
        $diskon = (int) ($transaksi->diskon_transaksikoran ?? 0);
        $dpp = max(0, $bruto - $diskon);
        $ppnPersen = $transaksi->ppn_persen ?: SistemConfig::getPpnPersen();
        $ppn = (int) ($transaksi->ppn_transaksikoran ?? round($dpp * ($ppnPersen / 100)));
        $totalTagihan = (int) ($transaksi->totaltagihan_transaksikoran ?: ($dpp + $ppn));
        $dibayar = (int) ($transaksi->jumlahbayar_transaksikoran ?? 0);
        $piutang = (int) ($transaksi->piutang_transaksikoran ?? max(0, $totalTagihan - $dibayar));

        $jadwalStr = !empty($transaksi->tanggal_muat_list)
            ? (is_array($transaksi->tanggal_muat_list) ? implode(', ', $transaksi->tanggal_muat_list) : $transaksi->tanggal_muat_list)
            : ($transaksi->tanggal_muatkoran ? \Carbon\Carbon::parse($transaksi->tanggal_muatkoran)->translatedFormat('d F Y') : '-');

        $sppiData = [
            'media_label'   => 'Harian Umum Kabar Priangan (Cetak)',
            'back_url'      => route('transaksikoran.index'),
            'nomor_sppi'    => $sppi->nomor_sppi,
            'nofaktur'      => $transaksi->nofakturkoran,
            'tanggal_order' => $transaksi->tanggal_transaksikoran,
            'nama_pemasang' => $transaksi->nama_pemasangkoran,
            'alamat'        => $transaksi->alamat_pemasangkoran ?: ($transaksi->pelanggan->alamat ?? '-'),
            'telepon'       => $transaksi->no_telp ?: ($transaksi->pelanggan->no_telp ?? '-'),
            'npwp_nik'      => $transaksi->pelanggan->npwp_nik ?? '-',
            'sales'         => $transaksi->salesAe?->nama_ae ?: ($transaksi->sales_iklankoran ?: 'Front Office'),
            'jenis_iklan'   => $transaksi->iklankoran?->nama_jenis ?: ($transaksi->kategori_form ?: 'Iklan Koran'),
            'spesifikasi'   => $transaksi->ukuran_iklan ?: ($transaksi->jumlah_baris ? $transaksi->jumlah_baris . ' Baris' : '-'),
            'warna_media'   => $transaksi->warna_iklan === 'FC' ? 'Full Color (FC)' : 'Hitam Putih (BW)',
            'penempatan'    => $transaksi->halaman_iklan ?: 'Halaman Reguler',
            'frekuensi'     => ($transaksi->total_muatkoran ?: 1) . ' Kali Terbit',
            'jadwal_tayang' => $jadwalStr,
            'catatan_materi'=> $transaksi->naskah_iklan_baris ?: ($transaksi->keterangan_materi ?: '-'),
            'bruto'         => $bruto,
            'diskon'        => $diskon,
            'dpp'           => $dpp,
            'ppn_persen'    => $ppnPersen,
            'ppn'           => $ppn,
            'total_tagihan' => $totalTagihan,
            'dibayar'       => $dibayar,
            'piutang'       => $piutang,
            'cara_bayar'    => $piutang <= 0 ? 'Tunai / Lunas' : 'Piutang',
        ];

        return view('pages.iklan.transaksi.koran.sppi', compact('transaksi', 'sppi', 'sppiData'));
    }

    /**
     * Tampilan cetak Surat Perintah Pemuatan Iklan (SPPI) - Online
     */
    public function cetakOnline($transaksiId)
    {
        $transaksi = TransaksiOnline::with(['pelanggan', 'iklanonline', 'salesAe'])->findOrFail($transaksiId);

        $sppi = Sppi::firstOrCreate(
            [
                'media_type'   => 'online',
                'transaksi_id' => $transaksi->id,
            ],
            [
                'nomor_sppi'   => Sppi::createCode('ONL'),
                'nofaktur'     => $transaksi->nofakturonline,
                'tanggal_sppi' => $transaksi->tanggal_transaksionline ?? now()->toDateString(),
                'status_sppi'  => 'Belum_Ditandatangani',
            ]
        );

        $bruto = (int) ($transaksi->harga_transaksionline * max(1, (int) $transaksi->total_muatiklanonline));
        $diskon = (int) ($transaksi->diskon_transaksionline ?? 0);
        $dpp = max(0, $bruto - $diskon);
        $ppnPersen = $transaksi->ppn_persen ?: SistemConfig::getPpnPersen();
        $ppn = (int) ($transaksi->ppn_transaksionline ?? round($dpp * ($ppnPersen / 100)));
        $totalTagihan = (int) ($transaksi->totaltagihan_transaksionline ?: ($dpp + $ppn));
        $dibayar = (int) ($transaksi->jumlahbayar_transaksionline ?? 0);
        $piutang = (int) ($transaksi->piutang_transaksionline ?? max(0, $totalTagihan - $dibayar));

        $sppiData = [
            'media_label'   => 'Portal Berita & Media Sosial Kabar Priangan (Online)',
            'back_url'      => route('transaksionline.index'),
            'nomor_sppi'    => $sppi->nomor_sppi,
            'nofaktur'      => $transaksi->nofakturonline,
            'tanggal_order' => $transaksi->tanggal_transaksionline,
            'nama_pemasang' => $transaksi->nama_pemasangonline,
            'alamat'        => $transaksi->alamat_pemasangonline ?: ($transaksi->pelanggan->alamat ?? '-'),
            'telepon'       => $transaksi->no_kontak_pemasang ?: ($transaksi->pelanggan->no_telp ?? '-'),
            'npwp_nik'      => $transaksi->pelanggan->npwp_nik ?? '-',
            'sales'         => $transaksi->salesAe?->nama_ae ?: ($transaksi->sales_iklanonline ?: 'Front Office'),
            'jenis_iklan'   => $transaksi->iklanonline?->nama_jenis ?: ($transaksi->iklanonline?->jenis_iklanonline ?: 'Iklan Online'),
            'spesifikasi'   => $transaksi->bentuk_publikasi ?: 'Artikel / Banner Digital',
            'warna_media'   => 'Full Color Digital',
            'penempatan'    => $transaksi->portal_iklanonline ?: 'kabar-priangan.com',
            'frekuensi'     => ($transaksi->total_muatiklanonline ?: 1) . ' Kali Tayang',
            'jadwal_tayang' => $transaksi->tanggal_muatiklanonline ? \Carbon\Carbon::parse($transaksi->tanggal_muatiklanonline)->translatedFormat('d F Y') : '-',
            'catatan_materi'=> $transaksi->materi_iklan ?: ($transaksi->catatan_redaksi ?: '-'),
            'bruto'         => $bruto,
            'diskon'        => $diskon,
            'dpp'           => $dpp,
            'ppn_persen'    => $ppnPersen,
            'ppn'           => $ppn,
            'total_tagihan' => $totalTagihan,
            'dibayar'       => $dibayar,
            'piutang'       => $piutang,
            'cara_bayar'    => $piutang <= 0 ? 'Tunai / Lunas' : 'Piutang',
        ];

        return view('pages.iklan.transaksi.koran.sppi', compact('transaksi', 'sppi', 'sppiData'));
    }

    /**
     * Tampilan cetak Surat Perintah Pemuatan Iklan (SPPI) - Priangan TV
     */
    public function cetakPriangan($transaksiId)
    {
        $transaksi = TransaksiPriangan::with(['pelanggan', 'iklanpriangan', 'salesAe'])->findOrFail($transaksiId);

        $sppi = Sppi::firstOrCreate(
            [
                'media_type'   => 'priangan',
                'transaksi_id' => $transaksi->id,
            ],
            [
                'nomor_sppi'   => Sppi::createCode('PTV'),
                'nofaktur'     => $transaksi->nofakturpriangan,
                'tanggal_sppi' => $transaksi->tanggal_transaksipriangan ?? now()->toDateString(),
                'status_sppi'  => 'Belum_Ditandatangani',
            ]
        );

        $bruto = (int) ($transaksi->harga_transaksipriangan * max(1, (int) $transaksi->total_muatiklanpriangan));
        $diskon = (int) ($transaksi->diskon_transaksipriangan ?? 0);
        $dpp = max(0, $bruto - $diskon);
        $ppnPersen = $transaksi->ppn_persen ?: SistemConfig::getPpnPersen();
        $ppn = (int) ($transaksi->ppn_transaksipriangan ?? round($dpp * ($ppnPersen / 100)));
        $totalTagihan = (int) ($transaksi->totaltagihan_transaksipriangan ?: ($dpp + $ppn));
        $dibayar = (int) ($transaksi->jumlahbayar_transaksipriangan ?? 0);
        $piutang = (int) ($transaksi->piutang_transaksipriangan ?? max(0, $totalTagihan - $dibayar));

        $sppiData = [
            'media_label'   => 'Priangan TV (Penyiaran & Multimedia)',
            'back_url'      => route('transaksipriangan.index'),
            'nomor_sppi'    => $sppi->nomor_sppi,
            'nofaktur'      => $transaksi->nofakturpriangan,
            'tanggal_order' => $transaksi->tanggal_transaksipriangan,
            'nama_pemasang' => $transaksi->nama_pemasangpriangan,
            'alamat'        => $transaksi->alamat_pemasangpriangan ?: ($transaksi->pelanggan->alamat ?? '-'),
            'telepon'       => $transaksi->no_telp ?: ($transaksi->pelanggan->no_telp ?? '-'),
            'npwp_nik'      => $transaksi->pelanggan->npwp_nik ?? '-',
            'sales'         => $transaksi->salesAe?->nama_ae ?: ($transaksi->sales_iklanpriangan ?: 'Front Office'),
            'jenis_iklan'   => $transaksi->iklanpriangan?->nama_jenis ?: ($transaksi->iklanpriangan?->jenis_iklanpriangan ?: 'Iklan TV'),
            'spesifikasi'   => ($transaksi->durasi_detik ?? 30) . ' Detik / Spot',
            'warna_media'   => 'Video HD / Broadcast',
            'penempatan'    => $transaksi->jam_tayang ?: 'Prime Time / Reguler',
            'frekuensi'     => ($transaksi->total_muatiklanpriangan ?: 1) . ' Kali Tayang',
            'jadwal_tayang' => $transaksi->tanggal_muatiklanpriangan ? \Carbon\Carbon::parse($transaksi->tanggal_muatiklanpriangan)->translatedFormat('d F Y') : '-',
            'catatan_materi'=> $transaksi->materi_iklan ?: '-',
            'bruto'         => $bruto,
            'diskon'        => $diskon,
            'dpp'           => $dpp,
            'ppn_persen'    => $ppnPersen,
            'ppn'           => $ppn,
            'total_tagihan' => $totalTagihan,
            'dibayar'       => $dibayar,
            'piutang'       => $piutang,
            'cara_bayar'    => $piutang <= 0 ? 'Tunai / Lunas' : 'Piutang',
        ];

        return view('pages.iklan.transaksi.koran.sppi', compact('transaksi', 'sppi', 'sppiData'));
    }

    /**
     * Konfirmasi tanda tangan SPPI oleh klien/pemasang
     */
    public function konfirmasiTtd(Request $request, $id)
    {
        $sppi = Sppi::findOrFail($id);

        $request->validate([
            'ditandatangani_oleh_nama' => 'required|string|max:150',
            'tanggal_ditandatangani'   => 'required|date',
        ]);

        $sppi->update([
            'status_sppi'              => 'Sudah_Ditandatangani',
            'tanggal_ditandatangani'   => $request->tanggal_ditandatangani,
            'ditandatangani_oleh_nama' => $request->ditandatangani_oleh_nama,
        ]);

        if ($sppi->media_type === 'koran') {
            $transaksi = TransaksiKoran::find($sppi->transaksi_id);
            if ($transaksi) {
                $update = ['status_sppi' => 'Sudah_Ditandatangani'];
                if ($transaksi->status_order === 'Menunggu_SPPI') {
                    $update['status_order'] = 'Antrean_Pracetak';
                }
                $transaksi->update($update);
            }
        }

        return redirect()->back()->with('success', 'Status SPPI berhasil diperbarui.');
    }
}

