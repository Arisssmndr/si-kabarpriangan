<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPerkiraan extends Model
{
    use HasFactory;

    protected $table = 'master_perkiraans';

    protected $fillable = [
        'kode',
        'nama',
        'kode_bagian',
        'bidang',
        'kategori',
        'tipe',
        'keterangan',
        'default_alokasi',
        'is_active',
    ];

    protected $casts = [
        'default_alokasi' => 'decimal:2',
        'is_active'       => 'boolean',
    ];

    /**
     * Hitung total transaksi dan rupiah pemakaian akun COA ini pada Bukti Pengeluaran (BP)
     */
    public function getStatistikPenggunaanAttribute(): array
    {
        $bps = BuktiPengeluaran::where('kode_perkiraan', $this->kode)->get();
        return [
            'total_bp'     => $bps->count(),
            'total_rupiah' => (float) $bps->whereIn('status', ['approved', 'dicairkan_kasir'])->sum('nominal_acc'),
        ];
    }

    /**
     * Seed data awal standar COA jika tabel masih kosong
     */
    public static function seedDefaultData(): void
    {
        if (self::count() > 0) {
            return;
        }

        $defaultList = [
            // --- BIDANG 01: IKLAN ---
            [
                'kode'            => '1.00.04.01.01.01',
                'nama'            => 'IKLAN SK.PRI',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Piutang Iklan',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Piutang order pemasangan iklan surat kabar Priangan cetak.',
                'default_alokasi' => 2000000,
            ],
            [
                'kode'            => '1.00.04.01.01.02',
                'nama'            => 'IKLAN ONLINE',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Piutang Iklan',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Piutang pemasangan banner & advetorial portal kabar-priangan.com.',
                'default_alokasi' => 1500000,
            ],
            [
                'kode'            => '2.00.01.01.01.01',
                'nama'            => 'HUTANG IKLAN',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Hutang Titipan',
                'tipe'            => 'KREDIT',
                'keterangan'      => 'Titipan uang muka order iklan tempo belum tayang.',
                'default_alokasi' => 0,
            ],
            [
                'kode'            => '2.00.01.01.01.02',
                'nama'            => 'HUTANG IKLAN ONLINE',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Hutang Titipan',
                'tipe'            => 'KREDIT',
                'keterangan'      => 'Titipan uang muka paket iklan digital.',
                'default_alokasi' => 0,
            ],
            [
                'kode'            => '6.00.01.00.01.02.00.00',
                'nama'            => 'Gaji / Honor tetap',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Beban SDM',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Gaji pokok staf divisi iklan.',
                'default_alokasi' => 4500000,
            ],
            [
                'kode'            => '6.00.01.00.01.02.00.01',
                'nama'            => 'Honor pegawai non struktural',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Beban SDM',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Honor sales lapangan / freelance iklan.',
                'default_alokasi' => 1200000,
            ],
            [
                'kode'            => '6.00.01.00.01.02.00.02.00',
                'nama'            => 'Tunj. Uang Makan',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Tunjangan',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Tunjangan uang makan staf iklan.',
                'default_alokasi' => 850000,
            ],
            [
                'kode'            => '6.00.01.00.01.02.00.02.01',
                'nama'            => 'Tunj. Transportasi',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Tunjangan',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Tunjangan transportasi operasional sales.',
                'default_alokasi' => 750000,
            ],
            [
                'kode'            => '6.00.01.00.01.02.00.02.02',
                'nama'            => 'Tunj. Kesehatan',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Tunjangan',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Bantuan biaya kesehatan staf.',
                'default_alokasi' => 450000,
            ],
            [
                'kode'            => '6.00.01.00.01.02.00.02.03',
                'nama'            => 'Piket / Kelebihan jam kerja',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Lembur',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Uang lembur deadline closing edisi cetak.',
                'default_alokasi' => 300000,
            ],
            [
                'kode'            => '6.00.01.00.01.02.00.02.04',
                'nama'            => 'Tunjangan Hari Raya',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Tunjangan',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Alokasi THR divisi iklan.',
                'default_alokasi' => 0,
            ],
            [
                'kode'            => '6.00.01.00.01.02.00.02.05',
                'nama'            => 'TUNJANGAN JABATAN',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Tunjangan',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Tunjangan jabatan manajer & koordinator iklan.',
                'default_alokasi' => 500000,
            ],
            [
                'kode'            => '6.00.01.00.01.02.00.02.06',
                'nama'            => 'Tunjangan Natura',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Tunjangan',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Tunjangan natura beras / sembako bulanan.',
                'default_alokasi' => 250000,
            ],
            [
                'kode'            => '6.00.01.00.01.02.01.00',
                'nama'            => 'Dinas',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Operasional',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Beban dinas luar kota dan kunjungan klien.',
                'default_alokasi' => 600000,
            ],
            [
                'kode'            => '6.00.01.00.01.02.01.00.00',
                'nama'            => 'Uang Saku Dinas',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Operasional',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Uang saku perjalanan dinas staf iklan.',
                'default_alokasi' => 350000,
            ],
            [
                'kode'            => '6.00.01.00.01.02.01.00.01',
                'nama'            => 'BBM / Transportasi Dinas',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'BBM & Armada',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'BBM operasional dinas dan tol.',
                'default_alokasi' => 400000,
            ],
            [
                'kode'            => '6.00.01.00.01.02.01.00.02',
                'nama'            => 'Hotel / Akomodasi Dinas',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Akomodasi',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Penginapan hotel tugas dinas luar kota.',
                'default_alokasi' => 500000,
            ],
            [
                'kode'            => '6.00.01.00.01.02.01.01.01',
                'nama'            => 'BBM',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'BBM & Armada',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'BBM harian kendaraan dinas sales iklan.',
                'default_alokasi' => 300000,
            ],
            [
                'kode'            => '6.00.01.00.01.02.01.01.02',
                'nama'            => 'Service / Suku Cadang / Oli',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Pemeliharaan',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Servis motor/mobil operasional iklan.',
                'default_alokasi' => 200000,
            ],
            [
                'kode'            => '6.00.01.00.01.02.01.02.00',
                'nama'            => 'Perlengkapan kantor / ATK',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'ATK & Kantor',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Kertas HVS, map order iklan, ballpoint, toner.',
                'default_alokasi' => 150000,
            ],
            [
                'kode'            => '6.00.01.00.01.02.01.02.01',
                'nama'            => 'Surat menyurat / Fotokopi',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'ATK & Kantor',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Pengiriman invoice fisik, materai & fotokopi berkas.',
                'default_alokasi' => 50000,
            ],
            [
                'kode'            => '6.00.01.00.01.02.01.02.02',
                'nama'            => 'Jamuan Rapat / Relasi',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Representasi',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Konsumsi pertemuan klien iklan & rapat edisi khusus.',
                'default_alokasi' => 150000,
            ],
            [
                'kode'            => '6.00.01.00.01.02.01.02.03',
                'nama'            => 'Telepon / Handphone / Pager',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Utilitas',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Paket data / pulsa koordinasi sales iklan.',
                'default_alokasi' => 100000,
            ],
            [
                'kode'            => '6.00.01.00.01.02.01.02.04',
                'nama'            => 'Bantuan Konsumsi',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Konsumsi',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Konsumsi staf saat lembur deadline iklan.',
                'default_alokasi' => 50000,
            ],
            [
                'kode'            => '6.00.01.00.01.02.01.02.05',
                'nama'            => 'Bantuan Mobilitas',
                'kode_bagian'     => '01',
                'bidang'          => '01 - Iklan',
                'kategori'        => 'Transportasi',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Bantuan mobilitas mendesak penagihan invoice iklan.',
                'default_alokasi' => 50000,
            ],

            // --- BIDANG 02: SIRKULASI ---
            [
                'kode'            => '6.00.01.00.01.04.00',
                'nama'            => 'Gaji / Honor Sirkulasi',
                'kode_bagian'     => '02',
                'bidang'          => '02 - Sirkulasi',
                'kategori'        => 'Beban SDM',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Gaji pokok staf divisi sirkulasi koran.',
                'default_alokasi' => 3500000,
            ],
            [
                'kode'            => '6.00.01.00.01.04.01',
                'nama'            => 'BBM Armada Distribusi Koran',
                'kode_bagian'     => '02',
                'bidang'          => '02 - Sirkulasi',
                'kategori'        => 'BBM & Armada',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'BBM armada mobil/motor pengantar koran subuh.',
                'default_alokasi' => 2000000,
            ],

            // --- BIDANG 03: UMUM & SDM ---
            [
                'kode'            => '6.00.01.00.01.04.02',
                'nama'            => 'Listrik, Air & Telepon Gedung',
                'kode_bagian'     => '03',
                'bidang'          => '03 - Umum & SDM',
                'kategori'        => 'Utilitas',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Tagihan PLN, PDAM, dan telepon kantor pusat Priangan.',
                'default_alokasi' => 4500000,
            ],

            // --- BIDANG 06: REDAKSI ---
            [
                'kode'            => '5.00.01.01.02.00',
                'nama'            => 'Honor Wartawan & Kontributor',
                'kode_bagian'     => '06',
                'bidang'          => '06 - Redaksi',
                'kategori'        => 'Beban Produksi',
                'tipe'            => 'DEBIT',
                'keterangan'      => 'Honorarium berita wartawan daerah Ciamis, Banjar, Garut, Pangandaran.',
                'default_alokasi' => 12000000,
            ],
        ];

        foreach ($defaultList as $item) {
            self::updateOrCreate(
                ['kode' => $item['kode']],
                $item
            );
        }
    }
}
