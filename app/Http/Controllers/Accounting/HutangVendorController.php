<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\AkunPerkiraan;
use App\Models\AuditLogAkuntansi;
use App\Models\JurnalUmum;
use App\Models\JurnalUmumDetail;
use App\Models\PeriodeAkuntansi;
use App\Models\VendorPembayaran;
use App\Models\VendorTagihan;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HutangVendorController extends Controller
{
    public function __construct(protected AccountingService $accountingService)
    {
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $status = $request->input('status', 'all');

        $query = VendorTagihan::with(['pembayarans', 'jurnalAkui']);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('no_tagihan', 'like', "%{$q}%")
                    ->orWhere('no_invoice_vendor', 'like', "%{$q}%")
                    ->orWhere('nama_vendor', 'like', "%{$q}%")
                    ->orWhere('kategori_beban', 'like', "%{$q}%");
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $tagihans = $query->latest('tanggal_tagihan')->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total_utang_aktif' => (float) VendorTagihan::whereNotIn('status', ['lunas', 'batal'])->sum('sisa_utang'),
            'jatuh_tempo_dekat' => VendorTagihan::whereNotIn('status', ['lunas', 'batal'])
                ->whereNotNull('jatuh_tempo')
                ->whereDate('jatuh_tempo', '<=', Carbon::today()->addDays(7))
                ->count(),
            'total_terbayar'    => (float) VendorTagihan::where('status', '!=', 'batal')->sum('total_terbayar'),
            'count_tercatat'    => VendorTagihan::whereNotIn('status', ['lunas', 'batal'])->count(),
        ];

        $daftarAkunBeban = AkunPerkiraan::whereIn('kepala', [1, 5, 6])
            ->where(function ($sub) {
                $sub->whereIn('kepala', [5, 6])
                    ->orWhere('kode_perkiraan', 'like', '1.00.07%'); // Persediaan Kertas Koran & Tinta
            })
            ->aktif()
            ->orderBy('kode_perkiraan')
            ->get();

        $akunUtangDefault = $this->accountingService->getMappedAccount('utang_usaha_ap');
        $akunKas = $this->accountingService->getMappedAccount('kas_operasional');
        $akunBca = $this->accountingService->getMappedAccount('bank_bca');
        $akunBjb = $this->accountingService->getMappedAccount('bank_bjb');

        $akunPembayaran = [
            $akunBca['kode'] => $akunBca['nama'],
            $akunBjb['kode'] => $akunBjb['nama'],
            $akunKas['kode'] => $akunKas['nama'],
        ];

        return view('pages.accounting.beban.hutang_vendor', compact(
            'tagihans',
            'stats',
            'daftarAkunBeban',
            'akunUtangDefault',
            'akunPembayaran',
            'q',
            'status'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_vendor'          => 'required|string|max:150',
            'no_invoice_vendor'    => 'nullable|string|max:80',
            'kategori_beban'       => 'required|string|max:80',
            'tanggal_tagihan'      => 'required|date',
            'jatuh_tempo'          => 'nullable|date',
            'kode_perkiraan_beban' => 'required|string|max:30',
            'dpp'                  => 'required|numeric|min:1',
            'ppn'                  => 'nullable|numeric|min:0',
            'pph23'                => 'nullable|numeric|min:0',
            'keterangan'           => 'nullable|string|max:255',
            'post_gl_now'          => 'nullable|boolean',
        ]);

        $periodeKey = Carbon::parse($request->tanggal_tagihan)->format('Y-m');
        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah terkunci.");
        }

        $dpp = (float) $request->dpp;
        $ppn = (float) $request->input('ppn', 0);
        $pph23 = (float) $request->input('pph23', 0);
        $totalTagihan = max(0, $dpp + $ppn - $pph23);

        $akunUtang = $this->accountingService->getMappedAccount('utang_usaha_ap');
        $akunBeban = AkunPerkiraan::where('kode_perkiraan', $request->kode_perkiraan_beban)->first();
        $akunPph23 = $this->accountingService->getMappedAccount('utang_pph23');
        $postNow = $request->boolean('post_gl_now', true);

        DB::transaction(function () use ($request, $dpp, $ppn, $pph23, $totalTagihan, $akunUtang, $akunBeban, $akunPph23, $postNow) {
            $noTagihan = VendorTagihan::generateNoTagihan($request->tanggal_tagihan);
            $jurnalId = null;

            if ($postNow) {
                $noJurnal = JurnalUmum::generateNomorJurnal($request->tanggal_tagihan);
                $bruto = $dpp + $ppn;

                $jurnal = JurnalUmum::create([
                    'no_jurnal'    => $noJurnal,
                    'tanggal'      => $request->tanggal_tagihan,
                    'tipe'         => 'auto',
                    'sumber'       => 'manual',
                    'referensi'    => $noTagihan,
                    'keterangan'   => "Tagihan Vendor {$request->nama_vendor} ({$request->kategori_beban})",
                    'total_debet'  => $bruto,
                    'total_kredit' => $bruto,
                    'is_balanced'  => true,
                    'status'       => 'posted',
                    'user_id'      => auth()->id() ?? 1,
                    'approved_by'  => auth()->id() ?? 1,
                    'approved_at'  => now(),
                ]);

                $urutan = 1;
                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => $request->kode_perkiraan_beban,
                    'nama_perkiraan' => $akunBeban?->nama_perkiraan ?? 'Beban / Persediaan Vendor',
                    'debet'          => $bruto,
                    'kredit'         => 0,
                    'urutan'         => $urutan++,
                ]);

                if ($pph23 > 0) {
                    JurnalUmumDetail::create([
                        'jurnal_umum_id' => $jurnal->id,
                        'kode_perkiraan' => $akunPph23['kode'],
                        'nama_perkiraan' => $akunPph23['nama'],
                        'debet'          => 0,
                        'kredit'         => $pph23,
                        'urutan'         => $urutan++,
                    ]);
                }

                JurnalUmumDetail::create([
                    'jurnal_umum_id' => $jurnal->id,
                    'kode_perkiraan' => $akunUtang['kode'],
                    'nama_perkiraan' => $akunUtang['nama'],
                    'debet'          => 0,
                    'kredit'         => $totalTagihan,
                    'urutan'         => $urutan++,
                ]);

                $jurnalId = $jurnal->id;
            }

            VendorTagihan::create([
                'no_tagihan'           => $noTagihan,
                'no_invoice_vendor'    => $request->no_invoice_vendor,
                'nama_vendor'          => trim($request->nama_vendor),
                'kategori_beban'       => $request->kategori_beban,
                'tanggal_tagihan'      => $request->tanggal_tagihan,
                'jatuh_tempo'          => $request->jatuh_tempo,
                'kode_perkiraan_beban' => $request->kode_perkiraan_beban,
                'kode_perkiraan_utang' => $akunUtang['kode'],
                'dpp'                  => $dpp,
                'ppn'                  => $ppn,
                'pph23'                => $pph23,
                'total_tagihan'        => $totalTagihan,
                'total_terbayar'       => 0,
                'sisa_utang'           => $totalTagihan,
                'status'               => $postNow ? 'tercatat' : 'draft',
                'keterangan'           => $request->keterangan,
                'jurnal_akui_id'       => $jurnalId,
                'user_id'              => auth()->id() ?? 1,
            ]);

            AuditLogAkuntansi::catat('CATAT_AP', 'AP_VENDOR', "Mencatat tagihan vendor {$noTagihan} ({$request->nama_vendor}) senilai Rp " . number_format($totalTagihan, 0, ',', '.'));
        });

        return redirect()->back()->with('success', 'Tagihan vendor (AP) berhasil dicatat dan dijurnal ke GL.');
    }

    public function bayar(Request $request, int $id)
    {
        $tagihan = VendorTagihan::findOrFail($id);
        if ($tagihan->status === 'lunas' || (float) $tagihan->sisa_utang <= 0) {
            return redirect()->back()->with('error', 'Tagihan vendor ini sudah berstatus LUNAS.');
        }

        $request->validate([
            'tanggal_bayar'           => 'required|date',
            'nominal_bayar'           => 'required|numeric|min:1',
            'kode_perkiraan_kas_bank' => 'required|string|max:30',
            'metode_bayar'            => 'required|string|max:30',
            'keterangan'              => 'nullable|string|max:255',
        ]);

        $nominal = (float) $request->nominal_bayar;
        if ($nominal > (float) $tagihan->sisa_utang) {
            return redirect()->back()->with('error', 'Nominal pembayaran melebihi sisa utang vendor.');
        }

        $periodeKey = Carbon::parse($request->tanggal_bayar)->format('Y-m');
        if (PeriodeAkuntansi::isPeriodeLocked($periodeKey)) {
            return redirect()->back()->with('error', "Periode {$periodeKey} sudah terkunci.");
        }

        $akunKasBank = AkunPerkiraan::where('kode_perkiraan', $request->kode_perkiraan_kas_bank)->first();
        $akunUtang = AkunPerkiraan::where('kode_perkiraan', $tagihan->kode_perkiraan_utang)->first();

        DB::transaction(function () use ($request, $tagihan, $nominal, $akunKasBank, $akunUtang) {
            $noBukti = VendorPembayaran::generateNoBukti($request->tanggal_bayar);
            $noJurnal = JurnalUmum::generateNomorJurnal($request->tanggal_bayar);

            $jurnal = JurnalUmum::create([
                'no_jurnal'    => $noJurnal,
                'tanggal'      => $request->tanggal_bayar,
                'tipe'         => 'auto',
                'sumber'       => 'manual',
                'referensi'    => $noBukti,
                'keterangan'   => "Pelunasan AP {$tagihan->no_tagihan} - {$tagihan->nama_vendor}",
                'total_debet'  => $nominal,
                'total_kredit' => $nominal,
                'is_balanced'  => true,
                'status'       => 'posted',
                'user_id'      => auth()->id() ?? 1,
                'approved_by'  => auth()->id() ?? 1,
                'approved_at'  => now(),
            ]);

            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => $tagihan->kode_perkiraan_utang,
                'nama_perkiraan' => $akunUtang?->nama_perkiraan ?? 'Utang Usaha Vendor',
                'debet'          => $nominal,
                'kredit'         => 0,
                'urutan'         => 1,
            ]);

            JurnalUmumDetail::create([
                'jurnal_umum_id' => $jurnal->id,
                'kode_perkiraan' => $request->kode_perkiraan_kas_bank,
                'nama_perkiraan' => $akunKasBank?->nama_perkiraan ?? 'Kas / Bank Pembayar',
                'debet'          => 0,
                'kredit'         => $nominal,
                'urutan'         => 2,
            ]);

            VendorPembayaran::create([
                'vendor_tagihan_id'       => $tagihan->id,
                'no_bukti_bayar'          => $noBukti,
                'tanggal_bayar'           => $request->tanggal_bayar,
                'metode_bayar'            => $request->metode_bayar,
                'kode_perkiraan_kas_bank' => $request->kode_perkiraan_kas_bank,
                'nominal_bayar'           => $nominal,
                'keterangan'              => $request->keterangan,
                'jurnal_bayar_id'         => $jurnal->id,
                'user_id'                 => auth()->id() ?? 1,
            ]);

            $terbayarBaru = (float) $tagihan->total_terbayar + $nominal;
            $sisaBaru = max(0, (float) $tagihan->total_tagihan - $terbayarBaru);
            $tagihan->update([
                'total_terbayar' => $terbayarBaru,
                'sisa_utang'     => $sisaBaru,
                'status'         => $sisaBaru <= 0 ? 'lunas' : 'dibayar_sebagian',
            ]);

            AuditLogAkuntansi::catat('BAYAR_AP', 'AP_VENDOR', "Pelunasan tagihan vendor {$tagihan->no_tagihan} ({$noBukti}) sebesar Rp " . number_format($nominal, 0, ',', '.'));
        });

        return redirect()->back()->with('success', "Pembayaran utang vendor {$tagihan->no_tagihan} berhasil diposting ke GL.");
    }
}
