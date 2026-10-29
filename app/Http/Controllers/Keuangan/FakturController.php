<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Agen;
use App\Models\Customer;
use App\Models\Faktur;
use App\Models\KasTransaksi;
use App\Models\PembayaranFaktur;
use App\Models\TransaksiKoran;
use App\Models\TransaksiOnline;
use App\Models\TransaksiPriangan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FakturController extends Controller
{
    /**
     * Sinkronisasi Otomatis Seluruh Transaksi Divisi Iklan ke Modul Faktur Keuangan.
     * Staf Faktur tidak perlu menginput manual karena transaksi dibuat langsung oleh Front Office Iklan.
     */
    protected function autoSyncFromIklan(): void
    {
        $existingNos = Faktur::pluck('no_faktur')->toArray();

        // 1. Sinkronisasi Iklan Koran
        $korans = TransaksiKoran::whereNotNull('nofakturkoran')
            ->where('nofakturkoran', '!=', '')
            ->whereNotIn('nofakturkoran', $existingNos)
            ->get();

        foreach ($korans as $k) {
            $customer = Customer::firstOrCreate(
                ['nama_customer' => $k->nama_pemasangkoran ?: 'Customer Iklan Koran'],
                [
                    'identitas' => $k->identitas,
                    'alamat'    => $k->alamat_pemasangkoran,
                    'kota'      => $k->kota ?: 'SUMEDANG',
                    'tipe'      => 'iklan',
                ]
            );

            $tagihan = (float) ($k->totaltagihan_transaksikoran ?: $k->harga_transaksikoran);
            $terbayar = (float) ($k->jumlahbayar_transaksikoran ?? 0);
            $piutang = (float) ($k->piutang_transaksikoran ?? max(0, $tagihan - $terbayar));
            $status = $piutang <= 0 ? 'lunas' : ($terbayar > 0 ? 'cicilan' : 'belum_bayar');

            $brutto = (float) ($k->brutto_transaksikoran ?: ($k->harga_transaksikoran * ($k->total_muatkoran ?: 1)));
            $diskonNominal = (float) ($k->diskon_transaksikoran ?? 0);
            $diskonPersen = (float) ($k->diskon_persen ?: ($brutto > 0 ? round(($diskonNominal / $brutto) * 100, 2) : 0));
            $netto = (float) ($k->netto_transaksikoran ?: max(0, $brutto - $diskonNominal));
            $ppn = (float) ($k->ppn_transaksikoran ?? 0);
            $materai = (float) ($k->materai_transaksikoran ?? 0);

            Faktur::create([
                'no_faktur'      => $k->nofakturkoran,
                'no_order'       => $k->no_order,
                'customer_id'    => $customer->id,
                'sales_nama'     => $k->sales_iklankoran,
                'kategori'       => 'iklan',
                'tipe_faktur'    => 'reguler',
                'tanggal_faktur' => $k->tanggal_transaksikoran ?? Carbon::today(),
                'jatuh_tempo'    => Carbon::parse($k->tanggal_transaksikoran ?? Carbon::today())->addDays(14),
                'materi_iklan'   => $k->materi_iklan,
                'brutto'         => $brutto,
                'diskon_persen'  => $diskonPersen,
                'diskon_nominal' => $diskonNominal,
                'netto'          => $netto,
                'ppn_nominal'    => $ppn,
                'materai'        => $materai,
                'rincian_biaya'  => [
                    [
                        'item'     => ($k->materi_iklan ? $k->materi_iklan . ' - ' : '') . 'Iklan Koran ' . ($k->halaman_iklan ?? '') . ' (' . ($k->ukuran_iklan ?? 'Standar') . ') - Tgl Muat: ' . ($k->tanggal_muatkoran ?? '-'),
                        'qty'      => $k->total_muatkoran ?: 1,
                        'harga'    => (float) $k->harga_transaksikoran,
                        'subtotal' => $tagihan,
                    ]
                ],
                'total_tagihan'  => $tagihan,
                'total_terbayar' => $terbayar,
                'sisa_piutang'   => $piutang,
                'status'         => $status,
                'created_by'     => 1,
                'catatan'        => 'Sinkronisasi otomatis Front Office Iklan Koran (HU. Priangan)',
            ]);
        }

        // 2. Sinkronisasi Iklan Online
        $onlines = TransaksiOnline::whereNotNull('nofakturonline')
            ->where('nofakturonline', '!=', '')
            ->whereNotIn('nofakturonline', $existingNos)
            ->get();

        foreach ($onlines as $o) {
            $customer = Customer::firstOrCreate(
                ['nama_customer' => $o->nama_pemasangonline ?: 'Customer Iklan Online'],
                [
                    'alamat' => $o->alamat_pemasangonline,
                    'tipe'   => 'iklan',
                ]
            );

            $tagihan = (float) ($o->totaltagihan_transaksionline ?: $o->harga_transaksionline);
            $terbayar = (float) ($o->jumlahbayar_transaksionline ?? 0);
            $piutang = (float) ($o->piutang_transaksionline ?? max(0, $tagihan - $terbayar));
            $status = $piutang <= 0 ? 'lunas' : ($terbayar > 0 ? 'cicilan' : 'belum_bayar');

            Faktur::create([
                'no_faktur'      => $o->nofakturonline,
                'customer_id'    => $customer->id,
                'sales_nama'     => $o->sales_iklanonline,
                'kategori'       => 'iklan',
                'tipe_faktur'    => 'reguler',
                'tanggal_faktur' => $o->tanggal_transaksionline ?? Carbon::today(),
                'jatuh_tempo'    => Carbon::parse($o->tanggal_transaksionline ?? Carbon::today())->addDays(14),
                'rincian_biaya'  => [
                    [
                        'item'     => 'Iklan Portal Online (' . ($o->portal_iklanonline ?? 'Portal Web') . ')',
                        'qty'      => $o->total_muatiklanonline ?: 1,
                        'harga'    => (float) $o->harga_transaksionline,
                        'subtotal' => $tagihan,
                    ]
                ],
                'total_tagihan'  => $tagihan,
                'total_terbayar' => $terbayar,
                'sisa_piutang'   => $piutang,
                'status'         => $status,
                'created_by'     => 1,
                'catatan'        => 'Sinkronisasi otomatis Front Office Iklan Online',
            ]);
        }

        // 3. Sinkronisasi Priangan TV
        $priangans = TransaksiPriangan::whereNotNull('nofakturpriangan')
            ->where('nofakturpriangan', '!=', '')
            ->whereNotIn('nofakturpriangan', $existingNos)
            ->get();

        foreach ($priangans as $p) {
            $customer = Customer::firstOrCreate(
                ['nama_customer' => $p->nama_pemasangpriangan ?: 'Customer Priangan TV'],
                [
                    'alamat' => $p->alamat_pemasangpriangan,
                    'tipe'   => 'iklan',
                ]
            );

            $tagihan = (float) ($p->totaltagihan_transaksipriangan ?: $p->harga_transaksipriangan);
            $terbayar = (float) ($p->jumlahbayar_transaksipriangan ?? 0);
            $piutang = (float) ($p->piutang_transaksipriangan ?? max(0, $tagihan - $terbayar));
            $status = $piutang <= 0 ? 'lunas' : ($terbayar > 0 ? 'cicilan' : 'belum_bayar');

            Faktur::create([
                'no_faktur'      => $p->nofakturpriangan,
                'customer_id'    => $customer->id,
                'sales_nama'     => $p->sales_iklanpriangan,
                'kategori'       => 'iklan',
                'tipe_faktur'    => 'reguler',
                'tanggal_faktur' => $p->tanggal_transaksipriangan ?? Carbon::today(),
                'jatuh_tempo'    => Carbon::parse($p->tanggal_transaksipriangan ?? Carbon::today())->addDays(14),
                'rincian_biaya'  => [
                    [
                        'item'     => 'Iklan Siaran Priangan TV',
                        'qty'      => $p->total_muatiklanpriangan ?: 1,
                        'harga'    => (float) $p->harga_transaksipriangan,
                        'subtotal' => $tagihan,
                    ]
                ],
                'total_tagihan'  => $tagihan,
                'total_terbayar' => $terbayar,
                'sisa_piutang'   => $piutang,
                'status'         => $status,
                'created_by'     => 1,
                'catatan'        => 'Sinkronisasi otomatis Front Office Priangan TV',
            ]);
        }
    }

    /**
     * Dashboard & Pemantauan Faktur Iklan (Sesuai Struktur Menu DOS faktur1.exe).
     * Modul ini murni untuk pemantauan data transaksi iklan dan penugasan piutang ke Inkaso.
     */
    public function index(Request $request)
    {
        // Jalankan auto-sync agar seluruh data iklan front office selalu up-to-date
        $this->autoSyncFromIklan();

        $allowedTabs = [
            'keseluruhan',
            'susulan',
            'rekap_tanggal',
            'rekap_bulan',
            'tunai',
            'piutang',
            'stopper_bonus',
            'stock_opname',
        ];

        $tab = $request->get('tab', 'keseluruhan');
        if (!in_array($tab, $allowedTabs)) {
            $tab = 'keseluruhan';
        }

        $now = Carbon::now();
        $tahun = (int) $request->get('tahun', $now->year);
        $bulan = (int) $request->get('bulan', $now->month);
        $statusSubmenu = $request->get('status', 'keseluruhan'); // 'lunas', 'belum_lunas', 'keseluruhan'

        $dariTanggal = $request->get('dari_tanggal', $now->copy()->startOfMonth()->toDateString());
        $sampaiTanggal = $request->get('sampai_tanggal', $now->copy()->endOfMonth()->toDateString());

        // Base Query Khusus Divisi Iklan
        $query = Faktur::with(['customer', 'creator', 'penugasanAktif.petugas'])
            ->where('kategori', 'iklan');

        // Pencarian teks global
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('no_faktur', 'like', "%{$q}%")
                    ->orWhere('sales_nama', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('nama_customer', 'like', "%{$q}%")
                          ->orWhere('alamat', 'like', "%{$q}%");
                    });
            });
        }

        // =====================================================================
        // FILTER BERDASARKAN TAB MENU SESUAI FOTO DOS APLIKASI
        // =====================================================================

        // 1. FAKTUR IKLAN KESELURUHAN
        if ($tab === 'keseluruhan') {
            if ($statusSubmenu === 'lunas') {
                $query->where('status', 'lunas');
            } elseif ($statusSubmenu === 'belum_lunas') {
                $query->where('status', '!=', 'lunas');
            }
        }

        // 2. FAKTUR IKLAN SUSULAN
        elseif ($tab === 'susulan') {
            $query->where('tipe_faktur', 'susulan');
            if ($statusSubmenu === 'lunas') {
                $query->where('status', 'lunas');
            } elseif ($statusSubmenu === 'belum_lunas') {
                $query->where('status', '!=', 'lunas');
            }
        }

        // 3. REKAP FAKTUR IKLAN / TANGGAL
        elseif ($tab === 'rekap_tanggal') {
            $query->whereBetween('tanggal_faktur', [$dariTanggal, $sampaiTanggal]);
            if ($statusSubmenu === 'lunas') {
                $query->where('status', 'lunas');
            } elseif ($statusSubmenu === 'belum_lunas') {
                $query->where('status', '!=', 'lunas');
            }
        }

        // 4. REKAP FAKTUR IKLAN / BULAN
        elseif ($tab === 'rekap_bulan') {
            $query->whereYear('tanggal_faktur', $tahun)
                  ->whereMonth('tanggal_faktur', $bulan);
            if ($statusSubmenu === 'lunas') {
                $query->where('status', 'lunas');
            } elseif ($statusSubmenu === 'belum_lunas') {
                $query->where('status', '!=', 'lunas');
            }
        }

        // 5. REKAP FAKTUR IKLAN TUNAI / BULAN
        elseif ($tab === 'tunai') {
            $query->where('status', 'lunas');
            if ($request->filled('bulan')) {
                $query->whereYear('tanggal_faktur', $tahun)->whereMonth('tanggal_faktur', $bulan);
            }
        }

        // 6. REKAP FAKTUR IKLAN PIUTANG / BULAN (Aging & Penugasan ke Inkaso)
        elseif ($tab === 'piutang') {
            $query->where('sisa_piutang', '>', 0)->where('status', '!=', 'lunas');

            if ($request->filled('status_inkaso')) {
                if ($request->status_inkaso === 'sudah') {
                    $query->whereHas('penugasanAktif');
                } elseif ($request->status_inkaso === 'belum') {
                    $query->whereDoesntHave('penugasanAktif');
                }
            }

            // Filter Umur Piutang (Aging)
            $aging = $request->get('aging');
            if ($aging === '0-30') {
                $query->whereRaw('DATEDIFF(CURRENT_DATE, tanggal_faktur) BETWEEN 0 AND 30');
            } elseif ($aging === '31-60') {
                $query->whereRaw('DATEDIFF(CURRENT_DATE, tanggal_faktur) BETWEEN 31 AND 60');
            } elseif ($aging === '61-90') {
                $query->whereRaw('DATEDIFF(CURRENT_DATE, tanggal_faktur) BETWEEN 61 AND 90');
            } elseif ($aging === '>90') {
                $query->whereRaw('DATEDIFF(CURRENT_DATE, tanggal_faktur) > 90');
            }
        }

        // 7. REKAP STOPPER / BONUS / BULAN
        elseif ($tab === 'stopper_bonus') {
            $query->whereIn('tipe_faktur', ['stopper', 'bonus']);
            if ($request->filled('tipe_faktur')) {
                $query->where('tipe_faktur', $request->tipe_faktur);
            }
        }

        // 8. STOCK OPNAME FAKTUR IKLAN
        elseif ($tab === 'stock_opname') {
            $query->whereYear('tanggal_faktur', $tahun);
            if ($request->filled('bulan')) {
                $query->whereMonth('tanggal_faktur', $bulan);
            }
        }

        // Eksekusi data faktur
        $fakturs = $query->latest('tanggal_faktur')->latest('id')->paginate(15)->withQueryString();

        // Metrik Ringkas KPI Iklan
        $baseIklan = Faktur::where('kategori', 'iklan');
        $totalFakturCount   = (clone $baseIklan)->count();
        $totalTagihanSum    = (float) (clone $baseIklan)->sum('total_tagihan');
        $totalTerbayarSum   = (float) (clone $baseIklan)->sum('total_terbayar');
        $totalPiutangSum    = (float) (clone $baseIklan)->sum('sisa_piutang');
        $fakturLunasCount   = (clone $baseIklan)->where('status', 'lunas')->count();
        $fakturPiutangCount = (clone $baseIklan)->where('status', '!=', 'lunas')->count();

        // Daftar AE / Sales
        $aeList = Faktur::where('kategori', 'iklan')
            ->whereNotNull('sales_nama')
            ->where('sales_nama', '!=', '')
            ->distinct()
            ->pluck('sales_nama')
            ->toArray();

        return view('pages.keuangan.faktur.index', compact(
            'tab',
            'fakturs',
            'totalFakturCount',
            'totalTagihanSum',
            'totalTerbayarSum',
            'totalPiutangSum',
            'fakturLunasCount',
            'fakturPiutangCount',
            'aeList',
            'tahun',
            'bulan',
            'statusSubmenu',
            'dariTanggal',
            'sampaiTanggal'
        ));
    }

    /**
     * Form Penerbitan Faktur Baru (Iklan & Sirkulasi).
     */
    public function create(?Request $request = null)
    {
        $request = $request ?? request();
        $kategoriDefault   = $request->get('kategori', 'iklan');
        $tipeFakturDefault = $request->get('tipe_faktur', 'reguler');
        $customers         = Customer::orderBy('nama_customer')->get();

        $period = date('Ym');
        if ($kategoriDefault === 'sirkulasi') {
            if ($tipeFakturDefault === 'susulan') {
                $prefix = 'INV-SRK-SUS-';
            } elseif ($tipeFakturDefault === 'insidentil') {
                $prefix = 'INV-SRK-INS-';
            } else {
                $prefix = 'INV-SRK-';
            }
        } else {
            if ($tipeFakturDefault === 'susulan') {
                $prefix = 'INV-IKL-SUS-';
            } elseif ($tipeFakturDefault === 'stopper') {
                $prefix = 'INV-IKL-STP-';
            } elseif ($tipeFakturDefault === 'bonus') {
                $prefix = 'INV-IKL-BNS-';
            } else {
                $prefix = 'INV-IKL-';
            }
        }

        $count = Faktur::where('no_faktur', 'like', "{$prefix}{$period}-%")->count() + 1;
        $noFakturSuggested = $prefix . $period . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        // Suggested numbers for different types
        $countIklReg = Faktur::where('no_faktur', 'like', "INV-IKL-{$period}-%")->count() + 1;
        $suggestedIklReguler = "INV-IKL-{$period}-" . str_pad($countIklReg, 3, '0', STR_PAD_LEFT);

        $countIklSus = Faktur::where('no_faktur', 'like', "INV-IKL-SUS-{$period}-%")->count() + 1;
        $suggestedIklSusulan = "INV-IKL-SUS-{$period}-" . str_pad($countIklSus, 3, '0', STR_PAD_LEFT);

        $countIklStp = Faktur::where('no_faktur', 'like', "INV-IKL-STP-{$period}-%")->count() + 1;
        $suggestedIklStopper = "INV-IKL-STP-{$period}-" . str_pad($countIklStp, 3, '0', STR_PAD_LEFT);

        $countIklBns = Faktur::where('no_faktur', 'like', "INV-IKL-BNS-{$period}-%")->count() + 1;
        $suggestedIklBonus = "INV-IKL-BNS-{$period}-" . str_pad($countIklBns, 3, '0', STR_PAD_LEFT);

        $countSrkReg = Faktur::where('no_faktur', 'like', "INV-SRK-{$period}-%")->count() + 1;
        $suggestedSrkReguler = "INV-SRK-{$period}-" . str_pad($countSrkReg, 3, '0', STR_PAD_LEFT);

        $countSrkSus = Faktur::where('no_faktur', 'like', "INV-SRK-SUS-{$period}-%")->count() + 1;
        $suggestedSrkSusulan = "INV-SRK-SUS-{$period}-" . str_pad($countSrkSus, 3, '0', STR_PAD_LEFT);

        $countSrkIns = Faktur::where('no_faktur', 'like', "INV-SRK-INS-{$period}-%")->count() + 1;
        $suggestedSrkInsidentil = "INV-SRK-INS-{$period}-" . str_pad($countSrkIns, 3, '0', STR_PAD_LEFT);

        // Ambil daftar AE
        $aeList = User::whereIn('role', ['iklan', 'petugas_inkaso'])->pluck('name')
            ->merge(Faktur::whereNotNull('sales_nama')->pluck('sales_nama'))
            ->merge(TransaksiKoran::pluck('sales_iklankoran'))
            ->merge(TransaksiOnline::pluck('sales_iklanonline'))
            ->merge(TransaksiPriangan::pluck('sales_iklanpriangan'))
            ->filter()->unique()->values()->toArray();

        // Ambil daftar Faktur Iklan untuk autocomplete / bantuan pilihan
        $fakturKoran = TransaksiKoran::latest('id')->take(20)->get()->map(function($t) {
            return [
                'no_faktur' => $t->nofakturkoran,
                'media'     => 'Koran',
                'pemasang'  => $t->nama_pemasangkoran,
                'sales'     => $t->sales_iklankoran,
                'tagihan'   => (float) $t->totaltagihan_transaksikoran,
                'piutang'   => (float) $t->piutang_transaksikoran,
                'tanggal'   => $t->tanggal_transaksikoran,
            ];
        });
        $fakturOnline = TransaksiOnline::latest('id')->take(20)->get()->map(function($t) {
            return [
                'no_faktur' => $t->nofakturonline,
                'media'     => 'Online',
                'pemasang'  => $t->nama_pemasangonline,
                'sales'     => $t->sales_iklanonline,
                'tagihan'   => (float) $t->totaltagihan_transaksionline,
                'piutang'   => (float) $t->piutang_transaksionline,
                'tanggal'   => $t->tanggal_transaksionline,
            ];
        });
        $fakturPriangan = TransaksiPriangan::latest('id')->take(20)->get()->map(function($t) {
            return [
                'no_faktur' => $t->nofakturpriangan,
                'media'     => 'Priangan TV',
                'pemasang'  => $t->nama_pemasangpriangan,
                'sales'     => $t->sales_iklanpriangan,
                'tagihan'   => (float) $t->totaltagihan_transaksipriangan,
                'piutang'   => (float) $t->piutang_transaksipriangan,
                'tanggal'   => $t->tanggal_transaksipriangan,
            ];
        });
        $fakturIklanList = $fakturKoran->merge($fakturOnline)->merge($fakturPriangan)->sortByDesc('tanggal')->values();

        return view('pages.keuangan.faktur.create', compact(
            'customers',
            'kategoriDefault',
            'tipeFakturDefault',
            'noFakturSuggested',
            'suggestedIklReguler',
            'suggestedIklSusulan',
            'suggestedIklStopper',
            'suggestedIklBonus',
            'suggestedSrkReguler',
            'suggestedSrkSusulan',
            'suggestedSrkInsidentil',
            'aeList',
            'fakturIklanList'
        ));
    }

    /**
     * Simpan Penerbitan Faktur Baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_faktur'        => 'required|string|unique:faktur,no_faktur',
            'customer_id'      => 'required|exists:customers,id',
            'kategori'         => 'required|in:iklan,sirkulasi',
            'tipe_faktur'      => 'nullable|in:reguler,susulan,insidentil,stopper,bonus',
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
            $tipeFaktur = $request->input('tipe_faktur', 'reguler');
            if ($request->kategori === 'sirkulasi') {
                if (!in_array($tipeFaktur, ['reguler', 'susulan', 'insidentil'])) {
                    $tipeFaktur = 'reguler';
                }
            } else {
                if (!in_array($tipeFaktur, ['reguler', 'susulan', 'stopper', 'bonus'])) {
                    $tipeFaktur = 'reguler';
                }
            }

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

            $userId = Auth::id() ?? 1;
            $customer = Customer::findOrFail($request->customer_id);

            // 2. Evaluasi Logika Pembayaran
            if ($request->tipe_pembayaran === 'lunas_langsung') {
                $totalTerbayar = $totalTagihan;
                $sisaPiutang   = 0.0;
                $status        = 'lunas';

                $faktur = Faktur::create([
                    'no_faktur'      => $request->no_faktur,
                    'customer_id'    => $customer->id,
                    'sales_nama'     => $request->sales_nama,
                    'kategori'       => $request->kategori,
                    'tipe_faktur'    => $tipeFaktur,
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

                $pesan = 'Faktur ' . $faktur->no_faktur . ' berhasil diterbitkan dan LUNAS.';
            } else {
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
                    'sales_nama'     => $request->sales_nama,
                    'kategori'       => $request->kategori,
                    'tipe_faktur'    => $tipeFaktur,
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

                $pesan = 'Faktur ' . $faktur->no_faktur . ' berhasil diterbitkan dengan sisa piutang Rp ' . number_format($sisaPiutang, 0, ',', '.') . '.';
            }

            return redirect()->route('keuangan.faktur.show', $faktur->id)->with('success', $pesan);
        });
    }

    /**
     * Tampilan Detail Rincian Faktur Iklan & Riwayat Pembayaran.
     */
    public function show($id)
    {
        $faktur = Faktur::with([
            'customer',
            'creator',
            'pembayaran.penerima',
        ])->findOrFail($id);

        return view('pages.keuangan.faktur.show', compact('faktur'));
    }

    /**
     * Catat Pembayaran / Pelunasan Faktur (Iklan maupun Sirkulasi).
     */
    public function catatPembayaran(Request $request, $id)
    {
        $request->validate([
            'nominal_bayar'   => 'required|numeric|min:1',
            'tanggal_bayar'   => 'required|date',
            'metode_bayar'    => 'required|in:tunai,transfer',
            'catatan'         => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $faktur = Faktur::lockForUpdate()->findOrFail($id);

            $nominal = (float) $request->nominal_bayar;
            if ($nominal > (float) $faktur->sisa_piutang) {
                return back()->with('error', 'Nominal bayar (Rp ' . number_format($nominal, 0, ',', '.') . ') melebihi sisa piutang faktur (Rp ' . number_format($faktur->sisa_piutang, 0, ',', '.') . ').');
            }

            $userId = Auth::id() ?? 1;

            // 1. Generate Nomor Kwitansi Resmi Format KW-YYYYMM-XXXX
            $period = date('Ym');
            $prefixKwitansi = 'KW-' . $period . '-';
            $nextSeq = PembayaranFaktur::where('no_kwitansi', 'like', "{$prefixKwitansi}%")->count() + 1;
            do {
                $kwitansiNo = $prefixKwitansi . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
                $nextSeq++;
            } while (PembayaranFaktur::where('no_kwitansi', $kwitansiNo)->exists());

            $isLunas = ($nominal >= (float) $faktur->sisa_piutang);

            // 2. Simpan Riwayat Pembayaran Faktur
            $bayar = PembayaranFaktur::create([
                'no_kwitansi'   => $kwitansiNo,
                'faktur_id'     => $faktur->id,
                'tanggal_bayar' => $request->tanggal_bayar,
                'nominal_bayar' => $nominal,
                'metode_bayar'  => $request->metode_bayar,
                'tipe_bayar'    => $isLunas ? 'lunas_langsung' : 'cicilan_inkaso',
                'diterima_oleh' => $userId,
                'catatan'       => $request->catatan ?? ($isLunas ? 'Pelunasan Faktur ' . $faktur->no_faktur : 'Cicilan Tagihan Faktur ' . $faktur->no_faktur),
            ]);

            // 3. Update Faktur
            $faktur->total_terbayar = (float) $faktur->total_terbayar + $nominal;
            $faktur->sisa_piutang   = max(0, (float) $faktur->sisa_piutang - $nominal);
            if ($faktur->sisa_piutang <= 0) {
                $faktur->status = 'lunas';
                $faktur->status_bayar = 'lunas';
            } else {
                $faktur->status = 'cicilan';
                $faktur->status_bayar = 'sebagian';
            }
            $faktur->save();

            // 4. Jika Sirkulasi, sesuaikan Saldo Piutang Agen jika terdaftar
            if ($faktur->kategori === 'sirkulasi' && $faktur->customer) {
                $agen = Agen::where('nama_agen', 'like', '%' . $faktur->customer->nama_customer . '%')
                    ->orWhere('kode_agen', $faktur->customer->identitas)
                    ->first();
                if ($agen) {
                    $agen->saldo_piutang = max(0, (float) $agen->saldo_piutang - $nominal);
                    $agen->save();
                }
            }

            // 5. Catat Mutasi Kas Masuk ke Buku Kas (Treasury)
            $kategoriKas = 'Pelunasan Faktur ' . ucfirst($faktur->kategori);
            KasTransaksi::catatMutasi(
                'masuk',
                $kategoriKas,
                $nominal,
                'Pembayaran Faktur ' . $faktur->no_faktur . ' (' . ($faktur->customer->nama_customer ?? 'Umum') . ') - Kwitansi ' . $kwitansiNo,
                $bayar,
                $userId,
                $bayar->tanggal_bayar->toDateString()
            );

            return back()->with('success', "Pembayaran Faktur {$faktur->no_faktur} sebesar Rp " . number_format($nominal, 0, ',', '.') . " berhasil dicatat dengan Kwitansi {$kwitansiNo}! Status faktur sekarang: " . strtoupper($faktur->status));
        });
    }

    /**
     * Cetak Lembar Order Transaksi Iklan (Sesuai Foto Slip Dokumen Dot-Matrix).
     */
    public function cetak($id)
    {
        $faktur = Faktur::with(['customer', 'creator', 'pembayaran'])->findOrFail($id);

        // Ambil data detail transaksi divisi jika ada
        $txKoran = TransaksiKoran::with('iklankoran')->where('nofakturkoran', $faktur->no_faktur)->first();
        $txOnline = TransaksiOnline::with('iklanonline')->where('nofakturonline', $faktur->no_faktur)->first();
        $txPriangan = TransaksiPriangan::with('iklanpriangan')->where('nofakturpriangan', $faktur->no_faktur)->first();

        return view('pages.keuangan.faktur.cetak', compact('faktur', 'txKoran', 'txOnline', 'txPriangan'));
    }

    /**
     * Cetak Surat Penugasan Penagihan Lapangan (Inkaso) untuk Faktur Piutang.
     * Petugas lapangan cukup memegang surat tugas fisik ini tanpa perlu akun/database inkaso.
     */
    public function cetakSuratTugas(Request $request, $id)
    {
        $faktur = Faktur::with(['customer', 'creator'])->findOrFail($id);
        $namaPetugas = trim($request->input('petugas', ''));

        return view('pages.keuangan.faktur.cetak-surat-tugas', compact('faktur', 'namaPetugas'));
    }

    /**
     * Cetak Laporan Rekap Pemutan Iklan Standar Dot-Matrix (Sesuai Foto Printout).
     */
    public function cetakRekap(Request $request)
    {
        $now = Carbon::now();
        $mode = $request->get('mode', 'tanggal'); // 'tanggal' atau 'bulan'
        $tahun = (int) $request->get('tahun', $now->year);
        $bulan = (int) $request->get('bulan', $now->month);
        $status = $request->get('status', 'keseluruhan');
        $dariTanggal = $request->get('dari_tanggal', $now->toDateString());
        $sampaiTanggal = $request->get('sampai_tanggal', $now->toDateString());

        $query = Faktur::with('customer')->where('kategori', 'iklan');

        if ($mode === 'tanggal') {
            $query->whereBetween('tanggal_faktur', [$dariTanggal, $sampaiTanggal]);
            $periodeJudul = Carbon::parse($dariTanggal)->translatedFormat('l / d-m-Y');
            if ($dariTanggal !== $sampaiTanggal) {
                $periodeJudul = Carbon::parse($dariTanggal)->format('d/m/Y') . ' s/d ' . Carbon::parse($sampaiTanggal)->format('d/m/Y');
            }
        } else {
            $query->whereYear('tanggal_faktur', $tahun)->whereMonth('tanggal_faktur', $bulan);
            $namaBulan = Carbon::create()->month($bulan)->translatedFormat('F');
            $periodeJudul = "BULAN {$namaBulan} {$tahun}";
        }

        if ($status === 'lunas') {
            $query->where('status', 'lunas');
            $statusJudul = 'LUNAS';
        } elseif ($status === 'belum_lunas') {
            $query->where('status', '!=', 'lunas');
            $statusJudul = 'BELUM LUNAS (PIUTANG)';
        } else {
            $statusJudul = 'KESELURUHAN';
        }

        $fakturs = $query->with(['customer', 'pembayaran'])->orderBy('tanggal_faktur')->get();

        $totalBrutto   = 0;
        $totalReduksi  = 0;
        $totalNetto    = 0;
        $totalPpn      = 0;
        $totalMaterai  = 0;
        $totalBayar    = (float) $fakturs->sum('total_terbayar');
        $totalKoreksi  = 0;
        $totalSisa     = (float) $fakturs->sum('sisa_piutang');
        $totalTagihan  = (float) $fakturs->sum('total_tagihan');

        foreach ($fakturs as $f) {
            $txK = $f->transaksiKoran;
            $brutto = (float) ($f->brutto > 0 ? $f->brutto : ($txK?->brutto_transaksikoran ?? $f->total_tagihan));
            $reduksi = (float) ($f->diskon_nominal > 0 ? $f->diskon_nominal : ($txK?->diskon_transaksikoran ?? 0));
            $netto = (float) ($f->netto > 0 ? $f->netto : ($txK?->netto_transaksikoran ?? max(0, $brutto - $reduksi)));
            $ppn = (float) ($f->ppn_nominal > 0 ? $f->ppn_nominal : ($txK?->ppn_transaksikoran ?? 0));
            $materai = (float) ($f->materai > 0 ? $f->materai : ($txK?->materai_transaksikoran ?? 0));

            $tglMuat = $txK?->tanggal_muatkoran;
            $f->calc_tgl_muat = $tglMuat ? Carbon::parse($tglMuat)->format('d-m-Y') : ($f->tanggal_faktur ? $f->tanggal_faktur->format('d-m-Y') : date('d-m-Y'));
            $f->calc_kwitansi = $f->pembayaran?->first()?->no_kwitansi ?? ('KW' . str_pad($f->id, 5, '0', STR_PAD_LEFT));
            $f->calc_no_perkiraan = $txK?->identitas ?? ($f->customer?->identitas ?? ('1.00.04.01.01.01.' . str_pad($f->customer_id ?? 1, 5, '0', STR_PAD_LEFT)));
            $f->calc_brutto = $brutto;
            $f->calc_reduksi = $reduksi;
            $f->calc_netto = $netto;
            $f->calc_ppn = $ppn;
            $f->calc_materai = $materai;

            $totalBrutto += $brutto;
            $totalReduksi += $reduksi;
            $totalNetto += $netto;
            $totalPpn += $ppn;
            $totalMaterai += $materai;
        }

        return view('pages.keuangan.faktur.cetak-rekap', compact(
            'fakturs',
            'periodeJudul',
            'statusJudul',
            'totalBrutto',
            'totalReduksi',
            'totalNetto',
            'totalPpn',
            'totalMaterai',
            'totalBayar',
            'totalKoreksi',
            'totalSisa',
            'totalTagihan'
        ));
    }

    /**
     * API Pencarian Data Transaksi Divisi Iklan (Kompatibilitas).
     */
    public function cariFakturIklan(Request $request)
    {
        $noFaktur = trim($request->get('no_faktur', ''));
        $faktur = Faktur::with('customer')->where('no_faktur', $noFaktur)->first();
        if ($faktur) {
            return response()->json(['success' => true, 'data' => $faktur]);
        }
        return response()->json(['success' => false, 'message' => 'Faktur tidak ditemukan.'], 404);
    }

    // =========================================================================
    // SUBMODUL FAKTUR SIRKULASI (SESUAI MENU DOS faktur1.exe > SIRKULASI)
    // 1. FAKTUR SIRKULASI KESELURUHAN
    // 2. FAKTUR SIRKULASI SUSULAN
    // 3. REKAP FAKTUR SIRKULASI
    // 4. FAKTUR INSIDENTIL
    // =========================================================================

    /**
     * 1. FAKTUR SIRKULASI KESELURUHAN (Sesuai Menu DOS faktur1.exe)
     */
    public function sirkulasiIndex(Request $request)
    {
        $query = Faktur::with(['customer', 'creator', 'penugasanAktif.petugas'])
            ->where('kategori', 'sirkulasi');

        // Filter Status Pembayaran
        if ($request->filled('status') && in_array($request->status, ['belum_bayar', 'cicilan', 'lunas'])) {
            $query->where('status', $request->status);
        }

        // Filter Tipe Faktur
        if ($request->filled('tipe_faktur') && in_array($request->tipe_faktur, ['reguler', 'susulan', 'insidentil'])) {
            $query->where('tipe_faktur', $request->tipe_faktur);
        }

        // Filter Rentang Tanggal
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_faktur', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_faktur', '<=', $request->sampai_tanggal);
        }

        // Pencarian No. Faktur atau Nama Agen/Loper
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_faktur', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('nama_customer', 'like', "%{$q}%");
                    });
            });
        }

        $fakturs = $query->latest('tanggal_faktur')->latest('id')->paginate(12)->withQueryString();

        $baseSirkulasi = Faktur::where('kategori', 'sirkulasi');
        $totalSirkulasiCount    = (clone $baseSirkulasi)->count();
        $totalSirkulasiTagihan  = (float) (clone $baseSirkulasi)->sum('total_tagihan');
        $totalSirkulasiTerbayar = (float) (clone $baseSirkulasi)->sum('total_terbayar');
        $totalSirkulasiPiutang  = (float) (clone $baseSirkulasi)->sum('sisa_piutang');
        $lunasCount             = (clone $baseSirkulasi)->where('status', 'lunas')->count();
        $piutangCount           = (clone $baseSirkulasi)->where('status', '!=', 'lunas')->count();
        $susulanCount           = (clone $baseSirkulasi)->where('tipe_faktur', 'susulan')->count();
        $insidentilCount        = (clone $baseSirkulasi)->where('tipe_faktur', 'insidentil')->count();

        return view('pages.keuangan.faktur.sirkulasi.index', compact(
            'fakturs',
            'totalSirkulasiCount',
            'totalSirkulasiTagihan',
            'totalSirkulasiTerbayar',
            'totalSirkulasiPiutang',
            'lunasCount',
            'piutangCount',
            'susulanCount',
            'insidentilCount'
        ));
    }

    /**
     * 2. FAKTUR SIRKULASI SUSULAN (Oplah Tambahan / Kiriman Susulan di Luar Jatah Rutin)
     */
    public function sirkulasiSusulan(Request $request)
    {
        $query = Faktur::with(['customer', 'creator', 'penugasanAktif.petugas'])
            ->where('kategori', 'sirkulasi')
            ->where('tipe_faktur', 'susulan');

        if ($request->filled('status') && in_array($request->status, ['belum_bayar', 'cicilan', 'lunas'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_faktur', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_faktur', '<=', $request->sampai_tanggal);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_faktur', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('nama_customer', 'like', "%{$q}%");
                    });
            });
        }

        $fakturs = $query->latest('tanggal_faktur')->latest('id')->paginate(12)->withQueryString();

        $baseSusulan = Faktur::where('kategori', 'sirkulasi')->where('tipe_faktur', 'susulan');
        $totalSusulanCount    = (clone $baseSusulan)->count();
        $totalSusulanTagihan  = (float) (clone $baseSusulan)->sum('total_tagihan');
        $totalSusulanTerbayar = (float) (clone $baseSusulan)->sum('total_terbayar');
        $totalSusulanPiutang  = (float) (clone $baseSusulan)->sum('sisa_piutang');

        return view('pages.keuangan.faktur.sirkulasi.susulan', compact(
            'fakturs',
            'totalSusulanCount',
            'totalSusulanTagihan',
            'totalSusulanTerbayar',
            'totalSusulanPiutang'
        ));
    }

    /**
     * 3. REKAP FAKTUR SIRKULASI (per Tanggal & per Bulan)
     */
    public function sirkulasiRekap(Request $request)
    {
        $mode = $request->get('mode', 'tanggal'); // 'tanggal' atau 'bulan'
        $now = Carbon::now();

        if ($mode === 'bulan') {
            $tahun = (int) $request->get('tahun', $now->year);

            $rekapData = Faktur::selectRaw("
                MONTH(tanggal_faktur) as bulan,
                COUNT(*) as total_faktur,
                SUM(total_tagihan) as total_tagihan,
                SUM(total_terbayar) as total_terbayar,
                SUM(sisa_piutang) as total_piutang,
                SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as jumlah_lunas,
                SUM(CASE WHEN status != 'lunas' THEN 1 ELSE 0 END) as jumlah_piutang,
                SUM(CASE WHEN tipe_faktur = 'susulan' THEN 1 ELSE 0 END) as total_susulan,
                SUM(CASE WHEN tipe_faktur = 'insidentil' THEN 1 ELSE 0 END) as total_insidentil
            ")
            ->where('kategori', 'sirkulasi')
            ->whereYear('tanggal_faktur', $tahun)
            ->groupBy(DB::raw("MONTH(tanggal_faktur)"))
            ->orderBy('bulan')
            ->get();

            $grandTagihan  = (float) $rekapData->sum('total_tagihan');
            $grandTerbayar = (float) $rekapData->sum('total_terbayar');
            $grandPiutang  = (float) $rekapData->sum('total_piutang');
            $grandFaktur   = (int) $rekapData->sum('total_faktur');

            return view('pages.keuangan.faktur.sirkulasi.rekap', compact(
                'mode',
                'tahun',
                'rekapData',
                'grandTagihan',
                'grandTerbayar',
                'grandPiutang',
                'grandFaktur'
            ));
        }

        // Mode Tanggal (Harian)
        $dariTanggal   = $request->get('dari_tanggal', $now->copy()->startOfMonth()->toDateString());
        $sampaiTanggal = $request->get('sampai_tanggal', $now->copy()->endOfMonth()->toDateString());

        $rekapData = Faktur::selectRaw("
            tanggal_faktur,
            COUNT(*) as total_faktur,
            SUM(total_tagihan) as total_tagihan,
            SUM(total_terbayar) as total_terbayar,
            SUM(sisa_piutang) as total_piutang,
            SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as jumlah_lunas,
            SUM(CASE WHEN status != 'lunas' THEN 1 ELSE 0 END) as jumlah_piutang,
            SUM(CASE WHEN tipe_faktur = 'susulan' THEN 1 ELSE 0 END) as total_susulan,
            SUM(CASE WHEN tipe_faktur = 'insidentil' THEN 1 ELSE 0 END) as total_insidentil
        ")
        ->where('kategori', 'sirkulasi')
        ->whereBetween('tanggal_faktur', [$dariTanggal, $sampaiTanggal])
        ->groupBy('tanggal_faktur')
        ->orderByDesc('tanggal_faktur')
        ->get();

        $grandTagihan  = (float) $rekapData->sum('total_tagihan');
        $grandTerbayar = (float) $rekapData->sum('total_terbayar');
        $grandPiutang  = (float) $rekapData->sum('total_piutang');
        $grandFaktur   = (int) $rekapData->sum('total_faktur');

        return view('pages.keuangan.faktur.sirkulasi.rekap', compact(
            'mode',
            'dariTanggal',
            'sampaiTanggal',
            'rekapData',
            'grandTagihan',
            'grandTerbayar',
            'grandPiutang',
            'grandFaktur'
        ));
    }

    /**
     * Cetak Lembar Rekapitulasi Faktur Sirkulasi.
     */
    public function cetakRekapSirkulasi(Request $request)
    {
        $mode = $request->get('mode', 'tanggal');
        $now = Carbon::now();

        if ($mode === 'bulan') {
            $tahun = (int) $request->get('tahun', $now->year);
            $rekapData = Faktur::selectRaw("
                MONTH(tanggal_faktur) as bulan,
                COUNT(*) as total_faktur,
                SUM(total_tagihan) as total_tagihan,
                SUM(total_terbayar) as total_terbayar,
                SUM(sisa_piutang) as total_piutang,
                SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as jumlah_lunas,
                SUM(CASE WHEN status != 'lunas' THEN 1 ELSE 0 END) as jumlah_piutang,
                SUM(CASE WHEN tipe_faktur = 'susulan' THEN 1 ELSE 0 END) as total_susulan,
                SUM(CASE WHEN tipe_faktur = 'insidentil' THEN 1 ELSE 0 END) as total_insidentil
            ")
            ->where('kategori', 'sirkulasi')
            ->whereYear('tanggal_faktur', $tahun)
            ->groupBy(DB::raw("MONTH(tanggal_faktur)"))
            ->orderBy('bulan')
            ->get();

            $periodeJudul = "Tahun {$tahun}";
        } else {
            $dariTanggal   = $request->get('dari_tanggal', $now->copy()->startOfMonth()->toDateString());
            $sampaiTanggal = $request->get('sampai_tanggal', $now->copy()->endOfMonth()->toDateString());

            $rekapData = Faktur::selectRaw("
                tanggal_faktur,
                COUNT(*) as total_faktur,
                SUM(total_tagihan) as total_tagihan,
                SUM(total_terbayar) as total_terbayar,
                SUM(sisa_piutang) as total_piutang,
                SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as jumlah_lunas,
                SUM(CASE WHEN status != 'lunas' THEN 1 ELSE 0 END) as jumlah_piutang,
                SUM(CASE WHEN tipe_faktur = 'susulan' THEN 1 ELSE 0 END) as total_susulan,
                SUM(CASE WHEN tipe_faktur = 'insidentil' THEN 1 ELSE 0 END) as total_insidentil
            ")
            ->where('kategori', 'sirkulasi')
            ->whereBetween('tanggal_faktur', [$dariTanggal, $sampaiTanggal])
            ->groupBy('tanggal_faktur')
            ->orderBy('tanggal_faktur')
            ->get();

            $periodeJudul = Carbon::parse($dariTanggal)->translatedFormat('d F Y') . ' s/d ' . Carbon::parse($sampaiTanggal)->translatedFormat('d F Y');
        }

        $grandTagihan  = (float) $rekapData->sum('total_tagihan');
        $grandTerbayar = (float) $rekapData->sum('total_terbayar');
        $grandPiutang  = (float) $rekapData->sum('total_piutang');
        $grandFaktur   = (int) $rekapData->sum('total_faktur');

        return view('pages.keuangan.faktur.sirkulasi.cetak-rekap', compact(
            'mode',
            'periodeJudul',
            'rekapData',
            'grandTagihan',
            'grandTerbayar',
            'grandPiutang',
            'grandFaktur'
        ));
    }

    /**
     * 4. FAKTUR SIRKULASI INSIDENTIL (Eceran Langsung / Pembelian Borongan Acara / Event / Dinas)
     */
    public function sirkulasiInsidentil(Request $request)
    {
        $query = Faktur::with(['customer', 'creator', 'penugasanAktif.petugas'])
            ->where('kategori', 'sirkulasi')
            ->where('tipe_faktur', 'insidentil');

        if ($request->filled('status') && in_array($request->status, ['belum_bayar', 'cicilan', 'lunas'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_faktur', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_faktur', '<=', $request->sampai_tanggal);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_faktur', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('nama_customer', 'like', "%{$q}%");
                    });
            });
        }

        $fakturs = $query->latest('tanggal_faktur')->latest('id')->paginate(12)->withQueryString();

        $baseInsidentil = Faktur::where('kategori', 'sirkulasi')->where('tipe_faktur', 'insidentil');
        $totalInsidentilCount    = (clone $baseInsidentil)->count();
        $totalInsidentilTagihan  = (float) (clone $baseInsidentil)->sum('total_tagihan');
        $totalInsidentilTerbayar = (float) (clone $baseInsidentil)->sum('total_terbayar');
        $totalInsidentilPiutang  = (float) (clone $baseInsidentil)->sum('sisa_piutang');

        return view('pages.keuangan.faktur.sirkulasi.insidentil', compact(
            'fakturs',
            'totalInsidentilCount',
            'totalInsidentilTagihan',
            'totalInsidentilTerbayar',
            'totalInsidentilPiutang'
        ));
    }
}

