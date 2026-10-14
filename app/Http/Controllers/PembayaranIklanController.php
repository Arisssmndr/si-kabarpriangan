<?php

namespace App\Http\Controllers;

use App\Models\PembayaranIklan;
use App\Models\TransaksiKoran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranIklanController extends Controller
{
    /**
     * Riwayat pembayaran kasir
     */
    public function index(Request $request)
    {
        $query = PembayaranIklan::with(['kasir', 'transaksiKoran'])->latest();

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('nokwitansi', 'like', "%{$keyword}%")
                  ->orWhere('nofaktur', 'like', "%{$keyword}%")
                  ->orWhere('nama_penyetor', 'like', "%{$keyword}%");
            });
        }

        $pembayaran = $query->paginate(15)->withQueryString();

        return view('pages.iklan.transaksi.koran.pembayaran_index', compact('pembayaran'));
    }

    /**
     * Catat pembayaran baru untuk transaksi iklan (CBP Gate / Pelunasan)
     */
    public function store(Request $request)
    {
        $request->validate([
            'transaksi_id'   => 'required|integer',
            'media_type'     => 'required|in:koran,online,priangan',
            'nominal_bayar'  => 'required',
            'potongan_pph23' => 'nullable',
            'metode_bayar'   => 'required|in:Tunai_Kasir,Transfer_Bank,Giro,Cek',
            'nama_penyetor'  => 'nullable|string|max:150',
            'catatan'        => 'nullable|string',
        ]);

        $nominal = (int) str_replace(['.', ','], '', $request->nominal_bayar);
        $pph23   = (int) str_replace(['.', ','], '', $request->potongan_pph23 ?? 0);

        if ($nominal <= 0) {
            return back()->withErrors(['nominal_bayar' => 'Nominal bayar harus lebih dari Rp 0!'])->withInput();
        }

        DB::beginTransaction();
        try {
            if ($request->media_type === 'koran') {
                $transaksi = TransaksiKoran::findOrFail($request->transaksi_id);
                $nofaktur  = $transaksi->nofakturkoran;

                $nokwitansi = PembayaranIklan::createCode('KRN');

                $pembayaran = PembayaranIklan::create([
                    'nokwitansi'     => $nokwitansi,
                    'media_type'     => 'koran',
                    'transaksi_id'   => $transaksi->id,
                    'nofaktur'       => $nofaktur,
                    'tanggal_bayar'  => now()->toDateString(),
                    'nominal_bayar'  => $nominal,
                    'potongan_pph23' => $pph23,
                    'metode_bayar'   => $request->metode_bayar,
                    'nama_penyetor'  => $request->nama_penyetor ?? $transaksi->nama_pemasangkoran,
                    'catatan'        => $request->catatan,
                    'kasir_user_id'  => Auth::id(),
                ]);

                // Update saldo bayar dan piutang di transaksi koran
                $totalBayarBaru = $transaksi->jumlahbayar_transaksikoran + $nominal + $pph23;
                $piutangBaru    = max(0, $transaksi->totaltagihan_transaksikoran - $totalBayarBaru);

                $updateData = [
                    'jumlahbayar_transaksikoran' => $totalBayarBaru,
                    'piutang_transaksikoran'      => $piutangBaru,
                ];

                // Buka gerbang CBP Gate jika sudah lunas
                if ($piutangBaru <= 0) {
                    $updateData['status_order'] = 'Lunas';
                } elseif ($transaksi->status_order === 'Menunggu_Bayar') {
                    // Jika sebelumnya terblokir di CBP gate dan sudah ada pembayaran DP/lunas
                    $updateData['status_order'] = 'Antrean_Pracetak';
                }

                $transaksi->update($updateData);

                DB::commit();

                return redirect()->route('pembayaran-iklan.kwitansi', $pembayaran->id)
                    ->with('success', "Pembayaran berhasil dicatat dengan No. Kwitansi {$nokwitansi}!");
            }

            DB::commit();
            return back()->with('success', 'Pembayaran berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal mencatat pembayaran: ' . $e->getMessage()]);
        }
    }

    /**
     * Tampilan cetak kwitansi resmi Kabar Priangan
     */
    public function kwitansi($id)
    {
        $pembayaran = PembayaranIklan::with(['kasir', 'transaksiKoran.pelanggan'])->findOrFail($id);
        return view('pages.iklan.transaksi.koran.kwitansi', compact('pembayaran'));
    }
}
