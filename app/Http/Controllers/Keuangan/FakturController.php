<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Faktur;
use App\Models\KasTransaksi;
use App\Models\PembayaranFaktur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FakturController extends Controller
{
    /**
     * Daftar Faktur Penjualan (Iklan & Sirkulasi).
     */
    public function index(Request $request)
    {
        $query = Faktur::with(['customer', 'creator', 'penugasanAktif.petugas']);

        // Filter Kategori
        if ($request->filled('kategori') && in_array($request->kategori, ['iklan', 'sirkulasi'])) {
            $query->where('kategori', $request->kategori);
        }

        // Filter Status
        if ($request->filled('status') && in_array($request->status, ['belum_bayar', 'cicilan', 'lunas'])) {
            $query->where('status', $request->status);
        }

        // Filter Pencarian
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_faktur', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('nama_customer', 'like', "%{$q}%");
                    });
            });
        }

        $fakturs = $query->latest('id')->paginate(12)->withQueryString();

        // Ringkasan KPI Faktur
        $totalFakturCount = Faktur::count();
        $totalTagihanSum  = (float) Faktur::sum('total_tagihan');
        $totalTerbayarSum = (float) Faktur::sum('total_terbayar');
        $totalPiutangSum  = (float) Faktur::sum('sisa_piutang');
        $fakturLunasCount = Faktur::where('status', 'lunas')->count();
        $fakturPiutangCount = Faktur::where('status', '!=', 'lunas')->count();

        return view('pages.keuangan.faktur.index', compact(
            'fakturs',
            'totalFakturCount',
            'totalTagihanSum',
            'totalTerbayarSum',
            'totalPiutangSum',
            'fakturLunasCount',
            'fakturPiutangCount'
        ));
    }

    /**
     * Form Penerbitan Faktur Baru.
     */
    public function create(Request $request)
    {
        $kategoriDefault = $request->get('kategori', 'iklan');
        $customers = Customer::orderBy('nama_customer')->get();

        // Generate Nomor Faktur Otomatis
        $prefix = $kategoriDefault === 'sirkulasi' ? 'INV-SRK-' : 'INV-IKL-';
        $period = date('Ym');
        $count = Faktur::where('no_faktur', 'like', "{$prefix}{$period}-%")->count() + 1;
        $noFakturSuggested = $prefix . $period . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        return view('pages.keuangan.faktur.create', compact('customers', 'kategoriDefault', 'noFakturSuggested'));
    }

    /**
     * Simpan Penerbitan Faktur Baru dengan Logika Transaksi Keuangan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_faktur'        => 'required|string|unique:faktur,no_faktur',
            'customer_id'      => 'required|exists:customers,id',
            'kategori'         => 'required|in:iklan,sirkulasi',
            'tanggal_faktur'   => 'required|date',
            'jatuh_tempo'      => 'required|date|after_or_equal:tanggal_faktur',
            'item_nama'        => 'required|array|min:1',
            'item_qty'         => 'required|array|min:1',
            'item_harga'       => 'required|array|min:1',
            'tipe_pembayaran'  => 'required|in:lunas_langsung,cicilan_tempo',
            'nominal_dp'       => 'nullable|numeric|min:0',
            'metode_bayar'     => 'required|in:tunai,transfer',
            'catatan'          => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Susun Rincian Biaya
            $rincianBiaya = [];
            $totalTagihan = 0.0;
            foreach ($request->item_nama as $index => $nama) {
                if (trim($nama) === '') continue;
                $qty = max(1, (float) ($request->item_qty[$index] ?? 1));
                $harga = max(0, (float) ($request->item_harga[$index] ?? 0));
                $subtotal = $qty * $harga;
                $totalTagihan += $subtotal;

                $rincianBiaya[] = [
                    'item'     => $nama,
                    'qty'      => $qty,
                    'harga'    => $harga,
                    'subtotal' => $subtotal,
                ];
            }

            if (empty($rincianBiaya) || $totalTagihan <= 0) {
                return back()->withInput()->with('error', 'Minimal harus ada 1 item transaksi dengan harga lebih dari 0.');
            }

            $userId = Auth::id();
            $customer = Customer::findOrFail($request->customer_id);

            // 2. Evaluasi Logika Pembayaran
            if ($request->tipe_pembayaran === 'lunas_langsung') {
                // Skenario A: Bayar Lunas Langsung (100%)
                $totalTerbayar = $totalTagihan;
                $sisaPiutang   = 0.0;
                $status        = 'lunas';

                $faktur = Faktur::create([
                    'no_faktur'      => $request->no_faktur,
                    'customer_id'    => $customer->id,
                    'kategori'       => $request->kategori,
                    'tanggal_faktur' => $request->tanggal_faktur,
                    'jatuh_tempo'    => $request->jatuh_tempo,
                    'rincian_biaya'  => $rincianBiaya,
                    'total_tagihan'  => $totalTagihan,
                    'total_terbayar' => $totalTerbayar,
                    'sisa_piutang'   => $sisaPiutang,
                    'status'         => $status,
                    'created_by'     => $userId,
                    'catatan'        => $request->catatan,
                ]);

                // Catat Kwitansi Pelunasan
                $kwitansiNo = 'KW-' . date('Ym') . '-' . str_pad(PembayaranFaktur::count() + 1, 4, '0', STR_PAD_LEFT);
                $bayar = PembayaranFaktur::create([
                    'no_kwitansi'         => $kwitansiNo,
                    'faktur_id'           => $faktur->id,
                    'penugasan_inkaso_id' => null,
                    'tanggal_bayar'       => $request->tanggal_faktur,
                    'nominal_bayar'       => $totalTagihan,
                    'metode_bayar'        => $request->metode_bayar,
                    'tipe_bayar'          => 'lunas_langsung',
                    'diterima_oleh'       => $userId,
                    'catatan'             => 'Pelunasan 100% langsung saat penerbitan faktur',
                ]);

                // Mutasi Kas Masuk Otomatis
                KasTransaksi::catatMutasi(
                    'masuk',
                    'Pelunasan Faktur ' . ucfirst($request->kategori),
                    $totalTagihan,
                    'Pelunasan langsung Faktur ' . $faktur->no_faktur . ' (' . $customer->nama_customer . ')',
                    $bayar,
                    $userId,
                    $request->tanggal_faktur
                );

                $pesan = 'Faktur ' . $faktur->no_faktur . ' berhasil diterbitkan dan LUNAS (Kas masuk tercatat otomatis tanpa penugasan Inkaso).';
            } else {
                // Skenario B: Bayar Bertahap / Nyicil / Tempo
                $dp = (float) ($request->nominal_dp ?? 0);
                if ($dp > $totalTagihan) {
                    $dp = $totalTagihan;
                }

                $totalTerbayar = $dp;
                $sisaPiutang   = max(0, $totalTagihan - $dp);
                $status        = ($sisaPiutang <= 0) ? 'lunas' : (($dp > 0) ? 'cicilan' : 'belum_bayar');

                $faktur = Faktur::create([
                    'no_faktur'      => $request->no_faktur,
                    'customer_id'    => $customer->id,
                    'kategori'       => $request->kategori,
                    'tanggal_faktur' => $request->tanggal_faktur,
                    'jatuh_tempo'    => $request->jatuh_tempo,
                    'rincian_biaya'  => $rincianBiaya,
                    'total_tagihan'  => $totalTagihan,
                    'total_terbayar' => $totalTerbayar,
                    'sisa_piutang'   => $sisaPiutang,
                    'status'         => $status,
                    'created_by'     => $userId,
                    'catatan'        => $request->catatan,
                ]);

                // Jika ada DP, catat kwitansi & kas masuk
                if ($dp > 0) {
                    $kwitansiNo = 'KW-' . date('Ym') . '-' . str_pad(PembayaranFaktur::count() + 1, 4, '0', STR_PAD_LEFT);
                    $bayarDp = PembayaranFaktur::create([
                        'no_kwitansi'         => $kwitansiNo,
                        'faktur_id'           => $faktur->id,
                        'penugasan_inkaso_id' => null,
                        'tanggal_bayar'       => $request->tanggal_faktur,
                        'nominal_bayar'       => $dp,
                        'metode_bayar'        => $request->metode_bayar,
                        'tipe_bayar'          => 'cicilan_inkaso',
                        'diterima_oleh'       => $userId,
                        'catatan'             => 'Uang Muka (DP) saat penerbitan faktur',
                    ]);

                    KasTransaksi::catatMutasi(
                        'masuk',
                        'Uang Muka (DP) Faktur ' . ucfirst($request->kategori),
                        $dp,
                        'Penerimaan DP Faktur ' . $faktur->no_faktur . ' (' . $customer->nama_customer . ')',
                        $bayarDp,
                        $userId,
                        $request->tanggal_faktur
                    );
                }

                $pesan = 'Faktur ' . $faktur->no_faktur . ' berhasil diterbitkan dengan sisa piutang Rp ' . number_format($sisaPiutang, 0, ',', '.') . ' (siap ditugaskan ke Inkaso).';
            }

            return redirect()->route('keuangan.faktur.show', $faktur->id)->with('success', $pesan);
        });
    }

    /**
     * Detail Rincian Faktur, Pembayaran, dan Status Inkaso.
     */
    public function show($id)
    {
        $faktur = Faktur::with([
            'customer',
            'creator',
            'pembayaran.penerima',
            'penugasanInkaso.petugas',
        ])->findOrFail($id);

        return view('pages.keuangan.faktur.show', compact('faktur'));
    }

    /**
     * Cetak Lembar Faktur Standar Kabar Priangan.
     */
    public function cetak($id)
    {
        $faktur = Faktur::with(['customer', 'creator', 'pembayaran'])->findOrFail($id);
        return view('pages.keuangan.faktur.cetak', compact('faktur'));
    }
}
