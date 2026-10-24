<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Faktur;
use App\Models\Pelanggan;
use App\Models\PembayaranFaktur;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FakturSyncService
{
    /**
     * Sinkronisasi Transaksi Iklan (Koran / Online / TV) ke Buku Faktur Keuangan
     * Menjamin 1 Transaksi = 1 Nomor Faktur Resmi di seluruh sistem.
     */
    public static function syncFromTransaksi(object $transaksi, string $mediaType): ?Faktur
    {
        $userId = Auth::id() ?? 1;

        // 1. Ekstraksi Field Berdasarkan Tipe Media
        if ($mediaType === 'koran') {
            $noFaktur       = $transaksi->nofakturkoran;
            $namaPelanggan  = $transaksi->nama_pemasangkoran;
            $alamat         = $transaksi->alamat_pemasangkoran ?? '-';
            $telp           = $transaksi->pelanggan?->no_telp ?? '-';
            $tanggalFaktur  = $transaksi->tanggal_transaksikoran;
            $jatuhTempo     = $transaksi->jatuh_tempo_pembayaran ?: $transaksi->tanggal_transaksikoran;
            $salesNama      = $transaksi->sales_iklankoran;
            $totalTagihan   = (float) $transaksi->totaltagihan_transaksikoran;
            $totalTerbayar  = (float) $transaksi->jumlahbayar_transaksikoran;
            $sisaPiutang    = (float) $transaksi->piutang_transaksikoran;
            $kategoriForm   = $transaksi->kategori_form ?? 'Display';
            $catatan        = "Order Iklan Koran ({$kategoriForm}) - Edisi: " . ($transaksi->tanggal_muatkoran ?? '-');
        } elseif ($mediaType === 'online') {
            $noFaktur       = $transaksi->nofakturonline;
            $namaPelanggan  = $transaksi->nama_pemasangonline;
            $alamat         = $transaksi->alamat_pemasangonline ?? '-';
            $telp           = $transaksi->pelanggan?->no_telp ?? $transaksi->no_telp ?? '-';
            $tanggalFaktur  = $transaksi->tanggal_transaksionline;
            $jatuhTempo     = $transaksi->tanggal_transaksionline;
            $salesNama      = $transaksi->sales_iklanonline;
            $totalTagihan   = (float) $transaksi->totaltagihan_transaksionline;
            $totalTerbayar  = (float) $transaksi->jumlahbayar_transaksionline;
            $sisaPiutang    = (float) $transaksi->piutang_transaksionline;
            $platform       = is_array($transaksi->platforms_publikasi) ? implode(', ', $transaksi->platforms_publikasi) : ($transaksi->portal_iklanonline ?? 'Online');
            $catatan        = "Order Iklan Online ({$platform}) - Materi: " . ($transaksi->materi_iklan ?? '-');
        } else { // Priangan TV
            $noFaktur       = $transaksi->nofakturpriangan;
            $namaPelanggan  = $transaksi->nama_pemasangpriangan;
            $alamat         = $transaksi->alamat_pemasangpriangan ?? '-';
            $telp           = $transaksi->pelanggan?->no_telp ?? $transaksi->no_telp ?? '-';
            $tanggalFaktur  = $transaksi->tanggal_transaksipriangan;
            $jatuhTempo     = $transaksi->tanggal_transaksipriangan;
            $salesNama      = $transaksi->sales_iklanpriangan;
            $totalTagihan   = (float) $transaksi->totaltagihan_transaksipriangan;
            $totalTerbayar  = (float) $transaksi->jumlahbayar_transaksipriangan;
            $sisaPiutang    = (float) $transaksi->piutang_transaksipriangan;
            $catatan        = "Order Iklan Priangan TV - Materi: " . ($transaksi->materi_iklan ?? '-');
        }

        if (empty($noFaktur)) {
            return null;
        }

        // 2. Hubungkan atau Buat Master Customer di Keuangan (Sync dengan Pelanggan)
        $customer = Customer::where('nama_customer', $namaPelanggan)->first();
        if (!$customer) {
            $customer = Customer::create([
                'nama_customer' => $namaPelanggan,
                'no_telepon'    => $telp,
                'alamat'        => $alamat,
                'tipe'          => 'iklan',
            ]);
        }

        // 3. Status Pelunasan
        $status = ($sisaPiutang <= 0) ? 'lunas' : (($totalTerbayar > 0) ? 'cicilan' : 'belum_bayar');

        // 4. Update atau Create Faktur Resmi Keuangan
        $faktur = Faktur::updateOrCreate(
            ['no_faktur' => $noFaktur],
            [
                'customer_id'    => $customer->id,
                'sales_nama'     => $salesNama,
                'kategori'       => 'iklan',
                'tipe_faktur'    => 'reguler',
                'tanggal_faktur' => $tanggalFaktur,
                'jatuh_tempo'    => $jatuhTempo,
                'total_tagihan'  => $totalTagihan,
                'total_terbayar' => $totalTerbayar,
                'sisa_piutang'   => $sisaPiutang,
                'status'         => $status,
                'status_bayar'   => ($status === 'lunas') ? 'lunas' : 'belum',
                'created_by'     => $userId,
                'catatan'        => $catatan,
            ]
        );

        // 5. Catat atau perbarui Pembayaran ke tabel pembayaran_faktur
        if ($faktur) {
            $pembayaran = PembayaranFaktur::where('faktur_id', $faktur->id)->first();
            if ($totalTerbayar > 0) {
                if (!$pembayaran) {
                    $kwitansiNo = 'KW-' . date('Ym') . '-' . str_pad($faktur->id, 4, '0', STR_PAD_LEFT);
                    PembayaranFaktur::create([
                        'no_kwitansi'         => $kwitansiNo,
                        'faktur_id'           => $faktur->id,
                        'penugasan_inkaso_id' => null,
                        'tanggal_bayar'       => $tanggalFaktur,
                        'nominal_bayar'       => $totalTerbayar,
                        'metode_bayar'        => 'kasir',
                        'tipe_bayar'          => ($status === 'lunas') ? 'lunas_langsung' : 'dp_titipan',
                        'diterima_oleh'       => $userId,
                        'catatan'             => 'Penerimaan awal order di loket iklan',
                    ]);
                } else {
                    $pembayaran->update([
                        'nominal_bayar' => $totalTerbayar,
                        'tipe_bayar'    => ($status === 'lunas') ? 'lunas_langsung' : 'dp_titipan',
                    ]);
                }
            } elseif ($pembayaran && $totalTerbayar == 0) {
                $pembayaran->delete();
            }
        }

        return $faktur;
    }

    /**
     * Sinkronisasi pembatalan transaksi ke Buku Faktur Keuangan
     */
    public static function cancelFaktur(string $noFaktur, string $alasan = ''): bool
    {
        $faktur = Faktur::where('no_faktur', $noFaktur)->first();
        if (!$faktur) {
            return false;
        }

        $faktur->update([
            'status'       => 'batal',
            'status_bayar' => 'batal',
            'sisa_piutang' => 0,
            'catatan'      => trim($faktur->catatan . " [DIBATALKAN: {$alasan}]"),
        ]);

        return true;
    }

    /**
     * Hapus faktur draft jika transaksi belum memiliki pembayaran dan dihapus
     */
    public static function deleteFaktur(string $noFaktur): bool
    {
        $faktur = Faktur::where('no_faktur', $noFaktur)->first();
        if ($faktur && $faktur->total_terbayar <= 0) {
            $faktur->pembayaran()->delete();
            return (bool) $faktur->delete();
        }

        return false;
    }
}
