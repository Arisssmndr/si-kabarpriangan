<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SirkulasiController extends Controller
{
    /**
     * Helper 8 Wilayah Pemasaran (Sistem DOS Asli SIRK.EXE)
     * 01 Tasikmalaya, 02 Garut, 03 Ciamis, 04 Banjar, 05 Sumedang, 06 Gratis, 07 Bandung, 08 Insidentil
     */
    public function getDaftarWilayah(): Collection
    {
        return collect([
            (object)[
                'kode' => '01',
                'nama' => 'Tasikmalaya',
                'keterangan' => 'Wilayah Distribusi Kota & Kab. Tasikmalaya',
                'total_agen' => 38,
                'alokasi_default' => 5200,
                'tipe' => 'Komersil',
            ],
            (object)[
                'kode' => '02',
                'nama' => 'Garut',
                'keterangan' => 'Wilayah Distribusi Kab. Garut (Kota & Garut Selatan)',
                'total_agen' => 22,
                'alokasi_default' => 2400,
                'tipe' => 'Komersil',
            ],
            (object)[
                'kode' => '03',
                'nama' => 'Ciamis',
                'keterangan' => 'Wilayah Distribusi Kab. Ciamis & Panjalu',
                'total_agen' => 26,
                'alokasi_default' => 3100,
                'tipe' => 'Komersil',
            ],
            (object)[
                'kode' => '04',
                'nama' => 'Banjar & Pangandaran',
                'keterangan' => 'Wilayah Distribusi Kota Banjar & Pesisir Pangandaran',
                'total_agen' => 19,
                'alokasi_default' => 2100,
                'tipe' => 'Komersil',
            ],
            (object)[
                'kode' => '05',
                'nama' => 'Sumedang & Sekitarnya',
                'keterangan' => 'Wilayah Distribusi Kab. Sumedang, Majalengka, Kuningan',
                'total_agen' => 14,
                'alokasi_default' => 1400,
                'tipe' => 'Komersil',
            ],
            (object)[
                'kode' => '06',
                'nama' => 'Gratis / Kuota Promo',
                'keterangan' => 'Sampel Bukti Iklan, Redaksi, Arsip Legal, Forkopimda & Mitra Dinas',
                'total_agen' => 6,
                'alokasi_default' => 450,
                'tipe' => 'Non-Komersil (Gratis)',
            ],
            (object)[
                'kode' => '07',
                'nama' => 'Bandung Raya',
                'keterangan' => 'Perwakilan Bandung, Kios Stasiun & Jalur Cileunyi',
                'total_agen' => 11,
                'alokasi_default' => 1250,
                'tipe' => 'Komersil',
            ],
            (object)[
                'kode' => '08',
                'nama' => 'Insidentil & Even Khusus',
                'keterangan' => 'Pesanan Eksemplar Tambahan Suplemen, Acara Pemda & Kegiatan Akbar',
                'total_agen' => 4,
                'alokasi_default' => 600,
                'tipe' => 'Insidentil',
            ],
        ]);
    }

    /**
     * Helper Standar Pengepakan (01 Bagian Sirkulasi & Variasi Kemasan)
     */
    public function getDaftarPengepakan(): Collection
    {
        return collect([
            (object)[
                'kode' => '01',
                'unit' => 'Bendel Standar Agen (50 Eks)',
                'kategori' => 'Distribusi Reguler',
                'standar_bendel' => '50 Eks / Ikat',
                'toleransi_ikat' => 'Maks. 50 Eks per Ikat Tali Rafia Silang Ganda Kencang',
                'waktu_muat' => '01:30 - 02:30 WIB',
                'pic_checker' => 'Koordinator Ekspedisi Gudang',
                'keterangan' => 'Standar wajib untuk drop point agen besar dan sub agen.',
                'status' => 'Standar Utama',
                'badge_color' => 'emerald',
            ],
            (object)[
                'kode' => '02',
                'unit' => 'Koran Lepas & Pecahan Eceran',
                'kategori' => 'Sisa Jatah Agen',
                'standar_bendel' => '1 s.d. 49 Eks',
                'toleransi_ikat' => 'Label pita kertas khusus mencantumkan sisa pesanan per agen',
                'waktu_muat' => '02:00 - 02:45 WIB',
                'pic_checker' => 'Tim Checker & Sortir Sisa',
                'keterangan' => 'Untuk pesanan agen yang bukan kelipatan 50 eksemplar.',
                'status' => 'Pecahan Jatah',
                'badge_color' => 'sky',
            ],
            (object)[
                'kode' => '03',
                'unit' => 'Ekspedisi Jarak Jauh (Waterproof)',
                'kategori' => 'Tahan Cuaca / Hujan',
                'standar_bendel' => '50 / 100 Eks',
                'toleransi_ikat' => 'Bungkus plastik tebal kedap air anti-hujan & debu jalanan',
                'waktu_muat' => '01:00 - 02:00 WIB',
                'pic_checker' => 'Petugas Ekspedisi Luar Kota',
                'keterangan' => 'Prioritas rute luar kota dan pengiriman jarak jauh.',
                'status' => 'Proteksi Cuaca',
                'badge_color' => 'amber',
            ],
            (object)[
                'kode' => '04',
                'unit' => 'Langganan Khusus / Instansi',
                'kategori' => 'Home Delivery / Kantor',
                'standar_bendel' => 'Per Paket Alamat',
                'toleransi_ikat' => 'Kantong transparan berstempel alamat dinas / instansi pemda',
                'waktu_muat' => '02:30 - 03:30 WIB',
                'pic_checker' => 'Petugas Loper Pengantar',
                'keterangan' => 'Pelanggan korporat, kantor dinas, bank, dan perhotelan.',
                'status' => 'Langganan Tetap',
                'badge_color' => 'purple',
            ],
            (object)[
                'kode' => '05',
                'unit' => 'Bukti Muat Iklan & Arsip',
                'kategori' => 'Internal & Rekanan',
                'standar_bendel' => '10 s.d. 25 Eks',
                'toleransi_ikat' => 'Stempel khusus Bukti Muat Iklan Priangan & arsip perpustakaan',
                'waktu_muat' => '07:00 - 08:30 WIB',
                'pic_checker' => 'Staf Administrasi Sirkulasi',
                'keterangan' => 'Voucher bukti tayang pemasang iklan dan arsip redaksi.',
                'status' => 'Internal & Iklan',
                'badge_color' => 'slate',
            ],
        ]);
    }

    /**
     * Helper 3 Jenis Agen SIRK.EXE:
     * - 00: Agen Non Aktif (Soft delete / filter, dilarang hard delete untuk akuntansi)
     * - 11: Agen Besar
     * - 99: Sub Agen
     */
    public function getDaftarJenisAgen(): Collection
    {
        return collect([
            (object)[
                'kode' => '00',
                'nama' => 'Agen Non Aktif',
                'label' => '00 - Agen Non Aktif',
                'harga_satuan' => 0,
                'ketentuan' => 'Terkunci dari alokasi jatah harian. Data mutlak dipertahankan (soft delete/filter) untuk audit kartu piutang buku besar akuntansi.',
                'sistem_distribusi' => 'Dihentikan / Non-Aktif',
                'badge_color' => 'rose',
                'status_aturan' => 'Dilarang Hard Delete',
            ],
            (object)[
                'kode' => '11',
                'nama' => 'Agen Besar',
                'label' => '11 - Agen Besar',
                'harga_satuan' => 2800,
                'ketentuan' => 'Mitra distribusi langsung drop point mobil subuh. Induk pembina jaringan sub agen dan loper kios.',
                'sistem_distribusi' => 'Drop Point Distribusi Utama (Jatah > 300 eks)',
                'badge_color' => 'blue',
                'status_aturan' => 'Komersil Utama',
            ],
            (object)[
                'kode' => '99',
                'nama' => 'Sub Agen / Pengecer',
                'label' => '99 - Sub Agen',
                'harga_satuan' => 3000,
                'ketentuan' => 'Pengecer, kios pasar, loper keliling di bawah koordinasi Agen Besar atau drop rute sepeda motor.',
                'sistem_distribusi' => 'Eceran & Loper Kios (Konsinyasi Rp3.000)',
                'badge_color' => 'emerald',
                'status_aturan' => 'Pengecer Resmi',
            ],
        ]);
    }

    /**
     * Helper 15 Jurusan Armada Mobil Distribusi Kabar Priangan (01 s.d. 15 Rute)
     */
    public function getDaftarJurusan(): Collection
    {
        return collect([
            (object)[
                'kode' => '01',
                'kode_jurusan' => '01',
                'nama_jurusan' => 'Ciamis - Pangandaran',
                'armada' => 'Mobil L300 Box 01 (Z 8344 KC)',
                'sopir' => 'Pak Tatang Supriatna',
                'jam_berangkat' => '02:00 WIB',
                'titik_tujuan' => 'Ciamis, Banjarsari, Kalipucang, Pangandaran, Parigi, Cijulang',
                'total_agen' => 16,
                'alokasi_oplah' => 2200,
                'status' => 'Siap Jalan',
            ],
            (object)[
                'kode' => '02',
                'kode_jurusan' => '02',
                'nama_jurusan' => 'Tasikmalaya - Cipatujah',
                'armada' => 'Mobil L300 Box 02 (Z 8190 HN)',
                'sopir' => 'Pak Dedi Sutisna',
                'jam_berangkat' => '02:15 WIB',
                'titik_tujuan' => 'Sukawening, Karangnunggal, Cibalong, Pamijahan, Cipatujah',
                'total_agen' => 14,
                'alokasi_oplah' => 1650,
                'status' => 'Siap Jalan',
            ],
            (object)[
                'kode' => '03',
                'kode_jurusan' => '03',
                'nama_jurusan' => 'Garut - Pameungpeuk',
                'armada' => 'Mobil Gran Max Box 03 (Z 8621 DA)',
                'sopir' => 'Kang Agus Mulyadi',
                'jam_berangkat' => '02:15 WIB',
                'titik_tujuan' => 'Tarogong, Garut Kota, Bayongbong, Cikajang, Cisurupan, Pameungpeuk',
                'total_agen' => 15,
                'alokasi_oplah' => 1950,
                'status' => 'Siap Jalan',
            ],
            (object)[
                'kode' => '04',
                'kode_jurusan' => '04',
                'nama_jurusan' => 'NPTS Bandung - Panjalu',
                'armada' => 'Mobil Carry Box 04 (Z 8812 HP)',
                'sopir' => 'Kang Asep Kurniawan',
                'jam_berangkat' => '02:30 WIB',
                'titik_tujuan' => 'Rajapolah, Jamanis, Cihaurbeuti, Sukamantri, Situ Panjalu',
                'total_agen' => 12,
                'alokasi_oplah' => 1400,
                'status' => 'Siap Jalan',
            ],
            (object)[
                'kode' => '05',
                'kode_jurusan' => '05',
                'nama_jurusan' => 'Bandung - Cirebon',
                'armada' => 'Mobil Isuzu Elf 05 (Z 7720 EA)',
                'sopir' => 'Pak Hendra Gunawan',
                'jam_berangkat' => '01:45 WIB',
                'titik_tujuan' => 'Cileunyi, Sumedang, Tomo, Kadipaten, Majalengka, Sumber Cirebon',
                'total_agen' => 13,
                'alokasi_oplah' => 1550,
                'status' => 'Siap Jalan',
            ],
            (object)[
                'kode' => '06',
                'kode_jurusan' => '06',
                'nama_jurusan' => 'Tasikmalaya Kota - Singaparna',
                'armada' => 'Mobil Carry Box 06 (Z 8421 HB)',
                'sopir' => 'Kang Iwan Ridwan',
                'jam_berangkat' => '03:00 WIB',
                'titik_tujuan' => 'Mitra Batik, Pasar Wetan, Cihideung, Alun-alun Singaparna, Kudang',
                'total_agen' => 24,
                'alokasi_oplah' => 3800,
                'status' => 'Siap Jalan',
            ],
            (object)[
                'kode' => '07',
                'kode_jurusan' => '07',
                'nama_jurusan' => 'Ciamis Kota - Kawali',
                'armada' => 'Mobil Luxio Box 07 (Z 1450 TA)',
                'sopir' => 'Kang Yana Suryana',
                'jam_berangkat' => '02:45 WIB',
                'titik_tujuan' => 'Alun-alun Ciamis, Pasar Manis, Baregbeg, Kawali, Lumbung',
                'total_agen' => 17,
                'alokasi_oplah' => 2100,
                'status' => 'Siap Jalan',
            ],
            (object)[
                'kode' => '08',
                'kode_jurusan' => '08',
                'nama_jurusan' => 'Banjar Patroman - Banjarsari',
                'armada' => 'Mobil Carry Box 08 (Z 8091 KA)',
                'sopir' => 'Pak Nanang Sumarna',
                'jam_berangkat' => '02:30 WIB',
                'titik_tujuan' => 'Terminal Banjar, Langensari, Pataruman, Pamarican, Banjarsari',
                'total_agen' => 11,
                'alokasi_oplah' => 1300,
                'status' => 'Siap Jalan',
            ],
            (object)[
                'kode' => '09',
                'kode_jurusan' => '09',
                'nama_jurusan' => 'Malangbong - Garut Kota',
                'armada' => 'Mobil Gran Max 09 (Z 8234 DG)',
                'sopir' => 'Kang Cecep Solihin',
                'jam_berangkat' => '02:20 WIB',
                'titik_tujuan' => 'Gentong, Malangbong, Bandrek, Cibatu, Wanaraja, Garut Kota',
                'total_agen' => 12,
                'alokasi_oplah' => 1250,
                'status' => 'Siap Jalan',
            ],
            (object)[
                'kode' => '10',
                'kode_jurusan' => '10',
                'nama_jurusan' => 'Rajapolah - Cihaurbeuti - Panjalu',
                'armada' => 'Mobil Carry Box 10 (Z 8555 TA)',
                'sopir' => 'Pak Oding Rohidin',
                'jam_berangkat' => '03:15 WIB',
                'titik_tujuan' => 'Pasar Rajapolah, Ciawi, Sukamantri, Alun-alun Panjalu',
                'total_agen' => 10,
                'alokasi_oplah' => 950,
                'status' => 'Siap Jalan',
            ],
            (object)[
                'kode' => '11',
                'kode_jurusan' => '11',
                'nama_jurusan' => 'Karangnunggal - Cibalong',
                'armada' => 'Mobil L300 Box 11 (Z 8667 HP)',
                'sopir' => 'Kang Ujang Mulyana',
                'jam_berangkat' => '02:10 WIB',
                'titik_tujuan' => 'Simpang, Karangnunggal, Bantarkalong, Cipatujah Pesisir',
                'total_agen' => 9,
                'alokasi_oplah' => 850,
                'status' => 'Siap Jalan',
            ],
            (object)[
                'kode' => '12',
                'kode_jurusan' => '12',
                'nama_jurusan' => 'Sumedang - Wado - Darmaraja',
                'armada' => 'Mobil Gran Max 12 (Z 8112 SD)',
                'sopir' => 'Pak Encep Suhendar',
                'jam_berangkat' => '02:00 WIB',
                'titik_tujuan' => 'Kirihim, Wado, Waduk Jatigede, Darmaraja, Situraja, Sumedang',
                'total_agen' => 10,
                'alokasi_oplah' => 1100,
                'status' => 'Siap Jalan',
            ],
            (object)[
                'kode' => '13',
                'kode_jurusan' => '13',
                'nama_jurusan' => 'Majalengka - Kuningan',
                'armada' => 'Mobil Luxio Box 13 (Z 8712 MJ)',
                'sopir' => 'Kang Rudi Hermawan',
                'jam_berangkat' => '01:50 WIB',
                'titik_tujuan' => 'Cikijing, Talaga, Maja, Majalengka Kota, Kadugede, Kuningan',
                'total_agen' => 11,
                'alokasi_oplah' => 1200,
                'status' => 'Siap Jalan',
            ],
            (object)[
                'kode' => '14',
                'kode_jurusan' => '14',
                'nama_jurusan' => 'Bandung Timur - Cileunyi',
                'armada' => 'Mobil Gran Max Box 14 (D 8452 BD)',
                'sopir' => 'Pak Bambang Irawan',
                'jam_berangkat' => '01:45 WIB',
                'titik_tujuan' => 'Rancaekek, Jatinangor, Cileunyi, Ujungberung, Cibiru Bandung',
                'total_agen' => 13,
                'alokasi_oplah' => 1450,
                'status' => 'Siap Jalan',
            ],
            (object)[
                'kode' => '15',
                'kode_jurusan' => '15',
                'nama_jurusan' => 'Jalur Khusus Kantor / Ekspedisi Subuh',
                'armada' => 'Mobil Operasional Kantor 15 (Z 1001 KP)',
                'sopir' => 'Tim Ekspedisi Internal KP',
                'jam_berangkat' => '03:30 WIB',
                'titik_tujuan' => 'Bale Kota, Gedung DPRD, Polres, Kodim, Kejaksaan, Agen Khusus',
                'total_agen' => 8,
                'alokasi_oplah' => 750,
                'status' => 'Siap Jalan',
            ],
        ]);
    }

    /**
     * Helper dummy master Agen Kabar Priangan sesuai DOS SIRK.EXE
     */
    public function getDaftarAgen(): Collection
    {
        return collect([
            (object)[
                'id' => 1,
                'tanggal_daftar' => '2024-01-10',
                'kode_agen' => 'AG-11-001',
                'nama_agen' => 'Kios Koran H. Dadang (Pasar Wetan)',
                'alamat' => 'Jl. Pasar Wetan No. 14, Kota Tasikmalaya',
                'kategori' => '11',
                'kategori_label' => '11 - Agen Besar',
                'induk_agen' => '-',
                'wilayah_kode' => '01',
                'wilayah_nama' => 'Tasikmalaya',
                'jurusan_kode' => '06',
                'jurusan_nama' => 'Tasikmalaya Kota - Singaparna',
                'telepon' => '0812-2345-6789',
                'jatah_default' => 450,
                'plafon_piutang' => 15000000,
                'saldo_piutang' => 3850000,
                'status' => 'aktif',
                'status_label' => 'Aktif',
            ],
            (object)[
                'id' => 2,
                'tanggal_daftar' => '2024-02-15',
                'kode_agen' => 'AG-99-011',
                'nama_agen' => 'Loper Mamat Cihideung',
                'alamat' => 'Komplek Pertokoan Cihideung Blok B-3, Kota Tasikmalaya',
                'kategori' => '99',
                'kategori_label' => '99 - Sub Agen',
                'induk_agen' => 'Kios Koran H. Dadang (AG-11-001)',
                'wilayah_kode' => '01',
                'wilayah_nama' => 'Tasikmalaya',
                'jurusan_kode' => '06',
                'jurusan_nama' => 'Tasikmalaya Kota - Singaparna',
                'telepon' => '0852-9876-1234',
                'jatah_default' => 80,
                'plafon_piutang' => 2000000,
                'saldo_piutang' => 640000,
                'status' => 'aktif',
                'status_label' => 'Aktif',
            ],
            (object)[
                'id' => 3,
                'tanggal_daftar' => '2024-01-12',
                'kode_agen' => 'AG-11-002',
                'nama_agen' => 'Agen Berkah Ciamis Kota',
                'alamat' => 'Jl. Jenderal Sudirman No. 45, Alun-alun Ciamis',
                'kategori' => '11',
                'kategori_label' => '11 - Agen Besar',
                'induk_agen' => '-',
                'wilayah_kode' => '03',
                'wilayah_nama' => 'Ciamis',
                'jurusan_kode' => '07',
                'jurusan_nama' => 'Ciamis Kota - Kawali',
                'telepon' => '0813-8899-7711',
                'jatah_default' => 600,
                'plafon_piutang' => 20000000,
                'saldo_piutang' => 5200000,
                'status' => 'aktif',
                'status_label' => 'Aktif',
            ],
            (object)[
                'id' => 4,
                'tanggal_daftar' => '2024-03-01',
                'kode_agen' => 'AG-99-021',
                'nama_agen' => 'Kios Alun-alun Kawali',
                'alamat' => 'Depan Pendopo Kawali, Pasar Kawali, Ciamis',
                'kategori' => '99',
                'kategori_label' => '99 - Sub Agen',
                'induk_agen' => 'Agen Berkah Ciamis Kota (AG-11-002)',
                'wilayah_kode' => '03',
                'wilayah_nama' => 'Ciamis',
                'jurusan_kode' => '07',
                'jurusan_nama' => 'Ciamis Kota - Kawali',
                'telepon' => '0877-1122-3344',
                'jatah_default' => 120,
                'plafon_piutang' => 3000000,
                'saldo_piutang' => 890000,
                'status' => 'aktif',
                'status_label' => 'Aktif',
            ],
            (object)[
                'id' => 5,
                'tanggal_daftar' => '2024-01-20',
                'kode_agen' => 'AG-11-003',
                'nama_agen' => 'Agen Tarogong Prima Garut',
                'alamat' => 'Jl. Raya Tarogong No. 102, Simpang Lima Garut',
                'kategori' => '11',
                'kategori_label' => '11 - Agen Besar',
                'induk_agen' => '-',
                'wilayah_kode' => '02',
                'wilayah_nama' => 'Garut',
                'jurusan_kode' => '03',
                'jurusan_nama' => 'Garut - Pameungpeuk',
                'telepon' => '0812-4455-6677',
                'jatah_default' => 550,
                'plafon_piutang' => 18000000,
                'saldo_piutang' => 4100000,
                'status' => 'aktif',
                'status_label' => 'Aktif',
            ],
            (object)[
                'id' => 6,
                'tanggal_daftar' => '2024-03-10',
                'kode_agen' => 'AG-99-031',
                'nama_agen' => 'Kios Subur Pameungpeuk Pesisir',
                'alamat' => 'Terminal Pameungpeuk, Garut Selatan',
                'kategori' => '99',
                'kategori_label' => '99 - Sub Agen',
                'induk_agen' => 'Agen Tarogong Prima Garut (AG-11-003)',
                'wilayah_kode' => '02',
                'wilayah_nama' => 'Garut',
                'jurusan_kode' => '03',
                'jurusan_nama' => 'Garut - Pameungpeuk',
                'telepon' => '0821-3344-5566',
                'jatah_default' => 90,
                'plafon_piutang' => 2500000,
                'saldo_piutang' => 720000,
                'status' => 'aktif',
                'status_label' => 'Aktif',
            ],
            (object)[
                'id' => 7,
                'tanggal_daftar' => '2024-01-18',
                'kode_agen' => 'AG-11-004',
                'nama_agen' => 'Kios Sinar Pagi Banjar Patroman',
                'alamat' => 'Terminal Kota Banjar Kios No. 05, Banjar',
                'kategori' => '11',
                'kategori_label' => '11 - Agen Besar',
                'induk_agen' => '-',
                'wilayah_kode' => '04',
                'wilayah_nama' => 'Banjar & Pangandaran',
                'jurusan_kode' => '08',
                'jurusan_nama' => 'Banjar Patroman - Banjarsari',
                'telepon' => '0853-2211-9988',
                'jatah_default' => 380,
                'plafon_piutang' => 12000000,
                'saldo_piutang' => 2950000,
                'status' => 'aktif',
                'status_label' => 'Aktif',
            ],
            (object)[
                'id' => 8,
                'tanggal_daftar' => '2024-02-05',
                'kode_agen' => 'AG-11-005',
                'nama_agen' => 'Agen Karangnunggal Sejahtera (Tasela)',
                'alamat' => 'Jl. Raya Karangnunggal KM 12, Tasikmalaya Selatan',
                'kategori' => '11',
                'kategori_label' => '11 - Agen Besar',
                'induk_agen' => '-',
                'wilayah_kode' => '01',
                'wilayah_nama' => 'Tasikmalaya',
                'jurusan_kode' => '02',
                'jurusan_nama' => 'Tasikmalaya - Cipatujah',
                'telepon' => '0813-7766-5544',
                'jatah_default' => 320,
                'plafon_piutang' => 10000000,
                'saldo_piutang' => 2100000,
                'status' => 'aktif',
                'status_label' => 'Aktif',
            ],
            (object)[
                'id' => 9,
                'tanggal_daftar' => '2023-11-12',
                'kode_agen' => 'AG-00-991',
                'nama_agen' => 'Ex-Kios Terminal Ciawi (Tutup Usaha)',
                'alamat' => 'Ex-Terminal Lama Ciawi, Tasikmalaya Utara',
                'kategori' => '00',
                'kategori_label' => '00 - Agen Non Aktif',
                'induk_agen' => '-',
                'wilayah_kode' => '01',
                'wilayah_nama' => 'Tasikmalaya',
                'jurusan_kode' => '10',
                'jurusan_nama' => 'Rajapolah - Cihaurbeuti - Panjalu',
                'telepon' => '0811-9988-7766',
                'jatah_default' => 0,
                'plafon_piutang' => 5000000,
                'saldo_piutang' => 1450000,
                'status' => 'non-aktif',
                'status_label' => 'Non-Aktif (Terkunci)',
            ],
            (object)[
                'id' => 10,
                'tanggal_daftar' => '2023-12-05',
                'kode_agen' => 'AG-00-992',
                'nama_agen' => 'Ex-Loper Cipatujah Pesisir',
                'alamat' => 'Pelabuhan Pamayangsari Cipatujah',
                'kategori' => '00',
                'kategori_label' => '00 - Agen Non Aktif',
                'induk_agen' => 'Agen Karangnunggal Sejahtera (AG-11-005)',
                'wilayah_kode' => '01',
                'wilayah_nama' => 'Tasikmalaya',
                'jurusan_kode' => '02',
                'jurusan_nama' => 'Tasikmalaya - Cipatujah',
                'telepon' => '0822-4433-2211',
                'jatah_default' => 0,
                'plafon_piutang' => 2000000,
                'saldo_piutang' => 450000,
                'status' => 'non-aktif',
                'status_label' => 'Non-Aktif (Terkunci)',
            ],
        ]);
    }

    /**
     * Dashboard Divisi Sirkulasi
     */
    public function dashboard(Request $request)
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $oplahHarian = 12500;
        $wilayah = [
            ['nama' => '01 - Kota & Kab. Tasikmalaya', 'oplah' => 5200, 'agen' => 38, 'retur_pct' => 1.8],
            ['nama' => '02 - Kabupaten Garut', 'oplah' => 2400, 'agen' => 22, 'retur_pct' => 2.4],
            ['nama' => '03 - Kabupaten Ciamis', 'oplah' => 3100, 'agen' => 26, 'retur_pct' => 2.1],
            ['nama' => '04 - Banjar & Pangandaran', 'oplah' => 2100, 'agen' => 19, 'retur_pct' => 1.7],
            ['nama' => '05 - Sumedang & Sekitarnya', 'oplah' => 1400, 'agen' => 14, 'retur_pct' => 2.5],
            ['nama' => '06 - Kuota Gratis / Redaksi', 'oplah' => 450, 'agen' => 6, 'retur_pct' => 0.0],
            ['nama' => '07 - Bandung Raya', 'oplah' => 1250, 'agen' => 11, 'retur_pct' => 2.0],
            ['nama' => '08 - Insidentil / Suplemen', 'oplah' => 600, 'agen' => 4, 'retur_pct' => 1.2],
        ];

        $daftarAgen = $this->getDaftarAgen();
        $totalAgenAktif = $daftarAgen->where('status', 'aktif')->count();
        $totalAgenNonAktif = $daftarAgen->where('status', 'non-aktif')->count();
        $totalAgen = $daftarAgen->count();
        $totalRetur = 275;
        $efektivitasDistribusi = 97.8;

        return view('pages.sirkulasi.dashboard', compact(
            'oplahHarian',
            'wilayah',
            'totalAgen',
            'totalAgenAktif',
            'totalAgenNonAktif',
            'totalRetur',
            'efektivitasDistribusi',
            'today',
            'tomorrow'
        ));
    }

    // =========================================================================
    // 1. MENU TABEL (Master Data DOS SIRK.EXE)
    // =========================================================================

    /**
     * Tabel Wilayah Pemasaran (01 s.d. 08)
     */
    public function tabelWilayah(Request $request)
    {
        $wilayahList = $this->getDaftarWilayah();
        $totalAlokasi = $wilayahList->sum('alokasi_default');
        $totalAgen = $wilayahList->sum('total_agen');

        return view('pages.sirkulasi.tabel.wilayah', compact('wilayahList', 'totalAlokasi', 'totalAgen'));
    }

    /**
     * Tabel Pengepakan (01 Bagian Sirkulasi)
     */
    public function tabelPengepakan(Request $request)
    {
        $pengepakanList = $this->getDaftarPengepakan();
        $jurusanList = $this->getDaftarJurusan();

        return view('pages.sirkulasi.tabel.pengepakan', compact('pengepakanList', 'jurusanList'));
    }

    /**
     * Tabel Jenis Agen (00, 11, 99)
     */
    public function tabelJenisAgen(Request $request)
    {
        $jenisList = $this->getDaftarJenisAgen();
        $agenList = $this->getDaftarAgen();

        return view('pages.sirkulasi.tabel.jenis-agen', compact('jenisList', 'agenList'));
    }


    /**
     * Tabel Data Agen (Tanggal, Kode, Nama, Alamat, Jatah, dll.)
     */
    public function tabelAgen(Request $request)
    {
        $search = $request->input('search');
        $kategoriFilter = $request->input('kategori', 'all');
        $statusFilter = $request->input('status', 'all');
        $wilayahFilter = $request->input('wilayah', 'all');
        $jurusanFilter = $request->input('jurusan', 'all');

        $agenList = $this->getDaftarAgen();

        if ($search) {
            $agenList = $agenList->filter(function ($item) use ($search) {
                return str_contains(strtolower($item->nama_agen), strtolower($search))
                    || str_contains(strtolower($item->kode_agen), strtolower($search))
                    || str_contains(strtolower($item->alamat), strtolower($search))
                    || str_contains(strtolower($item->wilayah_nama), strtolower($search));
            });
        }

        if ($kategoriFilter !== 'all') {
            $agenList = $agenList->where('kategori', $kategoriFilter);
        }

        if ($statusFilter !== 'all') {
            $agenList = $agenList->where('status', $statusFilter);
        }

        if ($wilayahFilter !== 'all') {
            $agenList = $agenList->where('wilayah_kode', $wilayahFilter);
        }

        if ($jurusanFilter !== 'all') {
            $agenList = $agenList->where('jurusan_kode', $jurusanFilter);
        }

        $wilayahList = $this->getDaftarWilayah();
        $jurusanList = $this->getDaftarJurusan();
        $jenisList = $this->getDaftarJenisAgen();

        return view('pages.sirkulasi.tabel.agen', compact(
            'agenList',
            'wilayahList',
            'jurusanList',
            'jenisList',
            'search',
            'kategoriFilter',
            'statusFilter',
            'wilayahFilter',
            'jurusanFilter'
        ));
    }

    public function storeAgen(Request $request)
    {
        // Divisi Sirkulasi hanya memiliki wewenang pemantauan (Read-Only)
        if (!auth()->user()?->isAdmin()) {
            return redirect()->route('sirkulasi.tabel.agen')
                ->with('error', 'Akses ditolak: Divisi Sirkulasi hanya memiliki wewenang untuk melihat dan memantau (Read-Only) data agen. Penambahan mitra agen baru hanya dapat dilakukan oleh Administrator.');
        }

        return redirect()->route('sirkulasi.tabel.agen')
            ->with('success', 'Data Agen baru berhasil disimpan ke Master Tabel Sirkulasi.');
    }

    public function toggleStatusAgen($id)
    {
        // Divisi Sirkulasi hanya memiliki wewenang pemantauan (Read-Only)
        if (!auth()->user()?->isAdmin()) {
            return redirect()->route('sirkulasi.tabel.agen')
                ->with('error', 'Akses ditolak: Divisi Sirkulasi tidak diizinkan mengubah status atau mengedit data keagenan. Pengelolaan status hanya dapat dilakukan oleh Administrator.');
        }

        return redirect()->route('sirkulasi.tabel.agen')
            ->with('success', 'Status keagenan (00 Non-Aktif / Aktif) berhasil diperbarui. Data historis piutang akuntansi tetap aman terjaga.');
    }

    // =========================================================================
    // 2. MENU JATAH (Operasional H+1)
    // =========================================================================

    /**
     * Edit Data Penjualan (Input matriks jatah H+1: Langganan, Konsinyasi/Eceran Rp3.000, Gratis, Insidentil)
     */
    public function jatahEdit(Request $request)
    {
        $today = Carbon::today();
        $targetTanggal = $request->input('tanggal', Carbon::tomorrow()->toDateString());
        $jurusanPilihan = $request->input('jurusan', 'all');

        $daftarAgen = $this->getDaftarAgen()->where('status', 'aktif');
        if ($jurusanPilihan !== 'all') {
            $daftarAgen = $daftarAgen->where('jurusan_kode', $jurusanPilihan);
        }

        $matrixJatah = $daftarAgen->map(function ($agen) {
            $langganan = (int) ($agen->jatah_default * 0.60);
            $konsinyasi = (int) ($agen->jatah_default * 0.35); // Eceran konsinyasi Rp3.000
            $gratis = (int) ($agen->jatah_default * 0.05);
            $insidentil = 0;
            $totalJatah = $langganan + $konsinyasi + $gratis + $insidentil;

            return (object)[
                'agen_id' => $agen->id,
                'kode_agen' => $agen->kode_agen,
                'nama_agen' => $agen->nama_agen,
                'alamat' => $agen->alamat,
                'kategori' => $agen->kategori,
                'jurusan_kode' => $agen->jurusan_kode,
                'jurusan_nama' => $agen->jurusan_nama,
                'langganan' => $langganan,
                'konsinyasi' => $konsinyasi,
                'gratis' => $gratis,
                'insidentil' => $insidentil,
                'total_jatah' => $totalJatah,
                'estimasi_omset' => $konsinyasi * 3000,
            ];
        });

        $totalLangganan = $matrixJatah->sum('langganan');
        $totalKonsinyasi = $matrixJatah->sum('konsinyasi');
        $totalGratis = $matrixJatah->sum('gratis');
        $totalInsidentil = $matrixJatah->sum('insidentil');
        $grandTotalJatah = $matrixJatah->sum('total_jatah');
        $grandTotalOmset = $matrixJatah->sum('estimasi_omset');

        $jurusanList = $this->getDaftarJurusan();

        return view('pages.sirkulasi.jatah.edit', compact(
            'matrixJatah',
            'targetTanggal',
            'jurusanPilihan',
            'jurusanList',
            'totalLangganan',
            'totalKonsinyasi',
            'totalGratis',
            'totalInsidentil',
            'grandTotalJatah',
            'grandTotalOmset',
            'today'
        ));
    }

    public function storeJatahEdit(Request $request)
    {
        $targetTanggal = $request->input('tanggal_target', Carbon::tomorrow()->toDateString());
        return redirect()->route('sirkulasi.jatah.edit', ['tanggal' => $targetTanggal])
            ->with('success', "Matriks Jatah Penjualan H+1 ({$targetTanggal}) berhasil disimpan ke sistem distribusi.");
    }

    /**
     * Copy Data Penjualan (Salin pola alokasi antar-tanggal kerja)
     */
    public function copyJatah(Request $request)
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $lastWeekSameDay = Carbon::tomorrow()->subDays(7);

        return view('pages.sirkulasi.jatah.copy', compact('today', 'tomorrow', 'lastWeekSameDay'));
    }

    public function processCopyJatah(Request $request)
    {
        $sumberTanggal = $request->input('sumber_tanggal');
        $targetTanggal = $request->input('target_tanggal');

        return redirect()->route('sirkulasi.jatah.edit', ['tanggal' => $targetTanggal])
            ->with('success', "Pola jatah penjualan dari tanggal {$sumberTanggal} berhasil disalin ke tanggal kerja {$targetTanggal}. Silakan periksa kembali pada menu Edit Data Penjualan.");
    }

    /**
     * Tutup Data Penjualan (Locking harian/bulanan sebelum ditarik Akuntansi)
     */
    public function jatahTutup(Request $request)
    {
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $carbonBulan = Carbon::parse($bulanPilihan . '-01');

        $riwayatClosing = collect([
            (object)[
                'periode' => 'September 2026',
                'periode_key' => '2026-09',
                'total_oplah_cetak' => 375000,
                'total_retur_eks' => 7420,
                'rasio_retur' => 1.98,
                'total_omset_bersih' => 1029224000,
                'status' => 'LOCKED',
                'status_label' => 'Ready to Pull Akunting',
                'closed_at' => '2026-09-30 23:59:00',
                'closed_by' => 'Manager Sirkulasi',
            ],
            (object)[
                'periode' => 'Agustus 2026',
                'periode_key' => '2026-08',
                'total_oplah_cetak' => 387500,
                'total_retur_eks' => 8150,
                'rasio_retur' => 2.10,
                'total_omset_bersih' => 1062180000,
                'status' => 'PULLED',
                'status_label' => 'Telah Diproses Akuntansi',
                'closed_at' => '2026-08-31 23:59:00',
                'closed_by' => 'Manager Sirkulasi',
            ],
        ]);

        $periodeSaatIni = (object)[
            'periode' => $carbonBulan->translatedFormat('F Y'),
            'periode_key' => $bulanPilihan,
            'total_oplah_cetak' => 185000,
            'total_terjual' => 180950,
            'total_retur_eks' => 4050,
            'rasio_retur' => 2.19,
            'omset_kotor' => 518000000,
            'potongan_retur' => 12150000,
            'omset_bersih' => 505850000,
            'is_locked' => false,
            'status' => 'OPEN',
            'status_label' => 'Operasional Aktif (Belum Tutup Buku)',
        ];

        return view('pages.sirkulasi.jatah.tutup', compact('riwayatClosing', 'periodeSaatIni', 'bulanPilihan'));
    }

    public function lockJatahTutup(Request $request)
    {
        $periode = $request->input('periode');
        return redirect()->route('sirkulasi.jatah.tutup', ['bulan' => $periode])
            ->with('success', "Data Penjualan Periode {$periode} berhasil DITUTUP & DIKUNCI (Status: READY TO PULL AKUNTING). Data jatah dan retur terkunci dari perubahan.");
    }

    /**
     * Hitung Data Penjualan (Kalkulasi akumulasi eksemplar)
     */
    public function jatahHitung(Request $request)
    {
        $tanggalPilihan = $request->input('tanggal', Carbon::tomorrow()->toDateString());
        $jurusanList = $this->getDaftarJurusan();

        $rekapKalkulasi = $jurusanList->map(function ($jurusan) {
            $langganan = (int) ($jurusan->alokasi_oplah * 0.58);
            $konsinyasi = (int) ($jurusan->alokasi_oplah * 0.36);
            $gratis = (int) ($jurusan->alokasi_oplah * 0.04);
            $insidentil = $jurusan->alokasi_oplah - ($langganan + $konsinyasi + $gratis);
            $totalEks = $langganan + $konsinyasi + $gratis + $insidentil;
            $omsetKonsinyasi = $konsinyasi * 3000;

            return (object)[
                'kode' => $jurusan->kode,
                'nama_jurusan' => $jurusan->nama_jurusan,
                'armada' => $jurusan->armada,
                'langganan' => $langganan,
                'konsinyasi' => $konsinyasi,
                'gratis' => $gratis,
                'insidentil' => $insidentil,
                'total_eks' => $totalEks,
                'omset_konsinyasi' => $omsetKonsinyasi,
            ];
        });

        $totalLangganan = $rekapKalkulasi->sum('langganan');
        $totalKonsinyasi = $rekapKalkulasi->sum('konsinyasi');
        $totalGratis = $rekapKalkulasi->sum('gratis');
        $totalInsidentil = $rekapKalkulasi->sum('insidentil');
        $grandTotalEks = $rekapKalkulasi->sum('total_eks');
        $grandTotalOmset = $rekapKalkulasi->sum('omset_konsinyasi');

        return view('pages.sirkulasi.jatah.hitung', compact(
            'tanggalPilihan',
            'rekapKalkulasi',
            'totalLangganan',
            'totalKonsinyasi',
            'totalGratis',
            'totalInsidentil',
            'grandTotalEks',
            'grandTotalOmset'
        ));
    }

    public function processJatahHitung(Request $request)
    {
        $tanggal = $request->input('tanggal');
        return redirect()->route('sirkulasi.jatah.hitung', ['tanggal' => $tanggal])
            ->with('success', "Perhitungan akumulasi eksemplar penjualan untuk tanggal {$tanggal} berhasil dikalkulasi ulang.");
    }

    // =========================================================================
    // 3. MENU CETAK (Laporan & Distribusi DOS SIRK.EXE)
    // =========================================================================

    /**
     * Laporan Pemasaran (8 Varian Cetak)
     */
    public function cetakPemasaran(Request $request)
    {
        $tab = $request->input('tab', 'agen-besar');
        $bulan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $wilayahList = $this->getDaftarWilayah();
        $jurusanList = $this->getDaftarJurusan();
        $agenList = $this->getDaftarAgen()->where('status', 'aktif');

        return view('pages.sirkulasi.cetak.pemasaran', compact('tab', 'bulan', 'wilayahList', 'jurusanList', 'agenList'));
    }

    /**
     * Laporan Penjualan (Rekapitulasi bulanan ke Keuangan/Faktur)
     */
    public function cetakPenjualan(Request $request)
    {
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $daftarAgen = $this->getDaftarAgen();

        $rekapPenjualan = $daftarAgen->map(function ($agen) {
            $kirim = $agen->jatah_default * 30;
            $retur = (int) ($kirim * 0.022);
            $nett = $kirim - $retur;
            $harga = $agen->kategori === '11' ? 2800 : 3000;
            $nilaiTagihan = $nett * $harga;

            return (object)[
                'kode_agen' => $agen->kode_agen,
                'nama_agen' => $agen->nama_agen,
                'wilayah' => $agen->wilayah_nama,
                'jurusan' => $agen->jurusan_nama,
                'kategori' => $agen->kategori,
                'kirim' => $kirim,
                'retur' => $retur,
                'nett_sales' => $nett,
                'harga_satuan' => $harga,
                'nilai_tagihan' => $nilaiTagihan,
                'saldo_piutang' => $agen->saldo_piutang,
                'status_bayar' => $agen->status === 'aktif' ? 'Lancar' : 'Terkunci',
            ];
        });

        $totalKirim = $rekapPenjualan->sum('kirim');
        $totalRetur = $rekapPenjualan->sum('retur');
        $totalNett = $rekapPenjualan->sum('nett_sales');
        $totalTagihan = $rekapPenjualan->sum('nilai_tagihan');
        $totalSaldoPiutang = $rekapPenjualan->sum('saldo_piutang');

        return view('pages.sirkulasi.cetak.penjualan', compact(
            'bulanPilihan',
            'rekapPenjualan',
            'totalKirim',
            'totalRetur',
            'totalNett',
            'totalTagihan',
            'totalSaldoPiutang'
        ));
    }

    /**
     * Nota & Distribusi (Nota Jurusan, Nota Agen, Distribusi Pengiriman Armada Subuh)
     */
    public function cetakNotaDistribusi(Request $request)
    {
        $tanggalDistribusi = $request->input('tanggal', Carbon::tomorrow()->toDateString());
        $selectedJurusanKode = $request->input('jurusan', '01');
        $jenisNota = $request->input('jenis', 'distribusi-jurusan');

        $jurusanList = $this->getDaftarJurusan();
        $activeJurusan = $jurusanList->firstWhere('kode_jurusan', $selectedJurusanKode) ?? $jurusanList->first();

        $agenJurusan = $this->getDaftarAgen()
            ->where('status', 'aktif')
            ->where('jurusan_kode', $activeJurusan->kode_jurusan);

        $packingItems = $agenJurusan->map(function ($agen) {
            $jatah = $agen->jatah_default;
            $bendel = intdiv($jatah, 50);
            $eceran = $jatah % 50;

            return (object)[
                'kode_agen' => $agen->kode_agen,
                'nama_agen' => $agen->nama_agen,
                'alamat' => $agen->alamat,
                'telepon' => $agen->telepon,
                'kategori' => $agen->kategori,
                'total_eks' => $jatah,
                'bendel_50' => $bendel,
                'sisa_ecer' => $eceran,
                'kemasan' => "{$bendel} Bendel (" . ($bendel * 50) . " eks) + {$eceran} eks lepas",
            ];
        });

        $totalEksemplar = $packingItems->sum('total_eks');
        $totalBendel = $packingItems->sum('bendel_50');
        $totalEceran = $packingItems->sum('sisa_ecer');

        return view('pages.sirkulasi.cetak.nota-distribusi', compact(
            'tanggalDistribusi',
            'jurusanList',
            'activeJurusan',
            'packingItems',
            'totalEksemplar',
            'totalBendel',
            'totalEceran',
            'jenisNota'
        ));
    }

    /**
     * Nama & Alamat Agen (Cetak Direktori Agen per Wilayah/Jurusan)
     */
    public function cetakAlamatAgen(Request $request)
    {
        $wilayahPilihan = $request->input('wilayah', 'all');
        $jurusanPilihan = $request->input('jurusan', 'all');
        $kategoriPilihan = $request->input('kategori', 'all');

        $agenList = $this->getDaftarAgen();

        if ($wilayahPilihan !== 'all') {
            $agenList = $agenList->where('wilayah_kode', $wilayahPilihan);
        }
        if ($jurusanPilihan !== 'all') {
            $agenList = $agenList->where('jurusan_kode', $jurusanPilihan);
        }
        if ($kategoriPilihan !== 'all') {
            $agenList = $agenList->where('kategori', $kategoriPilihan);
        }

        $wilayahList = $this->getDaftarWilayah();
        $jurusanList = $this->getDaftarJurusan();
        $jenisList = $this->getDaftarJenisAgen();

        return view('pages.sirkulasi.cetak.alamat-agen', compact(
            'agenList',
            'wilayahList',
            'jurusanList',
            'jenisList',
            'wilayahPilihan',
            'jurusanPilihan',
            'kategoriPilihan'
        ));
    }

    // =========================================================================
    // BACKWARD COMPATIBILITY ALIASES
    // =========================================================================
    public function masterAgen(Request $request) { return $this->tabelAgen($request); }
    public function masterRute(Request $request) { return $this->tabelAgen($request); }
    public function masterKalender(Request $request) { return $this->tabelWilayah($request); }
    public function masterTarif(Request $request) { return $this->tabelJenisAgen($request); }
    public function pelanggan(Request $request) { return $this->tabelAgen($request); }
    public function jatahHarian(Request $request) { return $this->jatahEdit($request); }
    public function storeJatahHarian(Request $request) { return $this->storeJatahEdit($request); }
    public function quickEditJatah(Request $request) { return $this->jatahEdit($request); }
    public function updateQuickEditJatah(Request $request) { return $this->storeJatahEdit($request); }
    public function suratJalan(Request $request) { return $this->cetakNotaDistribusi($request); }
    public function returBapr(Request $request) { return $this->cetakPenjualan($request); }
    public function storeReturBapr(Request $request) { return redirect()->route('sirkulasi.cetak.penjualan')->with('success', 'BAPR Retur tercatat.'); }
    public function orderCetak(Request $request) { return $this->cetakPemasaran($request); }
    public function closingBulanan(Request $request) { return $this->jatahTutup($request); }
    public function lockDataBulanan(Request $request) { return $this->lockJatahTutup($request); }
    public function rekapPemasaran(Request $request) { return $this->cetakPemasaran($request); }
    public function rekapPenjualan(Request $request) { return $this->cetakPenjualan($request); }
}
