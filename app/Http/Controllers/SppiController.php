<?php

namespace App\Http\Controllers;

use App\Models\Sppi;
use App\Models\TransaksiKoran;
use Illuminate\Http\Request;

class SppiController extends Controller
{
    /**
     * Tampilan cetak Surat Perjanjian Pemasangan Iklan (SPPI / Insertion Order)
     */
    public function cetak($transaksiId)
    {
        $transaksi = TransaksiKoran::with(['pelanggan', 'iklankoran', 'inputOleh'])->findOrFail($transaksiId);

        // Ambil atau generate nomor SPPI otomatis jika belum ada
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

        return view('pages.iklan.transaksi.koran.sppi', compact('transaksi', 'sppi'));
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

        // Sinkronisasi status SPPI di transaksi koran
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

        return redirect()->back()->with('success', 'Status SPPI berhasil diperbarui menjadi Sudah Ditandatangani!');
    }
}
