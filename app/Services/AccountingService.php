<?php

namespace App\Services;

use App\Models\AccountingAccountMapping;
use App\Models\Agen;
use App\Models\AkunPerkiraan;
use App\Models\AsetTetap;
use App\Models\JurnalUmum;
use App\Models\JurnalUmumDetail;
use App\Models\KasKeluar;
use App\Models\KasMasuk;
use App\Models\SistemConfig;
use App\Models\TransaksiKoran;
use App\Models\TransaksiOnline;
use App\Models\TransaksiPriangan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AccountingService
{
    /**
     * Definisi default mapping akun COA 7-segmen standar perusahaan.
     */
    public function getDefaultAccountMappings(): array
    {
        return [
            'kas_operasional' => [
                'label'          => 'Kas Tunai Operasional / Kasir',
                'kategori_modul' => 'Kas & Bank',
                'kode_perkiraan' => '1.00.00.01.01.00.00001',
                'nama_perkiraan' => 'Kas Operasional / Kasir',
                'keterangan'     => 'Penampung setoran tunai fisik kasir harian.',
            ],
            'bank_bca' => [
                'label'          => 'Bank BCA Giro Tasikmalaya',
                'kategori_modul' => 'Kas & Bank',
                'kode_perkiraan' => '1.00.01.01.01.00.00001',
                'nama_perkiraan' => 'Bank BCA Giro Tasikmalaya',
                'keterangan'     => 'Rekening penerimaan transfer & giro BCA serta QRIS utama.',
            ],
            'bank_bjb' => [
                'label'          => 'Bank BJB Giro Tasik',
                'kategori_modul' => 'Kas & Bank',
                'kode_perkiraan' => '1.00.01.01.01.00.06270',
                'nama_perkiraan' => 'Bank BJB Giro Tasik (Rek: 6270)',
                'keterangan'     => 'Rekening penerimaan transfer Bank BJB.',
            ],
            'piutang_iklan' => [
                'label'          => 'Piutang Usaha Iklan Umum',
                'kategori_modul' => 'Piutang (AR)',
                'kode_perkiraan' => '1.00.04.01.01.01.00001',
                'nama_perkiraan' => 'Piutang Usaha Iklan Umum',
                'keterangan'     => 'Sub-ledger piutang iklan Koran, Online, TV & Paket.',
            ],
            'piutang_sirkulasi' => [
                'label'          => 'Piutang Usaha Koran Sirkulasi',
                'kategori_modul' => 'Piutang (AR)',
                'kode_perkiraan' => '1.00.04.01.01.00.00002',
                'nama_perkiraan' => 'Piutang Usaha Koran Sirkulasi',
                'keterangan'     => 'Sub-ledger piutang agen & distribusi koran.',
            ],
            'piutang_pph23' => [
                'label'          => 'Piutang Pajak PPh Pasal 23',
                'kategori_modul' => 'Pajak & Piutang',
                'kode_perkiraan' => '1.00.06.01.01.00.00001',
                'nama_perkiraan' => 'Piutang Pajak PPh Pasal 23',
                'keterangan'     => 'Kredit bukti potong PPh 23 dari pemasang iklan instansi/PT.',
            ],
            'uang_muka_iklan' => [
                'label'          => 'Uang Muka / Pendapatan Diterima Di Muka',
                'kategori_modul' => 'Kewajiban & Pajak',
                'kode_perkiraan' => '2.00.01.01.01.01.00001',
                'nama_perkiraan' => 'Pendapatan Diterima Di Muka / Uang Muka Iklan',
                'keterangan'     => 'DP atau bayar di muka iklan sebelum tanggal terbit (PSAK 72).',
            ],
            'ppn_keluaran' => [
                'label'          => 'Utang Pajak PPN Keluaran',
                'kategori_modul' => 'Kewajiban & Pajak',
                'kode_perkiraan' => '2.00.03.01.01.00.00001',
                'nama_perkiraan' => 'Utang Pajak PPN Keluaran 11%',
                'keterangan'     => 'PPN yang dipungut atas faktur penjualan iklan.',
            ],
            'utang_pph23' => [
                'label'          => 'Utang Pajak PPh Pasal 23',
                'kategori_modul' => 'Kewajiban & Pajak',
                'kode_perkiraan' => '2.00.03.01.03.00.00001',
                'nama_perkiraan' => 'Utang Pajak PPh Pasal 23',
                'keterangan'     => 'Potongan PPh 23 atas tagihan jasa vendor/percetakan.',
            ],
            'utang_usaha_ap' => [
                'label'          => 'Utang Usaha Vendor / Supplier (AP)',
                'kategori_modul' => 'Utang Usaha (AP)',
                'kode_perkiraan' => '2.00.05.01.01.00.00001',
                'nama_perkiraan' => 'Beban Akrual / Utang Usaha Vendor',
                'keterangan'     => 'Utang usaha tagihan supplier kertas, tinta, percetakan & jasa.',
            ],
            'modal_disetor' => [
                'label'          => 'Modal Disetor Pemegang Saham',
                'kategori_modul' => 'Ekuitas',
                'kode_perkiraan' => '3.00.01.01.01.00.00001',
                'nama_perkiraan' => 'Modal Disetor PT Berkah Pikiran Rakyat',
                'keterangan'     => 'Akun modal dasar & disetor perusahaan.',
            ],
            'laba_ditahan' => [
                'label'          => 'Laba Ditahan',
                'kategori_modul' => 'Ekuitas',
                'kode_perkiraan' => '3.00.02.01.01.00.00001',
                'nama_perkiraan' => 'Laba Ditahan',
                'keterangan'     => 'Akumulasi laba ditahan lintas periode tutup buku.',
            ],
            'laba_tahun_berjalan' => [
                'label'          => 'Laba Tahun Berjalan (Closing)',
                'kategori_modul' => 'Ekuitas',
                'kode_perkiraan' => '3.00.03.01.01.00.00001',
                'nama_perkiraan' => 'Laba Tahun Berjalan',
                'keterangan'     => 'Penampung jurnal penutup Laba Rugi akhir periode.',
            ],
            'pendapatan_koran' => [
                'label'          => 'Pendapatan Iklan Koran',
                'kategori_modul' => 'Pendapatan',
                'kode_perkiraan' => '4.00.01.01.01.00.00001',
                'nama_perkiraan' => 'Pendapatan Iklan Kolom Koran',
                'keterangan'     => 'Pengakuan omset DPP iklan cetak koran.',
            ],
            'pendapatan_online' => [
                'label'          => 'Pendapatan Iklan Media Online',
                'kategori_modul' => 'Pendapatan',
                'kode_perkiraan' => '4.00.02.01.01.00.00001',
                'nama_perkiraan' => 'Pendapatan Iklan Media Online',
                'keterangan'     => 'Pengakuan omset DPP iklan web/portal online.',
            ],
            'pendapatan_tv' => [
                'label'          => 'Pendapatan Iklan Priangan TV',
                'kategori_modul' => 'Pendapatan',
                'kode_perkiraan' => '4.00.03.01.01.00.00001',
                'nama_perkiraan' => 'Pendapatan Iklan Priangan TV',
                'keterangan'     => 'Pengakuan omset DPP iklan siaran/konten TV.',
            ],
            'pendapatan_sirkulasi' => [
                'label'          => 'Pendapatan Sirkulasi Koran Cetak',
                'kategori_modul' => 'Pendapatan',
                'kode_perkiraan' => '4.00.04.01.01.00.00001',
                'nama_perkiraan' => 'Pendapatan Sirkulasi Koran Cetak',
                'keterangan'     => 'Pengakuan penjualan koran ke agen & pelanggan.',
            ],
            'retur_iklan' => [
                'label'          => 'Koreksi & Diskon Penjualan Iklan (CN-IKL)',
                'kategori_modul' => 'Pendapatan',
                'kode_perkiraan' => '4.00.05.01.01.00.00001',
                'nama_perkiraan' => 'Koreksi & Diskon Penjualan Iklan (CN-IKL)',
                'keterangan'     => 'Akun kontra pendapatan nota kredit koreksi iklan.',
            ],
            'retur_sirkulasi' => [
                'label'          => 'Retur & Koreksi Sirkulasi Koran (CN-SIR / BAPR)',
                'kategori_modul' => 'Pendapatan',
                'kode_perkiraan' => '4.00.05.01.02.00.00001',
                'nama_perkiraan' => 'Retur & Koreksi Sirkulasi Koran (CN-SIR)',
                'keterangan'     => 'Akun kontra pendapatan retur fisik BAPR & nota kredit agen.',
            ],
            'aset_tetap_default' => [
                'label'          => 'Aset Tetap Mesin, Bangunan & Peralatan',
                'kategori_modul' => 'Aset & Penyusutan',
                'kode_perkiraan' => '1.00.10.01.01.00.00001',
                'nama_perkiraan' => 'Aset Tetap Mesin Cetak & Komputer',
                'keterangan'     => 'Akun perolehan aset tetap perusahaan.',
            ],
            'akumulasi_penyusutan' => [
                'label'          => 'Akumulasi Penyusutan Aktiva Tetap',
                'kategori_modul' => 'Aset & Penyusutan',
                'kode_perkiraan' => '1.00.11.01.01.00.00001',
                'nama_perkiraan' => 'Akumulasi Penyusutan Aktiva Tetap',
                'keterangan'     => 'Kontra-aset akumulasi depresiasi bulanan.',
            ],
            'beban_penyusutan' => [
                'label'          => 'Beban Penyusutan Aset Tetap',
                'kategori_modul' => 'Aset & Penyusutan',
                'kode_perkiraan' => '6.00.22.01.01.00.00001',
                'nama_perkiraan' => 'Beban Penyusutan Aset Tetap',
                'keterangan'     => 'Beban depresiasi bulanan aset tetap ke GL.',
            ],
            'beban_admin_bank' => [
                'label'          => 'Beban Administrasi & Biaya Bank',
                'kategori_modul' => 'Rekonsiliasi Bank',
                'kode_perkiraan' => '6.00.01.01.01.00.00001',
                'nama_perkiraan' => 'Beban Operasional & Administrasi Bank',
                'keterangan'     => 'Penyesuaian biaya admin rekening koran bank.',
            ],
            'pendapatan_bunga_bank' => [
                'label'          => 'Pendapatan Jasa Giro / Bunga Bank',
                'kategori_modul' => 'Rekonsiliasi Bank',
                'kode_perkiraan' => '7.00.01.01.01.00.00001',
                'nama_perkiraan' => 'Pendapatan Jasa Giro & Bunga Bank',
                'keterangan'     => 'Penyesuaian jasa giro / bunga rekening koran bank.',
            ],
        ];
    }

    /**
     * Seed tabel mapping COA secara otomatis bila masih kosong.
     */
    public function ensureAccountMappingsSeeded(): void
    {
        if (!Schema::hasTable('accounting_account_mappings')) {
            return;
        }

        if (AccountingAccountMapping::count() > 0) {
            return;
        }

        foreach ($this->getDefaultAccountMappings() as $key => $cfg) {
            $akun = AkunPerkiraan::where('kode_perkiraan', $cfg['kode_perkiraan'])->first();
            AccountingAccountMapping::firstOrCreate(
                ['mapping_key' => $key],
                [
                    'label'          => $cfg['label'],
                    'kategori_modul' => $cfg['kategori_modul'],
                    'kode_perkiraan' => $cfg['kode_perkiraan'],
                    'nama_perkiraan' => $akun ? $akun->nama_perkiraan : $cfg['nama_perkiraan'],
                    'keterangan'     => $cfg['keterangan'],
                ]
            );
        }
    }

    /**
     * Ambil pasangan kode & nama COA berdasarkan kunci mapping dinamis.
     */
    public function getMappedAccount(string $key): array
    {
        $defaults = $this->getDefaultAccountMappings();
        $fallback = $defaults[$key] ?? [
            'kode_perkiraan' => '1.00.00.01.01.00.00001',
            'nama_perkiraan' => 'Kas Operasional / Kasir',
        ];

        if (!Schema::hasTable('accounting_account_mappings')) {
            return [
                'kode' => $fallback['kode_perkiraan'],
                'nama' => $fallback['nama_perkiraan'],
            ];
        }

        $this->ensureAccountMappingsSeeded();
        $row = AccountingAccountMapping::where('mapping_key', $key)->first();
        if (!$row) {
            return [
                'kode' => $fallback['kode_perkiraan'],
                'nama' => $fallback['nama_perkiraan'],
            ];
        }

        return [
            'kode' => $row->kode_perkiraan,
            'nama' => $row->nama_perkiraan,
        ];
    }

    /**
     * Tentukan akun Kas / Bank (Tunai Kasir, Bank BCA, atau Bank BJB) secara otomatis dari data KasMasuk.
     */
    public function resolveCashOrBankAccount(KasMasuk $km): array
    {
        $metode = strtolower((string) ($km->metode_bayar ?? 'tunai'));
        $bank = strtolower((string) ($km->bank_tujuan ?? ''));

        if (str_contains($bank, 'bjb')) {
            return $this->getMappedAccount('bank_bjb');
        }

        if (str_contains($bank, 'bca')) {
            return $this->getMappedAccount('bank_bca');
        }

        if (in_array($metode, ['transfer', 'qris', 'giro'], true) || (!empty($bank) && !str_contains($bank, 'kasir') && !str_contains($bank, 'tunai'))) {
            return $this->getMappedAccount('bank_bca');
        }

        return $this->getMappedAccount('kas_operasional');
    }

    /**
     * Buat Jurnal Pembalik (Reversal Journal) tanpa menghapus jurnal asli agar jejak audit tetap utuh (immutable ledger).
     */
    public function reverseJurnal(JurnalUmum $original, string $reason, ?string $tanggalReversal = null): ?JurnalUmum
    {
        if ($original->reversed_by_jurnal_id) {
            return JurnalUmum::find($original->reversed_by_jurnal_id);
        }

        $original->loadMissing('details');
        $tglRev = $tanggalReversal ?: Carbon::today()->toDateString();

        return DB::transaction(function () use ($original, $reason, $tglRev) {
            $noRev = JurnalUmum::generateNomorJurnal($tglRev);

            $rev = JurnalUmum::create([
                'no_jurnal'             => $noRev,
                'tanggal'               => $tglRev,
                'tipe'                  => 'pembalik',
                'sumber'                => $original->sumber,
                'referensi'             => 'REV-' . ($original->referensi ?: $original->no_jurnal),
                'keterangan'            => "Pembalik ({$original->no_jurnal}): {$reason}",
                'total_debet'           => $original->total_kredit,
                'total_kredit'          => $original->total_debet,
                'is_balanced'           => true,
                'status'                => 'posted',
                'user_id'               => auth()->id() ?? 1,
                'reversal_of_jurnal_id' => $original->id,
                'approved_by'           => auth()->id() ?? 1,
                'approved_at'           => now(),
                'approval_notes'        => $reason,
            ]);

            $urutan = 1;
            foreach ($original->details as $det) {
                JurnalUmumDetail::create([
                    'jurnal_umum_id'    => $rev->id,
                    'akun_perkiraan_id' => $det->akun_perkiraan_id,
                    'kode_perkiraan'    => $det->kode_perkiraan,
                    'nama_perkiraan'    => $det->nama_perkiraan,
                    'debet'             => $det->kredit,
                    'kredit'            => $det->debet,
                    'keterangan_baris'  => 'Pembalik: ' . ($det->keterangan_baris ?: $original->no_jurnal),
                    'urutan'            => $urutan++,
                ]);
            }

            $original->update([
                'reversed_by_jurnal_id' => $rev->id,
            ]);

            return $rev;
        });
    }

    /**
     * Ambil tarif & komposisi Sirkulasi dari SistemConfig (selaras SirkulasiDataService).
     */
    public function getSirkulasiPricing(): array
    {
        $hasConfig = Schema::hasTable('sistem_configs');
        $getCfg = fn (string $key, mixed $default) => $hasConfig ? SistemConfig::get($key, $default) : $default;

        $rasioL = (float) $getCfg('sirkulasi_rasio_langganan', 60);
        $rasioK = (float) $getCfg('sirkulasi_rasio_konsinyasi', 35);
        $rasioG = (float) $getCfg('sirkulasi_rasio_gratis', 5);
        $sumR = max(1, $rasioL + $rasioK + $rasioG);

        return [
            'harga_agen_besar' => max(0, (int) $getCfg('sirkulasi_harga_agen_besar', 2800)),
            'harga_eceran'     => max(0, (int) $getCfg('sirkulasi_harga_eceran', 3000)),
            'harga_langganan'  => max(0, (int) $getCfg('sirkulasi_harga_langganan', 3000)),
            'harga_cover_het'  => max(0, (int) $getCfg('sirkulasi_harga_cover_het', 3500)),
            'pct_langganan'    => $rasioL / $sumR,
            'pct_konsinyasi'   => $rasioK / $sumR,
            'pct_gratis'       => $rasioG / $sumR,
        ];
    }

    /**
     * Hitung omset harian satu agen dari jatah custom (Cache) atau komposisi default SistemConfig.
     */
    public function hitungNilaiJatahAgenHarian(Agen $agen, ?array $custom, array $pricing): array
    {
        $l = isset($custom['langganan']) ? (int) $custom['langganan'] : (int) ($agen->jatah_default * $pricing['pct_langganan']);
        $k = isset($custom['konsinyasi']) ? (int) $custom['konsinyasi'] : (int) ($agen->jatah_default * $pricing['pct_konsinyasi']);
        $g = isset($custom['gratis']) ? (int) $custom['gratis'] : (int) ($agen->jatah_default * $pricing['pct_gratis']);
        $p = isset($custom['pengenalan']) ? (int) $custom['pengenalan'] : 0;
        $i = isset($custom['insidentil']) ? (int) $custom['insidentil'] : 0;

        $isAgenBesar = (string) ($agen->kategori ?? '') === '11';
        $tarifJual = $isAgenBesar ? $pricing['harga_agen_besar'] : $pricing['harga_eceran'];
        $tarifLangganan = $pricing['harga_langganan'];

        $eksemplarBerbayar = $l + $k + $i;
        $totalEks = $l + $k + $g + $p + $i;
        $nominal = ($l * $tarifLangganan) + (($k + $i) * $tarifJual);

        return [
            'eks_total'    => $totalEks,
            'eks_berbayar' => $eksemplarBerbayar,
            'nominal'      => $nominal,
        ];
    }

    /**
     * Hitung rekap omset Sirkulasi dalam rentang tanggal (membaca jatah harian tersimpan atau proyeksi aktif).
     */
    public function hitungOmsetSirkulasiRange(string $startDate, string $endDate): array
    {
        $pricing = $this->getSirkulasiPricing();
        $daftarAgen = Agen::where('status', 'aktif')->orderBy('nama_agen')->get();
        $perAgen = [];
        foreach ($daftarAgen as $agen) {
            $perAgen[$agen->id] = ['eks_total' => 0, 'eks_berbayar' => 0, 'nominal' => 0.0];
        }

        $cursor = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $hari = 0;

        while ($cursor->lte($end)) {
            $tglStr = $cursor->toDateString();
            $savedMap = Cache::get('jatah_harian_' . $tglStr, []);
            foreach ($daftarAgen as $agen) {
                $calc = $this->hitungNilaiJatahAgenHarian($agen, $savedMap[$agen->id] ?? null, $pricing);
                $perAgen[$agen->id]['eks_total'] += $calc['eks_total'];
                $perAgen[$agen->id]['eks_berbayar'] += $calc['eks_berbayar'];
                $perAgen[$agen->id]['nominal'] += $calc['nominal'];
            }
            $hari++;
            $cursor->addDay();
        }

        return [
            'pricing'        => $pricing,
            'daftar_agen'    => $daftarAgen,
            'per_agen'       => $perAgen,
            'hari'           => $hari,
            'total_eks'      => array_sum(array_column($perAgen, 'eks_total')),
            'total_berbayar' => array_sum(array_column($perAgen, 'eks_berbayar')),
            'total_nominal'  => array_sum(array_column($perAgen, 'nominal')),
            'harga_rata'     => $pricing['harga_eceran'],
        ];
    }

    protected function getDefaultAsetSeeds(): array
    {
        return [
            [
                'kode'          => 'AST-001',
                'nama'          => 'Gedung Kantor Jl. Dr. Sukardjo',
                'kategori'      => 'Bangunan Permanen',
                'tgl_perolehan' => '2015-01-01',
                'harga_beli'    => 1200000000,
                'tarif_susut'   => 5.0,
                'susut_tahunan' => 60000000,
                'susut_bulanan' => 5000000,
            ],
            [
                'kode'          => 'AST-002',
                'nama'          => 'Mesin Cetak Web Offset Goss Community',
                'kategori'      => 'Mesin Produksi',
                'tgl_perolehan' => '2018-06-01',
                'harga_beli'    => 850000000,
                'tarif_susut'   => 12.5,
                'susut_tahunan' => 106250000,
                'susut_bulanan' => 8854167,
            ],
            [
                'kode'          => 'AST-003',
                'nama'          => 'Server & Perangkat Redaksi Komputer',
                'kategori'      => 'Peralatan Kantor',
                'tgl_perolehan' => '2023-01-15',
                'harga_beli'    => 180000000,
                'tarif_susut'   => 25.0,
                'susut_tahunan' => 45000000,
                'susut_bulanan' => 3750000,
            ],
            [
                'kode'          => 'AST-004',
                'nama'          => 'Armada Distribusi Sirkulasi (Mobil Box)',
                'kategori'      => 'Kendaraan',
                'tgl_perolehan' => '2022-04-10',
                'harga_beli'    => 240000000,
                'tarif_susut'   => 12.5,
                'susut_tahunan' => 30000000,
                'susut_bulanan' => 2500000,
            ],
        ];
    }

    /**
     * Pastikan tabel relasional aset_tetaps terisi dari data JSON lama atau default perusahaan.
     */
    public function ensureAsetTetapSeeded(): void
    {
        if (!Schema::hasTable('aset_tetaps') || AsetTetap::count() > 0) {
            return;
        }

        $seedSource = $this->getDefaultAsetSeeds();
        if (Schema::hasTable('sistem_configs')) {
            $raw = SistemConfig::get('accounting_aset_tetap_json');
            if ($raw) {
                $decoded = json_decode((string) $raw, true);
                if (is_array($decoded) && count($decoded) > 0) {
                    $seedSource = $decoded;
                }
            }
        }

        $defaultCoaAset = $this->getMappedAccount('aset_tetap_default')['kode'];

        foreach ($seedSource as $row) {
            AsetTetap::firstOrCreate(
                ['kode' => $row['kode']],
                [
                    'nama'                => $row['nama'] ?? 'Aset Tetap',
                    'kategori'            => $row['kategori'] ?? 'Peralatan Kantor',
                    'tgl_perolehan'       => $row['tgl_perolehan'] ?? Carbon::today()->toDateString(),
                    'harga_beli'          => (float) ($row['harga_beli'] ?? 0),
                    'tarif_susut'         => (float) ($row['tarif_susut'] ?? 10),
                    'susut_tahunan'       => (float) ($row['susut_tahunan'] ?? 0),
                    'susut_bulanan'       => (float) ($row['susut_bulanan'] ?? 0),
                    'kode_perkiraan_aset' => $row['kode_perkiraan_aset'] ?? $defaultCoaAset,
                    'status'              => $row['status'] ?? 'aktif',
                ]
            );
        }
    }

    /**
     * Ambil daftar Aset Tetap aktif dari tabel relasional `aset_tetaps` (atau fallback SistemConfig).
     */
    public function getDaftarAsetTetap(bool $includeDisposed = false): array
    {
        if (Schema::hasTable('aset_tetaps')) {
            $this->ensureAsetTetapSeeded();
            $query = AsetTetap::orderBy('kode');
            if (!$includeDisposed) {
                $query->where('status', 'aktif');
            }
            return $query->get()->map(fn (AsetTetap $a) => [
                'id'                  => $a->id,
                'kode'                => $a->kode,
                'nama'                => $a->nama,
                'kategori'            => $a->kategori,
                'tgl_perolehan'       => $a->tgl_perolehan?->format('Y-m-d'),
                'harga_beli'          => (float) $a->harga_beli,
                'tarif_susut'         => (float) $a->tarif_susut,
                'susut_tahunan'       => (float) $a->susut_tahunan,
                'susut_bulanan'       => (float) $a->susut_bulanan,
                'kode_perkiraan_aset' => $a->kode_perkiraan_aset,
                'status'              => $a->status,
                'tgl_disposal'        => $a->tgl_disposal?->format('Y-m-d'),
                'nilai_disposal'      => $a->nilai_disposal !== null ? (float) $a->nilai_disposal : null,
                'jurnal_perolehan_id' => $a->jurnal_perolehan_id,
                'jurnal_disposal_id'  => $a->jurnal_disposal_id,
            ])->values()->all();
        }

        $defaults = $this->getDefaultAsetSeeds();
        if (!Schema::hasTable('sistem_configs')) {
            return $defaults;
        }

        $raw = SistemConfig::get('accounting_aset_tetap_json');
        if ($raw === null || $raw === '') {
            SistemConfig::set('accounting_aset_tetap_json', json_encode($defaults), 'Daftar Register Aset Tetap Perusahaan');
            return $defaults;
        }

        $decoded = json_decode((string) $raw, true);
        return is_array($decoded) ? array_values($decoded) : $defaults;
    }

    public function saveDaftarAsetTetap(array $assets): void
    {
        if (Schema::hasTable('sistem_configs')) {
            SistemConfig::set('accounting_aset_tetap_json', json_encode(array_values($assets)), 'Daftar Register Aset Tetap Perusahaan');
        }
    }

    /**
     * Hitung akumulasi penyusutan seluruh aset tetap aktif hingga akhir bulan periode.
     */
    public function hitungAkumulasiSusutHingga(array $assets, string $endDate): float
    {
        $end = Carbon::parse($endDate)->endOfMonth();
        $akumulasi = 0.0;

        foreach ($assets as $aset) {
            if (($aset['status'] ?? 'aktif') !== 'aktif') {
                continue;
            }
            $tgl = Carbon::parse($aset['tgl_perolehan'] ?? $endDate)->startOfMonth();
            if ($tgl->gt($end)) {
                continue;
            }
            $bulanBerjalan = max(0, ($end->year - $tgl->year) * 12 + ($end->month - $tgl->month) + 1);
            $harga = (float) ($aset['harga_beli'] ?? 0);
            $susutBulanan = (float) ($aset['susut_bulanan'] ?? 0);
            $akumulasi += min($harga, $bulanBerjalan * $susutBulanan);
        }

        return $akumulasi;
    }

    /**
     * Scope KasMasuk aktif (tidak void).
     */
    public function kasMasukAktif(): Builder
    {
        return KasMasuk::where(function ($q) {
            $q->where('status_void', false)->orWhereNull('status_void');
        });
    }

    /**
     * Scope KasKeluar aktif (tidak void) dan mengecualikan mutasi internal Setor Kas ke Bank (KO-09).
     */
    public function kasKeluarBebanAktif(): Builder
    {
        return KasKeluar::where(function ($q) {
            $q->where('status_void', false)->orWhereNull('status_void');
        })->where(function ($q) {
            $q->whereNull('kategori_beban')
              ->orWhere('kategori_beban', '!=', 'setor_bank');
        })->where(function ($q) {
            $q->whereNull('kode_perkiraan')
              ->orWhere('kode_perkiraan', '!=', '1.02.01.01.01');
        });
    }

    /**
     * Ringkasan transaksi operasional (Sub-Ledger) yang belum ditarik/diposting ke General Ledger (GL).
     */
    public function getUnpostedSubledgerSummary(?string $periode = null): array
    {
        $periodeKey = $periode ?: Carbon::now()->format('Y-m');

        $iklanUnpulled = TransaksiKoran::where('tanggal_muatkoran', 'like', "{$periodeKey}%")
            ->where('status_order', '!=', 'Batal')
            ->where(fn ($q) => $q->where('is_pulled_akuntansi', false)->orWhereNull('is_pulled_akuntansi'))
            ->count()
            + TransaksiOnline::where('tanggal_muatiklanonline', 'like', "{$periodeKey}%")
                ->where(fn ($q) => $q->whereNull('status_produksi')->orWhere('status_produksi', '!=', 'batal'))
                ->where(fn ($q) => $q->where('is_pulled_akuntansi', false)->orWhereNull('is_pulled_akuntansi'))
                ->count()
            + TransaksiPriangan::where('tanggal_muatiklanpriangan', 'like', "{$periodeKey}%")
                ->where(fn ($q) => $q->whereNull('status_tayang')->orWhere('status_tayang', '!=', 'batal'))
                ->where(fn ($q) => $q->where('is_pulled_akuntansi', false)->orWhereNull('is_pulled_akuntansi'))
                ->count();

        $kasMasukIklanUnpulled = $this->kasMasukAktif()
            ->where(fn ($q) => $q->where('kode_bagian', '01')->orWhere('tipe_transaksi', 'iklan'))
            ->where(fn ($q) => $q->where('tgl_transaksi', 'like', "{$periodeKey}%")->orWhere('tanggal_terima', 'like', "{$periodeKey}%"))
            ->where(fn ($q) => $q->where('is_pulled_accounting', false)->orWhereNull('is_pulled_accounting'))
            ->count();

        $kasMasukSirkulasiUnpulled = $this->kasMasukAktif()
            ->where(fn ($q) => $q->where('kode_bagian', '02')->orWhere('tipe_transaksi', 'sirkulasi'))
            ->where(fn ($q) => $q->where('tgl_transaksi', 'like', "{$periodeKey}%")->orWhere('tanggal_terima', 'like', "{$periodeKey}%"))
            ->where(fn ($q) => $q->where('is_pulled_accounting', false)->orWhereNull('is_pulled_accounting'))
            ->count();

        $bebanUnpulledQuery = $this->kasKeluarBebanAktif()
            ->where('tanggal_keluar', 'like', "{$periodeKey}%")
            ->where(fn ($q) => $q->where('is_pulled_accounting', false)->orWhereNull('is_pulled_accounting'));
        $bebanUnpulledCount = (clone $bebanUnpulledQuery)->count();
        $bebanUnpulledNominal = (float) (clone $bebanUnpulledQuery)->sum('nominal_keluar');

        $draftJurnalCount = JurnalUmum::where('tanggal', 'like', "{$periodeKey}%")
            ->where('status', 'draft')
            ->count();

        $totalPendingItems = $iklanUnpulled + $kasMasukIklanUnpulled + $kasMasukSirkulasiUnpulled + $bebanUnpulledCount + $draftJurnalCount;

        return [
            'periode'                     => $periodeKey,
            'iklan_unpulled'              => $iklanUnpulled,
            'kas_masuk_iklan_unpulled'    => $kasMasukIklanUnpulled,
            'kas_masuk_sirkulasi_unpulled'=> $kasMasukSirkulasiUnpulled,
            'beban_unpulled_count'        => $bebanUnpulledCount,
            'beban_unpulled_nominal'      => $bebanUnpulledNominal,
            'draft_jurnal_count'          => $draftJurnalCount,
            'total_pending_items'         => $totalPendingItems,
        ];
    }
}
