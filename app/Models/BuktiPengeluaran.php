<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BuktiPengeluaran extends Model
{
    use HasFactory;

    protected $table = 'bukti_pengeluarans';

    protected $fillable = [
        'no_bp',
        'no_register_anggaran',
        'pengajuan_anggaran_id',
        'divisi',
        'kode_bagian',
        'kode_perkiraan',
        'nama_perkiraan',
        'tanggal_pengajuan',
        'pemohon',
        'keperluan',
        'nominal_pengajuan',
        'nominal_acc',
        'catatan',
        'status',
        'approved_by',
        'dicairkan_oleh',
        'dicairkan_at',
    ];

    /**
     * Generator Nomor Bukti Pengeluaran (BP) Priangan
     * Format: BP-YYYYMM-XXXX
     */
    public static function generateNoBp(): string
    {
        $period = date('Ym');
        $prefix = "BP-{$period}-";
        $latest = self::where('no_bp', 'like', "{$prefix}%")
            ->orderByRaw('CAST(SUBSTRING(no_bp, -4) AS UNSIGNED) DESC')
            ->first();

        $nextSeq = 1;
        if ($latest) {
            $parts = explode('-', $latest->no_bp);
            $nextSeq = ((int) end($parts)) + 1;
        }

        do {
            $noBpSuggested = $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
            $nextSeq++;
        } while (self::where('no_bp', $noBpSuggested)->exists());

        return $noBpSuggested;
    }

    /**
     * Generator Nomor Register Anggaran (PR) Format DOS ANGGARAN.EXE
     * Format: PR-YYYYMMDD-XXXX
     */
    public static function generateNoRegister(): string
    {
        $ymd = date('Ymd');
        $prefix = "PR-{$ymd}-";
        $latest = self::where('no_register_anggaran', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('no_register_anggaran');

        $seq = 1;
        if ($latest && str_starts_with($latest, $prefix)) {
            $seq = intval(substr($latest, strlen($prefix))) + 1;
        }

        return $prefix . sprintf('%04d', $seq);
    }

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'nominal_pengajuan' => 'decimal:2',
        'nominal_acc'       => 'decimal:2',
        'dicairkan_at'      => 'datetime',
    ];

    /**
     * Master Definisi Lengkap 21 Bidang Resmi HU Kabar Priangan (DOS ANGGARAN.EXE)
     */
    public static function getMaster21Bidang(): array
    {
        return [
            '01' => [
                'kode'           => '01',
                'slug'           => 'iklan',
                'nama'           => 'Iklan',
                'nama_lengkap'   => '01 - Iklan',
                'deskripsi'      => 'Operasional penerimaan iklan display, adv, kolom, baris, event, dan komisi agen iklan.',
                'pos_beban'      => '6.00.01.01.01.02',
                'nama_pos'       => 'Beban Operasional Iklan',
                'pj'             => 'Manajer Iklan / Koordinator Sales',
                'default_plafon' => 15000000,
            ],
            '02' => [
                'kode'           => '02',
                'slug'           => 'sirkulasi',
                'nama'           => 'Sirkulasi',
                'nama_lengkap'   => '02 - Sirkulasi',
                'deskripsi'      => 'Distribusi koran harian, jatah agen koran, biaya ekspedisi luar kota, dan BBM armada sirkulasi.',
                'pos_beban'      => '6.00.01.00.01.01',
                'nama_pos'       => 'Beban Sirkulasi & Ekspedisi',
                'pj'             => 'Manajer Sirkulasi / Ekspedisi',
                'default_plafon' => 12000000,
            ],
            '03' => [
                'kode'           => '03',
                'slug'           => 'umum_sdm',
                'nama'           => 'Umum & SDM',
                'nama_lengkap'   => '03 - Umum & SDM',
                'deskripsi'      => 'Rumah tangga kantor, perlengkapan ATK, listrik/air/telp, kebersihan, perawatan gedung & kendaraan.',
                'pos_beban'      => '6.00.01.01.01.04',
                'nama_pos'       => 'Beban Umum & SDM / Perlengkapan RT',
                'pj'             => 'Kabag Umum & SDM',
                'default_plafon' => 18000000,
            ],
            '04' => [
                'kode'           => '04',
                'slug'           => 'akuntansi',
                'nama'           => 'Akuntansi',
                'nama_lengkap'   => '04 - Akuntansi',
                'deskripsi'      => 'Penyusunan laporan keuangan, pembukuan jurnal, konsultan audit eksternal, software akuntansi.',
                'pos_beban'      => '6.00.01.01.01.03',
                'nama_pos'       => 'Beban Administrasi Akuntansi',
                'pj'             => 'Kepala Bagian Akuntansi',
                'default_plafon' => 5000000,
            ],
            '05' => [
                'kode'           => '05',
                'slug'           => 'keuangan',
                'nama'           => 'Keuangan',
                'nama_lengkap'   => '05 - Keuangan',
                'deskripsi'      => 'Treasury, manajemen kas masuk/keluar, administrasi bank, pajak operasional, materai, dan penagihan inkaso.',
                'pos_beban'      => '6.00.01.01.01.02',
                'nama_pos'       => 'Beban Keuangan & Kasir',
                'pj'             => 'Manajer Keuangan',
                'default_plafon' => 8000000,
            ],
            '06' => [
                'kode'           => '06',
                'slug'           => 'redaksi',
                'nama'           => 'Redaksi',
                'nama_lengkap'   => '06 - Redaksi',
                'deskripsi'      => 'Biaya liputan harian wartawan, honor tulisan kontributor daerah, fotografer, dan perlengkapan berita.',
                'pos_beban'      => '5.00.01.01.02',
                'nama_pos'       => 'Beban Redaksi & Honor Wartawan',
                'pj'             => 'Pemimpin Redaksi / Redaktur',
                'default_plafon' => 25000000,
            ],
            '07' => [
                'kode'           => '07',
                'slug'           => 'pimpinan',
                'nama'           => 'Pimpinan',
                'nama_lengkap'   => '07 - Pimpinan',
                'deskripsi'      => 'Biaya perjalanan dinas direksi/pimpinan, rapat manajemen eksekutif, dan beban representasi.',
                'pos_beban'      => '6.00.01.01.01.00',
                'nama_pos'       => 'Beban Operasional Pimpinan',
                'pj'             => 'Direksi / Pimpinan Perusahaan',
                'default_plafon' => 10000000,
            ],
            '08' => [
                'kode'           => '08',
                'slug'           => 'tehnik_redaksi',
                'nama'           => 'Tehnik Redaksi',
                'nama_lengkap'   => '08 - Tehnik Redaksi',
                'deskripsi'      => 'Setting layout koran, tata artistik, scanning, separasi film, repro, proof kertas & cartridge, dan pracetak redaksi.',
                'pos_beban'      => '5.00.01.01.03',
                'nama_pos'       => 'Beban Tehnik Redaksi & Pracetak',
                'pj'             => 'Koordinator Artistik & Pracetak',
                'default_plafon' => 7500000,
            ],
            '09' => [
                'kode'           => '09',
                'slug'           => 'bank_penerbitan',
                'nama'           => 'Bank Penerbitan',
                'nama_lengkap'   => '09 - Bank Penerbitan',
                'deskripsi'      => 'Rekening giro/bank penerbitan resmi HU Kabar Priangan (BJB, BCA, Mandiri, BRI, BTN).',
                'pos_beban'      => '1.00.01.01.01',
                'nama_pos'       => 'Kas & Bank Penerbitan',
                'pj'             => 'Manajer Keuangan & Treasury',
                'default_plafon' => 50000000,
            ],
            '10' => [
                'kode'           => '10',
                'slug'           => 'pemasaran',
                'nama'           => 'Pemasaran',
                'nama_lengkap'   => '10 - Pemasaran',
                'deskripsi'      => 'Promosi koran, event kemitraan, expo/pameran, spanduk, billboard, neon box, souvenir, dan kemitraan biro iklan.',
                'pos_beban'      => '6.00.01.00.01.00',
                'nama_pos'       => 'Beban Pemasaran & Promosi',
                'pj'             => 'Manajer Promosi & Event',
                'default_plafon' => 8500000,
            ],
            '11' => [
                'kode'           => '11',
                'slug'           => 'litbang',
                'nama'           => 'Litbang',
                'nama_lengkap'   => '11 - Litbang',
                'deskripsi'      => 'Riset pembaca koran, survei oplah & pasar priangan timur, pengembangan produk baru penerbitan.',
                'pos_beban'      => '6.00.01.01.01.09',
                'nama_pos'       => 'Beban Penelitian & Pengembangan',
                'pj'             => 'Koordinator Litbang',
                'default_plafon' => 4500000,
            ],
            '12' => [
                'kode'           => '12',
                'slug'           => 'percetakan',
                'nama'           => 'Percetakan',
                'nama_lengkap'   => '12 - Percetakan',
                'deskripsi'      => 'Operasional mesin cetak, sparepart mesin web, jasa cetak luar / cetak komersil tabloid rekanan.',
                'pos_beban'      => '5.00.01.02.05',
                'nama_pos'       => 'Beban Percetakan & Maintenance Mesin',
                'pj'             => 'Kepala Unit Percetakan',
                'default_plafon' => 20000000,
            ],
            '13' => [
                'kode'           => '13',
                'slug'           => 'ekspedisi',
                'nama'           => 'Ekspedisi',
                'nama_lengkap'   => '13 - Ekspedisi',
                'deskripsi'      => 'Biaya ekspedisi lintas wilayah Ciamis, Banjar, Pangandaran, Garut, Bandung, dan sewa armada pengangkut.',
                'pos_beban'      => '6.00.01.00.01.06',
                'nama_pos'       => 'Beban Ekspedisi & Logistik Distribusi',
                'pj'             => 'Koordinator Ekspedisi',
                'default_plafon' => 11000000,
            ],
            '14' => [
                'kode'           => '14',
                'slug'           => 'beban_lain_usaha',
                'nama'           => 'Beban Lain-Lain Usaha',
                'nama_lengkap'   => '14 - Beban Lain-Lain Usaha',
                'deskripsi'      => 'Biaya management PT Berkah Pikiran Rakyat, pengeluaran lain-lain, setoran pendapatan PT BPR, pengeluaran portal grup.',
                'pos_beban'      => '6.00.01.01',
                'nama_pos'       => 'Beban Lain-Lain Usaha & Management Grup',
                'pj'             => 'Manajer Keuangan / Direksi',
                'default_plafon' => 10000000,
            ],
            '15' => [
                'kode'           => '15',
                'slug'           => 'kas_operasional',
                'nama'           => 'Kas Operasional',
                'nama_lengkap'   => '15 - Kas Operasional',
                'deskripsi'      => 'Kas kecil (petty cash) kasir loket, uang kembalian, dan cadangan likuiditas kas tunai harian.',
                'pos_beban'      => '1.00.01.01.01.01',
                'nama_pos'       => 'Kas Kecil & Operasional Kasir',
                'pj'             => 'Kasir Utama',
                'default_plafon' => 10000000,
            ],
            '16' => [
                'kode'           => '16',
                'slug'           => 'koreksi_kwitansi',
                'nama'           => 'Koreksi Kwitansi',
                'nama_lengkap'   => '16 - Koreksi Kwitansi',
                'deskripsi'      => 'Penyesuaian selisih pencatatan kwitansi, pembatalan/void kuitansi kasir, dan koreksi kas masuk.',
                'pos_beban'      => '2.00.01.02.01.00',
                'nama_pos'       => 'Penyesuaian & Koreksi Kwitansi',
                'pj'             => 'Supervisor Kasir & Akuntansi',
                'default_plafon' => 2000000,
            ],
            '17' => [
                'kode'           => '17',
                'slug'           => 'hutang_pajak',
                'nama'           => 'Hutang Pajak',
                'nama_lengkap'   => '17 - Hutang Pajak',
                'deskripsi'      => 'Penyetoran kewajiban pajak perusahaan (Hutang PPn Sirkulasi, Hutang PPn Iklan, Hutang PPh).',
                'pos_beban'      => '2.00.03.01.01',
                'nama_pos'       => 'Kewajiban / Hutang Pajak',
                'pj'             => 'Staf Perpajakan & Keuangan',
                'default_plafon' => 12000000,
            ],
            '18' => [
                'kode'           => '18',
                'slug'           => 'hutang_hubungan_usaha',
                'nama'           => 'Hutang Hubungan Usaha',
                'nama_lengkap'   => '18 - Hutang Hubungan Usaha',
                'deskripsi'      => 'Hutang usaha penerbitan, biaya pemakaian kertas koran, dan ongkos cetak percetakan.',
                'pos_beban'      => '2.00.00.01.01',
                'nama_pos'       => 'Hutang Hubungan Usaha & Ongkos Cetak',
                'pj'             => 'Manajer Percetakan & Akuntansi',
                'default_plafon' => 40000000,
            ],
            '19' => [
                'kode'           => '19',
                'slug'           => 'hutang_afiliasi',
                'nama'           => 'Hutang Afiliasi',
                'nama_lengkap'   => '19 - Hutang Afiliasi',
                'deskripsi'      => 'Transaksi rekening koran hutang antar entitas afiliasi PT Berkah Pikiran Rakyat (Hutang Afiliasi SK PRI).',
                'pos_beban'      => '2.00.03.01.01',
                'nama_pos'       => 'Hutang Antar Perusahaan Afiliasi',
                'pj'             => 'Direktur Keuangan / Holding',
                'default_plafon' => 15000000,
            ],
            '20' => [
                'kode'           => '20',
                'slug'           => 'sekretariat',
                'nama'           => 'Sekretariat',
                'nama_lengkap'   => '20 - Sekretariat',
                'deskripsi'      => 'Administrasi kesekretariatan resmi, gaji staf, dinas, perlengkapan kantor, surat menyurat, dan jamuan rapat.',
                'pos_beban'      => '6.00.01.01.01.05',
                'nama_pos'       => 'Beban Kesekretariatan Perusahaan',
                'pj'             => 'Sekretaris Perusahaan',
                'default_plafon' => 8000000,
            ],
            '21' => [
                'kode'           => '21',
                'slug'           => 'on_line',
                'nama'           => 'On Line',
                'nama_lengkap'   => '21 - On Line',
                'deskripsi'      => 'Operasional portal digital kabar-priangan.com, gaji staf digital, insentif iklan online, cash discount, biaya web.',
                'pos_beban'      => '6.00.01.00.01.02',
                'nama_pos'       => 'Beban Operasional Portal Online & Digital',
                'pj'             => 'Manajer Digital Media & IT',
                'default_plafon' => 12000000,
            ],
        ];
    }

    /**
     * Daftar 21 Bidang Resmi Kabar Priangan (Sesuai Menu Tabel > Bidang DOS ANGGARAN.EXE)
     */
    public static function getDaftarBidang(): array
    {
        $res = [];
        foreach (self::getMaster21Bidang() as $k => $item) {
            $res[$k] = $item['nama_lengkap'];
        }
        return $res;
    }

    /**
     * Daftar Bagan Akun Perkiraan (COA) per Bidang (Sesuai Dokumen Fisik Dot-Matrix)
     */
    public static function getCoaPerBidang(string $kodeBagian): array
    {
        // Prioritaskan dari database master_perkiraans jika tersedia
        try {
            $dbCoas = MasterPerkiraan::where('kode_bagian', $kodeBagian)
                ->where('is_active', true)
                ->orderBy('kode', 'asc')
                ->get();

            if ($dbCoas->isNotEmpty()) {
                return $dbCoas->map(function ($c) {
                    return [
                        'kode'    => $c->kode,
                        'nama'    => $c->nama,
                        'alokasi' => (float) $c->default_alokasi,
                    ];
                })->all();
            }
        } catch (\Throwable $e) {
            // Fallback jika database belum termigrasi
        }

        if ($kodeBagian === '01') {
            return [
                ['kode' => '1.00.04.01.01.01',          'nama' => 'IKLAN SK.PRI',                     'alokasi' => 2000000],
                ['kode' => '1.00.04.01.01.02',          'nama' => 'IKLAN ONLINE',                     'alokasi' => 1500000],
                ['kode' => '2.00.01.01.01.01',          'nama' => 'HUTANG IKLAN',                     'alokasi' => 0],
                ['kode' => '2.00.01.01.01.02',          'nama' => 'HUTANG IKLAN ONLINE',              'alokasi' => 0],
                ['kode' => '6.00.01.00.01.02.00.00',    'nama' => 'Gaji / Honor tetap',               'alokasi' => 4500000],
                ['kode' => '6.00.01.00.01.02.00.01',    'nama' => 'Honor pegawai non struktural',     'alokasi' => 1200000],
                ['kode' => '6.00.01.00.01.02.00.02.00', 'nama' => 'Tunj. Uang Makan',                 'alokasi' => 850000],
                ['kode' => '6.00.01.00.01.02.00.02.01', 'nama' => 'Tunj. Transportasi',               'alokasi' => 750000],
                ['kode' => '6.00.01.00.01.02.00.02.02', 'nama' => 'Tunj. Kesehatan',                  'alokasi' => 450000],
                ['kode' => '6.00.01.00.01.02.00.02.03', 'nama' => 'Piket / Kelebihan jam kerja',      'alokasi' => 300000],
                ['kode' => '6.00.01.00.01.02.00.02.04', 'nama' => 'Tunjangan Hari Raya',              'alokasi' => 0],
                ['kode' => '6.00.01.00.01.02.00.02.05', 'nama' => 'TUNJANGAN JABATAN',                'alokasi' => 500000],
                ['kode' => '6.00.01.00.01.02.00.02.06', 'nama' => 'Tunjangan Natura',                 'alokasi' => 250000],
                ['kode' => '6.00.01.00.01.02.01.00',    'nama' => 'Dinas',                            'alokasi' => 600000],
                ['kode' => '6.00.01.00.01.02.01.00.00', 'nama' => 'Uang Saku Dinas',                  'alokasi' => 350000],
                ['kode' => '6.00.01.00.01.02.01.00.01', 'nama' => 'BBM / Transportasi Dinas',         'alokasi' => 400000],
                ['kode' => '6.00.01.00.01.02.01.00.02', 'nama' => 'Hotel / Akomodasi Dinas',          'alokasi' => 500000],
                ['kode' => '6.00.01.00.01.02.01.01.00', 'nama' => 'Sewa',                             'alokasi' => 0],
                ['kode' => '6.00.01.00.01.02.01.01.01', 'nama' => 'BBM',                              'alokasi' => 300000],
                ['kode' => '6.00.01.00.01.02.01.01.02', 'nama' => 'Service / Suku Cadang / Oli',       'alokasi' => 200000],
                ['kode' => '6.00.01.00.01.02.01.02.00', 'nama' => 'Perlengkapan kantor / ATK',         'alokasi' => 150000],
                ['kode' => '6.00.01.00.01.02.01.02.01', 'nama' => 'Surat menyurat / Fotokopi',         'alokasi' => 50000],
                ['kode' => '6.00.01.00.01.02.01.02.02', 'nama' => 'Jamuan Rapat / Relasi',             'alokasi' => 150000],
                ['kode' => '6.00.01.00.01.02.01.02.03', 'nama' => 'Telepon / Handphone / Pager',      'alokasi' => 100000],
                ['kode' => '6.00.01.00.01.02.01.02.04', 'nama' => 'Bantuan Konsumsi',                 'alokasi' => 50000],
                ['kode' => '6.00.01.00.01.02.01.02.05', 'nama' => 'Bantuan Mobilitas',                'alokasi' => 50000],
            ];
        }

        $master = self::getMaster21Bidang()[$kodeBagian] ?? null;
        $basePos = $master['pos_beban'] ?? '6.00.01.00.01.00';
        $namaPos = $master['nama_pos'] ?? 'Beban Operasional';

        return [
            ['kode' => $basePos . '.00',    'nama' => 'Gaji / Honor Staf',             'alokasi' => 4000000],
            ['kode' => $basePos . '.01',    'nama' => 'Tunjangan Uang Makan & Trans',   'alokasi' => 1200000],
            ['kode' => $basePos . '.02',    'nama' => 'Operasional ' . ($master['nama'] ?? 'Bagian'), 'alokasi' => 2500000],
            ['kode' => $basePos . '.03',    'nama' => 'Perlengkapan & ATK Kerja',       'alokasi' => 800000],
            ['kode' => $basePos . '.04',    'nama' => 'Perjalanan Dinas & Transport',   'alokasi' => 1500000],
            ['kode' => $basePos . '.05',    'nama' => 'Biaya Konsumsi & Rapat Kerja',   'alokasi' => 500000],
            ['kode' => $basePos . '.99',    'nama' => 'Beban Lain-Lain ' . ($master['nama'] ?? ''), 'alokasi' => 500000],
        ];
    }

    /**
     * Rekening Bank Resmi Priangan (Standar DOS ANGGARAN.EXE)
     */
    public static function getDaftarRekeningBank(): array
    {
        return [
            [
                'bank_nama'       => 'Bank BJB (Bank Pembangunan Daerah Jabar Banten)',
                'cabang'          => 'KCP Tasikmalaya Sutisna Senjaya',
                'no_rekening'     => '001-023-456789-0',
                'atas_nama'       => 'PT BERKAH PIKIRAN RAKYAT (KABAR PRIANGAN)',
                'tipe_rekening'   => 'Giro Operasional Utama',
                'fungsi_utama'    => 'Pencairan Kasir & Pengeluaran BP Priangan, Rekening Koran BJB',
                'kode_coa'        => '1.00.02.01.01.01',
                'status'          => 'Aktif (Utama)',
            ],
            [
                'bank_nama'       => 'Bank Central Asia (BCA)',
                'cabang'          => 'KCU Tasikmalaya',
                'no_rekening'     => '054-089-1234',
                'atas_nama'       => 'PT BERKAH PIKIRAN RAKYAT',
                'tipe_rekening'   => 'Giro Bisnis Korporat',
                'fungsi_utama'    => 'Penerimaan Pembayaran Faktur Iklan Nasional & Langganan',
                'kode_coa'        => '1.00.02.01.01.02',
                'status'          => 'Aktif',
            ],
            [
                'bank_nama'       => 'Bank Mandiri (Persero)',
                'cabang'          => 'KC Tasikmalaya Otto Iskandardinata',
                'no_rekening'     => '131-00-9876543-2',
                'atas_nama'       => 'PT BERKAH PIKIRAN RAKYAT',
                'tipe_rekening'   => 'Giro Operasional Cadangan',
                'fungsi_utama'    => 'Operasional Khusus Penggajian (Payroll) & Pajak Perusahaan',
                'kode_coa'        => '1.00.02.01.01.03',
                'status'          => 'Aktif',
            ],
            [
                'bank_nama'       => 'Bank Rakyat Indonesia (BRI)',
                'cabang'          => 'Kanca Tasikmalaya',
                'no_rekening'     => '0100-01-001234-50-8',
                'atas_nama'       => 'PT BERKAH PIKIRAN RAKYAT',
                'tipe_rekening'   => 'BritAma Bisnis',
                'fungsi_utama'    => 'Penerimaan Setoran Koran dari Agen Sirkulasi Daerah Priangan Timur',
                'kode_coa'        => '1.00.02.01.01.04',
                'status'          => 'Aktif',
            ],
        ];
    }

    /**
     * Relasi ke pengajuan anggaran asli di divisi keuangan
     */
    public function pengajuanAnggaran(): BelongsTo
    {
        return $this->belongsTo(PengajuanAnggaran::class, 'pengajuan_anggaran_id');
    }

    /**
     * Bukti Kas Keluar (BKK) kasir setelah dicairkan
     */
    public function kasKeluar(): HasOne
    {
        return $this->hasOne(KasKeluar::class, 'bp_id');
    }

    /**
     * Pejabat yang menyetujui anggaran (Keuangan / Anggaran)
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Petugas kasir yang mencairkan kas
     */
    public function disburser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicairkan_oleh');
    }

    /**
     * Helper status badge
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'approved' => [
                'bg'    => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'label' => 'Approved (Siap Cair)',
                'icon'  => 'check-circle',
            ],
            'dicairkan_kasir' => [
                'bg'    => 'bg-slate-100 text-slate-700 border-slate-300',
                'label' => 'Sudah Dicairkan',
                'icon'  => 'archive',
            ],
            'rejected' => [
                'bg'    => 'bg-rose-50 text-rose-700 border-rose-200',
                'label' => 'Ditolak',
                'icon'  => 'x-circle',
            ],
            default => [
                'bg'    => 'bg-amber-50 text-amber-700 border-amber-200',
                'label' => 'Pending Approval',
                'icon'  => 'clock',
            ],
        };
    }
}
