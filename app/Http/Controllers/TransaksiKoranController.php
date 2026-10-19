<?php

namespace App\Http\Controllers;

use App\Models\DailyCutoff;
use App\Models\JenisIklan;
use App\Models\KopIklan;
use App\Models\KopKota;
use App\Models\MemoUnlock;
use App\Models\OrderJadwalMuat;
use App\Models\Pelanggan;
use App\Models\PembayaranIklan;
use App\Models\SalesAe;
use App\Models\SistemConfig;
use App\Models\Sppi;
use App\Models\TransaksiKoran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransaksiKoranController extends Controller
{
    /**
     * Tampilkan daftar transaksi iklan koran
     */
    public function index(Request $request)
    {
        $query = TransaksiKoran::with([
            'iklankoran', 'pelanggan', 'inputOleh', 'sppi',
            'kopKota', 'kopIklan', 'salesAe', 'jadwalMuat'
        ])->latest();

        // Fitur Pencarian
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('nofakturkoran', 'like', "%{$keyword}%")
                  ->orWhere('nama_pemasangkoran', 'like', "%{$keyword}%")
                  ->orWhere('sales_iklankoran', 'like', "%{$keyword}%")
                  ->orWhere('komisioner', 'like', "%{$keyword}%")
                  ->orWhereHas('pelanggan', function ($pq) use ($keyword) {
                      $pq->where('nama_instansi', 'like', "%{$keyword}%")
                        ->orWhere('kode_pelanggan', 'like', "%{$keyword}%");
                  });
            });
        }

        // Filter Kategori Form (Baris / Kolom / Display / Advertorial)
        if ($request->filled('kategori_form')) {
            $query->where('kategori_form', $request->kategori_form);
        }

        // Filter Status Pembayaran
        if ($request->filled('status')) {
            if ($request->status === 'lunas') {
                $query->where('piutang_transaksikoran', '<=', 0);
            } elseif ($request->status === 'piutang') {
                $query->where('piutang_transaksikoran', '>', 0);
            }
        }

        // Filter Status Order (Lifecycle)
        if ($request->filled('status_order')) {
            $query->where('status_order', $request->status_order);
        }

        $transaksikoran = $query->paginate(15)->withQueryString();
        $iklankoran = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'KRN'))->where('is_active', true)->get();

        // Statistik Cepat untuk Top Bar
        $totalOmset = TransaksiKoran::where('status_order', '!=', 'Batal')->sum('totaltagihan_transaksikoran');
        $totalPiutang = TransaksiKoran::where('status_order', '!=', 'Batal')->sum('piutang_transaksikoran');
        $antreanCBP = TransaksiKoran::where('status_order', 'Menunggu_Bayar')->count();

        return view('pages.iklan.transaksi.koran.index', compact('transaksikoran', 'iklankoran', 'totalOmset', 'totalPiutang', 'antreanCBP'));
    }

    /**
     * Formulir terpadu pemasangan iklan koran digital (Dual-Pane Cockpit 2026)
     */
    public function create()
    {
        $iklankoran = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'KRN'))
            ->where('is_active', true)
            ->orderByRaw("FIELD(tipe_dasar, 'BARIS', 'KOLOM', 'DISPLAY', 'KHUSUS', 'ADVERTORIAL', 'BONUS')")
            ->orderBy('id')
            ->get();

        $nofakturkoran = TransaksiKoran::createCode();
        $pelanggans = Pelanggan::where('is_active', true)->orderBy('nama_instansi')->get();
        $kopKotas = KopKota::where('is_active', true)->orderBy('nomor_halaman')->get();
        $kopIklans = KopIklan::where('is_active', true)->orderBy('urutan')->get();
        $salesAes = SalesAe::where('is_active', true)->orderBy('kode_ae')->get();

        // Parameter Konfigurasi Standar Industri Kabar Priangan
        $configs = [
            'ppn_persen'         => SistemConfig::getPpnPersen(),
            'jam_cutoff'         => SistemConfig::getJamCutoff(),
            'is_past_cutoff'     => SistemConfig::isPastCutoff(),
            'tarif_per_baris'    => (int) SistemConfig::get('tarif_per_baris', 22750),
            'tarif_mmk_bw'       => (int) SistemConfig::get('tarif_mmk_bw', 12000),
            'tarif_mmk_fc'       => (int) SistemConfig::get('tarif_mmk_fc', 24000),
            'minimal_baris'      => (int) SistemConfig::get('minimal_baris', 2),
            'maks_karakter'      => (int) SistemConfig::get('maksimal_karakter_per_baris', 32),
            'diskon_agensi'      => (int) SistemConfig::get('diskon_agensi_default', 15),
            'materai_nominal'    => (int) SistemConfig::get('materai_nominal', 10000),
            'materai_threshold'  => (int) SistemConfig::get('materai_threshold', 5000000),
        ];

        // Kalender Nasional Resmi & Peringatan Indonesia
        $kalenderService = app(\App\Services\KalenderNasionalService::class);
        $kalenderAwal = $kalenderService->getKalenderBulan((int) date('Y'), (int) date('n'));

        return view('pages.iklan.transaksi.koran.create', compact(
            'nofakturkoran', 'iklankoran', 'pelanggans', 'kopKotas', 'kopIklans', 'salesAes', 'configs', 'kalenderAwal'
        ));
    }

    /**
     * Simpan transaksi iklan koran
     */
    public function store(Request $request)
    {
        $request->validate([
            'nofakturkoran'          => 'required',
            'tanggal_transaksikoran' => 'required|date',
            'nama_pemasangkoran'     => 'required|string',
            'alamat_pemasangkoran'   => 'nullable|string',
            'id_iklankoran'          => 'required|exists:jenis_iklan,id',
            'kategori_form'          => 'required|in:BARIS,KOLOM,DISPLAY,ADVERTORIAL,KHUSUS,BONUS',
            'total_muatkoran'        => 'required|numeric|min:1',
            'jenis_pembayaran'       => 'required|in:Tunai,Kredit',
            'harga_transaksikoran'   => 'required',
            'jumlahbayar_transaksikoran' => 'required',
            'lebar_kolom'            => 'nullable|numeric|max:7',
            'tinggi_mm'              => 'nullable|numeric|max:540',
            'file_materi'            => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,tiff|max:15360',
        ]);

        $qty = (int) ($request->total_muatkoran ?? 1);

        // Validasi Aturan Bisnis 1: Minimal 2 Baris untuk Iklan Baris
        if ($request->kategori_form === 'BARIS') {
            $jmlBaris = (int) ($request->jumlah_baris ?? 0);
            if ($jmlBaris < 2) {
                return back()->withErrors(['jumlah_baris' => 'Minimal order iklan baris adalah 2 baris teks naskah.'])->withInput();
            }
        }

        // Validasi Aturan Bisnis 2: Maksimal 7 Kolom
        $lebarKolom = (int) ($request->lebar_kolom ?? 1);
        if ($lebarKolom > 7) {
            return back()->withErrors(['lebar_kolom' => 'Lebar kolom iklan maksimal 7 kolom (batas fisik halaman koran).'])->withInput();
        }

        // Validasi Aturan Bisnis 3: Sinkronisasi 1-to-1 Jumlah Pemuatan & Tanggal Muat
        $rawDates = $request->tanggal_muat_list;
        if (is_string($rawDates)) {
            $rawDates = array_values(array_filter(array_map('trim', explode(',', $rawDates))));
        } elseif (!is_array($rawDates)) {
            $rawDates = [];
        }

        // Jika hanya 1 tanggal muat dan list kosong, fallback ke tanggal_muatkoran
        if (empty($rawDates) && $request->filled('tanggal_muatkoran')) {
            $rawDates = [$request->tanggal_muatkoran];
        }

        if (count($rawDates) !== $qty) {
            return back()->withErrors([
                'tanggal_muat_list' => "Jumlah tanggal muat yang dipilih (" . count($rawDates) . " tanggal) harus persis sama dengan jumlah pemuatan ({$qty} kali)."
            ])->withInput();
        }

        // Cek apakah ada tanggal yang terkunci oleh Tutup Transaksi Harian (Cut-Off)
        foreach ($rawDates as $tgl) {
            if (DailyCutoff::isDateLocked($tgl)) {
                return back()->withErrors([
                    'tanggal_muat_list' => "Edisi tanggal {$tgl} sudah ditutup (Cut-off). Gunakan Memo Unlock Supervisor untuk memasukkan order susulan."
                ])->withInput();
            }
        }

        // Handle File Upload Materi Iklan
        $fileMateriPath = null;
        if ($request->hasFile('file_materi')) {
            $file = $request->file('file_materi');
            $cleanFaktur = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->nofakturkoran);
            $fileName = 'materi_' . time() . '_' . $cleanFaktur . '.' . $file->getClientOriginalExtension();
            $fileMateriPath = $file->storeAs('materi_iklan', $fileName, 'public');
        }

        $harga_bersih   = (int) str_replace(['.', ','], '', $request->harga_transaksikoran);
        $diskon_agensi  = (int) str_replace(['.', ','], '', $request->diskon_agensi ?? 0);
        $diskon_umum    = (int) str_replace(['.', ','], '', $request->diskon_umum ?? 0);
        $diskon_khusus  = (int) str_replace(['.', ','], '', $request->diskon_khusus ?? 0);
        $diskon_total   = $diskon_agensi + $diskon_umum + $diskon_khusus;

        $bayar_bersih   = (int) str_replace(['.', ','], '', $request->jumlahbayar_transaksikoran);

        $ppnPersenConfig = (float) ($request->ppn_persen ?? SistemConfig::getPpnPersen());
        $materaiNominal  = (int) str_replace(['.', ','], '', $request->materai ?? 0);

        // Perhitungan Tagihan Bruto & Diskon
        $total_omset    = $harga_bersih * $qty;
        $setelah_diskon = max(0, $total_omset - $diskon_total);

        // Rumus PPN Resmi DJP: Dasar Pengenaan Pajak (DPP)
        $dpp            = (int) round($setelah_diskon / (1 + ($ppnPersenConfig / 100)));
        $ppn            = $setelah_diskon - $dpp;
        $total_tagihan  = $setelah_diskon + $materaiNominal;
        $piutang        = max(0, $total_tagihan - $bayar_bersih);

        // Komisioner (Perantara / Pihak Ketiga / Calo) & Komisi Fleksibel
        $komisionerNama = $request->filled('komisioner') ? trim($request->komisioner) : null;
        $komisiPersen   = $request->filled('komisi_persen') ? (float) $request->komisi_persen : 0;
        $komisi_input   = $request->filled('komisi_transaksikoran') ? (int) str_replace(['.', ','], '', $request->komisi_transaksikoran) : 0;
        
        if ($komisi_input > 0) {
            $komisi = $komisi_input;
            if ($dpp > 0 && $komisiPersen == 0) {
                $komisiPersen = round(($komisi / $dpp) * 100, 2);
            }
        } elseif ($komisiPersen > 0) {
            $komisi = (int) round(($dpp * $komisiPersen) / 100);
        } else {
            $komisi = 0;
            $komisiPersen = 0;
        }

        // Insentif Sales AE / FO Fleksibel
        $insentifPersen = $request->filled('insentif_persen') ? (float) $request->insentif_persen : 0;
        $insentif_input = $request->filled('insentif_transaksikoran') ? (int) str_replace(['.', ','], '', $request->insentif_transaksikoran) : 0;

        if ($insentif_input > 0) {
            $insentif = $insentif_input;
            if ($dpp > 0 && $insentifPersen == 0) {
                $insentifPersen = round(($insentif / $dpp) * 100, 2);
            }
        } elseif ($insentifPersen > 0) {
            $insentif = (int) round(($dpp * $insentifPersen) / 100);
        } else {
            $insentif = 0;
            $insentifPersen = 0;
        }

        // Tentukan Status Order (Aturan CBP Gate)
        $statusOrder = 'Antrean_Pracetak';
        if ($request->jenis_pembayaran === 'Tunai') {
            if ($piutang > 0) {
                // CBP Gate aktif: tidak boleh ke pracetak sebelum lunas
                $statusOrder = 'Menunggu_Bayar';
            } else {
                $statusOrder = 'Antrean_Pracetak';
            }
        } elseif ($request->jenis_pembayaran === 'Kredit') {
            $statusOrder = $request->boolean('disetujui_kredit') ? 'Antrean_Pracetak' : 'Menunggu_Bayar';
        }

        // Resolusi Relasi Kop Kota, Kop Iklan, dan Sales AE
        $kopKota = $request->filled('kop_kota_id') ? KopKota::find($request->kop_kota_id) : null;
        $kopKotaNama = $kopKota ? $kopKota->nama_penempatan : ($request->halaman_iklan ?? 'Halaman Dalam');

        $salesAe = $request->filled('sales_ae_id') ? SalesAe::find($request->sales_ae_id) : null;
        $salesNama = $salesAe ? ($salesAe->kode_ae . ' - ' . $salesAe->nama_ae) : ($request->sales_iklankoran ?? 'FO - Front Office');

        DB::beginTransaction();
        try {
            // Relasi atau pembuatan pelanggan otomatis jika belum terhubung
            $pelangganId = $request->pelanggan_id;
            if (!$pelangganId && $request->filled('nama_pemasangkoran')) {
                $existingPlg = Pelanggan::where('nama_instansi', $request->nama_pemasangkoran)->first();
                if ($existingPlg) {
                    $pelangganId = $existingPlg->id;
                } else {
                    $newPlg = Pelanggan::create([
                        'kode_pelanggan' => Pelanggan::createCode(),
                        'nama_instansi'  => $request->nama_pemasangkoran,
                        'jenis_klien'    => 'Perorangan',
                        'alamat'         => $request->alamat_pemasangkoran,
                        'no_telp'        => $request->no_telp ?? null,
                        'is_active'      => true,
                    ]);
                    $pelangganId = $newPlg->id;
                }
            }

            $primaryTanggalMuat = $rawDates[0] ?? $request->tanggal_muatkoran ?? now()->addDay()->toDateString();

            $transaksi = TransaksiKoran::create([
                'nofakturkoran'          => $request->nofakturkoran,
                'tanggal_transaksikoran' => $request->tanggal_transaksikoran,
                'pelanggan_id'           => $pelangganId,
                'input_oleh_user_id'     => Auth::id(),
                'nama_pemasangkoran'     => $request->nama_pemasangkoran,
                'alamat_pemasangkoran'   => $request->alamat_pemasangkoran ?? '-',
                'id_iklankoran'          => $request->id_iklankoran,
                'halaman_iklan'          => $kopKotaNama,
                'kop_kota_id'            => $request->kop_kota_id,
                'kop_iklan_id'           => $request->kop_iklan_id,
                'sales_ae_id'            => $request->sales_ae_id,
                'warna_iklan'            => $request->warna_iklan ?? 'BW',
                'ukuran_iklan'           => $request->ukuran_iklan ?? '-',
                'kategori_form'          => $request->kategori_form,
                'naskah_iklan_baris'     => $request->naskah_iklan_baris,
                'jumlah_baris'           => $request->jumlah_baris ?? 0,
                'lebar_kolom'            => $lebarKolom,
                'tinggi_mm'              => $request->tinggi_mm ?? 0,
                'produk_jasa'            => $request->produk_jasa,
                'jenis_harga'            => $request->jenis_harga ?? 'Normal',
                'jenis_pembayaran'       => $request->jenis_pembayaran,
                'jatuh_tempo_pembayaran' => $request->jatuh_tempo_pembayaran,
                'keterangan_materi'      => $request->keterangan_materi,
                'status_materi'          => $request->status_materi ?? 'Siap_Cetak',
                'file_materi'            => $fileMateriPath,
                'kop_iklan_kota'         => $kopKotaNama,
                'tanggal_muatkoran'      => $primaryTanggalMuat,
                'tanggal_muat_list'      => $rawDates,
                'sales_iklankoran'       => $salesNama,
                'komisioner'             => $komisionerNama,
                'komisi_persen'          => $komisiPersen,
                'insentif_persen'        => $insentifPersen,
                'total_muatkoran'        => $qty,
                'harga_transaksikoran'   => $harga_bersih,
                'diskon_transaksikoran'  => $diskon_total,
                'diskon_agensi'          => $diskon_agensi,
                'diskon_umum'            => $diskon_umum,
                'diskon_khusus'          => $diskon_khusus,
                'materai'                => $materaiNominal,
                'ppn_persen'             => $ppnPersenConfig,
                'insentif_transaksikoran'=> $insentif,
                'komisi_transaksikoran'  => $komisi,
                'ppn_transaksikoran'     => $ppn,
                'totaltagihan_transaksikoran' => $total_tagihan,
                'jumlahbayar_transaksikoran'  => $bayar_bersih,
                'piutang_transaksikoran'      => $piutang,
                'status_order'           => $statusOrder,
                'status_sppi'            => 'Belum_Ditandatangani',
                'disetujui_kredit'       => $request->boolean('disetujui_kredit', false),
                'disetujui_kredit_oleh'  => $request->boolean('disetujui_kredit') ? Auth::id() : null,
            ]);

            // Sinkronisasi Multi-Date ke tabel order_jadwal_muat
            foreach ($rawDates as $index => $tglMuat) {
                OrderJadwalMuat::create([
                    'transaksi_id'  => $transaksi->id,
                    'tanggal_muat'  => $tglMuat,
                    'edisi_ke'      => $index + 1,
                    'status_tayang' => 'TERJADWAL',
                ]);
            }

            // Auto-generate SPPI (Insertion Order)
            Sppi::create([
                'nomor_sppi'   => Sppi::createCode('KRN'),
                'media_type'   => 'koran',
                'transaksi_id' => $transaksi->id,
                'nofaktur'     => $transaksi->nofakturkoran,
                'tanggal_sppi' => $transaksi->tanggal_transaksikoran,
                'status_sppi'  => 'Belum_Ditandatangani',
            ]);

            // Catat Pembayaran Pertama / DP jika ada uang yang disetorkan
            if ($bayar_bersih > 0) {
                PembayaranIklan::create([
                    'nokwitansi'     => PembayaranIklan::createCode('KRN'),
                    'media_type'     => 'koran',
                    'transaksi_id'   => $transaksi->id,
                    'nofaktur'       => $transaksi->nofakturkoran,
                    'tanggal_bayar'  => $transaksi->tanggal_transaksikoran,
                    'nominal_bayar'  => $bayar_bersih,
                    'potongan_pph23' => 0,
                    'metode_bayar'   => ($request->jenis_pembayaran === 'Tunai') ? 'Tunai_Kasir' : 'Transfer_Bank',
                    'nama_penyetor'  => $transaksi->nama_pemasangkoran,
                    'catatan'        => 'Pembayaran awal saat pemesanan iklan di FO',
                    'kasir_user_id'  => Auth::id(),
                ]);
            }

            DB::commit();

            return redirect()->route('transaksikoran.show', $transaksi->id)
                ->with('success', "Pemesanan Iklan Koran {$transaksi->nofakturkoran} Berhasil Disimpan & Terjadwal!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan transaksi: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Tampilkan rincian detail lengkap transaksi order (Show View)
     */
    public function show(string $id)
    {
        $transaksi = TransaksiKoran::with([
            'iklankoran', 'pelanggan', 'inputOleh', 'sppi',
            'pembayaranList.kasirUser', 'kopKota', 'kopIklan', 'salesAe', 'jadwalMuat',
            'dibatalkanOleh', 'disetujuiKreditOleh'
        ])->findOrFail($id);

        return view('pages.iklan.transaksi.koran.show', compact('transaksi'));
    }

    /**
     * Tampilkan form edit & reschedule transaksi
     */
    public function edit(string $id)
    {
        $transaksi = TransaksiKoran::with([
            'iklankoran', 'pelanggan', 'kopKota', 'kopIklan', 'salesAe', 'jadwalMuat'
        ])->findOrFail($id);

        $iklankoran = JenisIklan::whereHas('kategoriMedia', fn($q) => $q->where('kode', 'KRN'))
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $kopKotas = KopKota::where('is_active', true)->orderBy('nomor_halaman')->get();
        $kopIklans = KopIklan::where('is_active', true)->orderBy('urutan')->get();
        $salesAes = SalesAe::where('is_active', true)->orderBy('kode_ae')->get();

        $configs = [
            'ppn_persen'         => SistemConfig::getPpnPersen(),
            'jam_cutoff'         => SistemConfig::getJamCutoff(),
            'is_past_cutoff'     => SistemConfig::isPastCutoff(),
        ];

        return view('pages.iklan.transaksi.koran.edit', compact(
            'transaksi', 'iklankoran', 'kopKotas', 'kopIklans', 'salesAes', 'configs'
        ));
    }

    /**
     * Update transaksi iklan koran (termasuk reschedule jadwal muat & koreksi materi)
     */
    public function update(Request $request, string $id)
    {
        $transaksi = TransaksiKoran::with('jadwalMuat')->findOrFail($id);

        $request->validate([
            'nama_pemasangkoran'     => 'required|string',
            'alamat_pemasangkoran'   => 'nullable|string',
            'status_materi'          => 'required|in:Siap_Cetak,Butuh_Setting',
            'file_materi'            => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,tiff|max:15360',
        ]);

        DB::beginTransaction();
        try {
            // Handle pergantian file materi jika diupload baru
            $fileMateriPath = $transaksi->file_materi;
            if ($request->hasFile('file_materi')) {
                $file = $request->file('file_materi');
                $cleanFaktur = preg_replace('/[^A-Za-z0-9_\-]/', '_', $transaksi->nofakturkoran);
                $fileName = 'materi_rev_' . time() . '_' . $cleanFaktur . '.' . $file->getClientOriginalExtension();
                $fileMateriPath = $file->storeAs('materi_iklan', $fileName, 'public');
            }

            // Update kop kota & sales jika dipilih
            $kopKotaNama = $transaksi->halaman_iklan;
            if ($request->filled('kop_kota_id')) {
                $kopKota = KopKota::find($request->kop_kota_id);
                if ($kopKota) $kopKotaNama = $kopKota->nama_penempatan;
            }

            $salesNama = $transaksi->sales_iklankoran;
            if ($request->filled('sales_ae_id')) {
                $salesAe = SalesAe::find($request->sales_ae_id);
                if ($salesAe) $salesNama = "[{$salesAe->kode_ae}] {$salesAe->nama_ae}";
            }

            // Update jadwal muat per edisi (Reschedule)
            if ($request->has('jadwal')) {
                foreach ($request->input('jadwal', []) as $jadwalId => $jadwalData) {
                    $itemJadwal = OrderJadwalMuat::where('transaksi_id', $transaksi->id)->find($jadwalId);
                    if ($itemJadwal && isset($jadwalData['tanggal_muat'])) {
                        $itemJadwal->update([
                            'tanggal_muat'  => $jadwalData['tanggal_muat'],
                            'status_tayang' => $jadwalData['status_tayang'] ?? $itemJadwal->status_tayang,
                            'catatan'       => $jadwalData['catatan'] ?? $itemJadwal->catatan,
                        ]);
                    }
                }
            }

            $updateData = [
                'nama_pemasangkoran'   => $request->nama_pemasangkoran,
                'alamat_pemasangkoran' => $request->alamat_pemasangkoran ?? '-',
                'kop_kota_id'          => $request->kop_kota_id ?? $transaksi->kop_kota_id,
                'halaman_iklan'        => $kopKotaNama,
                'kop_iklan_id'         => $request->kop_iklan_id ?? $transaksi->kop_iklan_id,
                'sales_ae_id'          => $request->sales_ae_id ?? $transaksi->sales_ae_id,
                'sales_iklankoran'     => $salesNama,
                'naskah_iklan_baris'   => $request->naskah_iklan_baris ?? $transaksi->naskah_iklan_baris,
                'keterangan_materi'    => $request->keterangan_materi ?? $transaksi->keterangan_materi,
                'status_materi'        => $request->status_materi,
                'file_materi'          => $fileMateriPath,
                'status_order'         => $request->status_order ?? $transaksi->status_order,
            ];

            if ($request->has('komisioner')) {
                $updateData['komisioner'] = $request->filled('komisioner') ? trim($request->komisioner) : null;
            }
            if ($request->has('komisi_persen')) {
                $updateData['komisi_persen'] = (float) $request->komisi_persen;
            }
            if ($request->has('komisi_transaksikoran')) {
                $updateData['komisi_transaksikoran'] = (int) str_replace(['.', ','], '', $request->komisi_transaksikoran ?? 0);
            }
            if ($request->has('insentif_persen')) {
                $updateData['insentif_persen'] = (float) $request->insentif_persen;
            }
            if ($request->has('insentif_transaksikoran')) {
                $updateData['insentif_transaksikoran'] = (int) str_replace(['.', ','], '', $request->insentif_transaksikoran ?? 0);
            }

            $transaksi->update($updateData);

            DB::commit();

            return redirect()->route('transaksikoran.show', $transaksi->id)
                ->with('success', "Order {$transaksi->nofakturkoran} berhasil diperbarui & jadwal disinkronkan!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memperbarui transaksi: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Prosedur Pembatalan Transaksi Iklan Standar Pers
     */
    public function batalkan(Request $request, string $id)
    {
        $transaksi = TransaksiKoran::findOrFail($id);

        $request->validate([
            'alasan_batal' => 'required|string|min:5|max:500',
        ]);

        $transaksi->update([
            'status_order'            => 'Batal',
            'alasan_batal'            => $request->alasan_batal,
            'dibatalkan_at'           => now(),
            'dibatalkan_oleh_user_id' => Auth::id(),
        ]);

        // Tandai seluruh jadwal muat menjadi BATAL
        $transaksi->jadwalMuat()->update(['status_tayang' => 'BATAL']);

        return redirect()->route('transaksikoran.show', $transaksi->id)
            ->with('success', "Order {$transaksi->nofakturkoran} resmi DIBATALKAN. Audit log pembatalan telah dicatat.");
    }

    /**
     * Cetak Lembar Kerja Pracetak (Job Ticket untuk setter & layout redaksi)
     */
    public function lembarKerjaPracetak(string $id)
    {
        $transaksi = TransaksiKoran::with([
            'iklankoran', 'pelanggan', 'inputOleh', 'sppi',
            'kopKota', 'kopIklan', 'salesAe', 'jadwalMuat'
        ])->findOrFail($id);

        return view('pages.iklan.transaksi.koran.lembar_kerja_pracetak', compact('transaksi'));
    }

    /**
     * Cetak faktur transaksi koran standar pers
     */
    public function cetak(string $id)
    {
        $transaksi = TransaksiKoran::with([
            'iklankoran', 'pelanggan', 'inputOleh', 'sppi',
            'pembayaranList', 'kopKota', 'kopIklan', 'salesAe', 'jadwalMuat'
        ])->findOrFail($id);

        return view('pages.iklan.transaksi.koran.cetak', compact('transaksi'));
    }

    /**
     * Hapus transaksi (Hanya untuk order draft)
     */
    public function destroy(string $id)
    {
        $transaksi = TransaksiKoran::findOrFail($id);

        if ($transaksi->jumlahbayar_transaksikoran > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Order yang sudah memiliki riwayat pembayaran tidak dapat dihapus permanen. Gunakan prosedur Pembatalan Transaksi.'
            ], 422);
        }

        $transaksi->delete();

        return response()->json(['status' => 'success']);
    }
}
