<?php

namespace App\Services;

use App\Models\Agen;
use App\Models\AuditLogAkuntansi;
use App\Models\DailyCutoff;
use App\Models\LogHitungAkuntansi;
use App\Models\MemoUnlock;
use App\Models\PeriodeAkuntansi;
use App\Models\Rute;
use App\Models\SirkulasiFakturBulanan;
use App\Models\SistemConfig;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SirkulasiDataService
{
    /**
     * Ambil pengaturan tarif, standar pengepakan, cut-off, dan komposisi oplah Sirkulasi dari database.
     */
    public function getConfig(): array
    {
        $hasConfigTable = Schema::hasTable('sistem_configs');
        $getCfg = fn(string $key, mixed $default) => $hasConfigTable ? SistemConfig::get($key, $default) : $default;

        $rasioLangganan  = (float) $getCfg('sirkulasi_rasio_langganan', 60);
        $rasioKonsinyasi = (float) $getCfg('sirkulasi_rasio_konsinyasi', 35);
        $rasioGratis     = (float) $getCfg('sirkulasi_rasio_gratis', 5);

        return [
            'harga_agen_besar'   => max(0, (int) $getCfg('sirkulasi_harga_agen_besar', 2800)),
            'harga_eceran'       => max(0, (int) $getCfg('sirkulasi_harga_eceran', 3000)),
            'harga_langganan'    => max(0, (int) $getCfg('sirkulasi_harga_langganan', 3000)),
            'harga_cover_het'    => max(0, (int) $getCfg('sirkulasi_harga_cover_het', 3500)),
            'isi_bendel'         => max(1, (int) $getCfg('sirkulasi_isi_bendel', 50)),
            'jam_cutoff'         => (string) $getCfg('sirkulasi_jam_cutoff', $hasConfigTable ? SistemConfig::getJamCutoffSirkulasi() : '17:00'),
            'batas_retur_persen' => max(0, (float) $getCfg('sirkulasi_batas_retur_persen', 10)),
            'rasio_langganan'    => $rasioLangganan,
            'rasio_konsinyasi'   => $rasioKonsinyasi,
            'rasio_gratis'       => $rasioGratis,
            'pct_langganan'      => $rasioLangganan / 100,
            'pct_konsinyasi'     => $rasioKonsinyasi / 100,
            'pct_gratis'         => $rasioGratis / 100,
        ];
    }

    /**
     * Cek apakah pengguna memiliki hak kelola Master Agen & Otorisasi Langsung (Divisi Akuntansi & Admin).
     */
    public function canManageAgen(): bool
    {
        $user = auth()->user();
        return $user && ($user->isAdmin() || $user->hasRole('accounting', 'akuntansi'));
    }

    /**
     * Eksekusi penutupan & penguncian data penjualan harian sirkulasi (otomatis maupun manual).
     */
    public static function executeTutupHarian(string $tanggal, ?string $operator = null): bool
    {
        $cleanDate = Carbon::parse($tanggal)->toDateString();
        $nowStr = Carbon::now()->format('Y-m-d H:i:s');
        $cutoffTime = class_exists(SistemConfig::class) ? SistemConfig::getJamCutoffSirkulasi() : '17:00';
        $operator = $operator ?: "Sistem Otomatis (Cut-Off {$cutoffTime} WIB)";

        session()->put('locked_harian_' . $cleanDate, true);
        session()->put('locked_at_' . $cleanDate, $nowStr);
        session()->put('locked_by_' . $cleanDate, $operator);
        Cache::put('locked_harian_' . $cleanDate, true, now()->addDays(7));
        Cache::put('locked_at_' . $cleanDate, $nowStr, now()->addDays(7));
        Cache::put('locked_by_' . $cleanDate, $operator, now()->addDays(7));

        if (Schema::hasTable('daily_cutoffs')) {
            DailyCutoff::updateOrCreate(
                ['tanggal_edisi' => $cleanDate],
                [
                    'jam_cutoff'        => $cutoffTime,
                    'is_closed'         => true,
                    'closed_by_user_id' => auth()->id() ?? 1,
                    'closed_at'         => now(),
                    'catatan'           => 'Tutup transaksi penjualan harian Sirkulasi oleh ' . $operator,
                ]
            );
        }

        if (Schema::hasTable('memo_unlocks')) {
            MemoUnlock::whereDate('tanggal_target', $cleanDate)
                ->where('tujuan_divisi', 'sirkulasi')
                ->where('status', 'OPEN')
                ->update([
                    'status'               => 'CLOSED',
                    'ditutup_kembali_pada' => now(),
                ]);
        }

        session()->forget('request_unlock_harian_' . $cleanDate);
        Cache::forget('request_unlock_harian_' . $cleanDate);

        if (class_exists(AuditLogAkuntansi::class) && Schema::hasTable('audit_log_akuntansis')) {
            AuditLogAkuntansi::catat('TUTUP_BUKU_HARIAN', 'SIRKULASI', "Tutup data penjualan sirkulasi edisi {$cleanDate} oleh {$operator}");
        }

        return true;
    }

    /**
     * Cek alasan penguncian tanggal edisi jatah sirkulasi (null jika terbuka untuk diedit).
     */
    public function getLockReasonTanggalJatah(string $tanggal): ?string
    {
        $cleanDate = Carbon::parse($tanggal)->toDateString();
        $periode = substr($cleanDate, 0, 7);

        if (Schema::hasTable('periode_akuntansis') && PeriodeAkuntansi::isPeriodeLocked($periode)) {
            return "Periode akuntansi {$periode} sudah ditutup permanen.";
        }

        if (Schema::hasTable('log_hitung_akuntansis')
            && LogHitungAkuntansi::where('jenis', 'penjualan_sirkulasi')->where('periode', $periode)->exists()) {
            return "Data penjualan periode {$periode} sudah ditarik oleh Divisi Akuntansi.";
        }

        if (session('locked_bulanan_' . $periode, Cache::get('locked_bulanan_' . $periode, false))) {
            return "Buku bulanan sirkulasi periode {$periode} sudah ditutup.";
        }

        if (Schema::hasTable('memo_unlocks')) {
            $hasApprovedMemo = MemoUnlock::whereDate('tanggal_target', $cleanDate)
                ->where(function ($q) {
                    $q->where('tujuan_divisi', 'sirkulasi')
                      ->orWhere('nomor_memo', 'like', 'REQ-ULK-%')
                      ->orWhere('nomor_memo', 'like', '%SIRK%')
                      ->orWhere('nomor_memo', 'like', '%SRK%');
                })
                ->where('status', 'OPEN')
                ->whereIn('status_akuntansi', ['APPROVED', 'OPEN', 'DISETUJUI'])
                ->exists();

            if ($hasApprovedMemo) {
                return null;
            }
        }

        if (session('locked_harian_' . $cleanDate, Cache::get('locked_harian_' . $cleanDate, false))) {
            return "Data harian edisi {$cleanDate} sudah ditutup di Tutup Buku Harian.";
        }

        if (Schema::hasTable('daily_cutoffs')) {
            $cutoff = DailyCutoff::whereDate('tanggal_edisi', $cleanDate)->first();
            if ($cutoff && $cutoff->is_closed) {
                return "Data harian edisi {$cleanDate} sudah melewati Cut-Off / ditutup.";
            }
        }

        $jamCutoffSirkulasi = class_exists(SistemConfig::class) ? SistemConfig::getJamCutoffSirkulasi() : '17:00';
        $todayStr = Carbon::today()->toDateString();
        if ($cleanDate === $todayStr && Carbon::now()->format('H:i') >= $jamCutoffSirkulasi) {
            self::executeTutupHarian($cleanDate, "Sistem Otomatis (Cut-Off {$jamCutoffSirkulasi} WIB)");
            return "Data harian edisi hari ini ({$cleanDate}) telah melewati jam Cut-Off ({$jamCutoffSirkulasi} WIB) dan ditutup otomatis.";
        }

        if ($cleanDate < $todayStr) {
            return "Data harian edisi lampau ({$cleanDate}) sudah melewati Cut-Off jadwal distribusi dan ditutup.";
        }

        return null;
    }

    /**
     * Cek apakah tanggal edisi jatah sirkulasi terkunci dan tidak boleh diedit.
     */
    public function isTanggalJatahLocked(string $tanggal): bool
    {
        return $this->getLockReasonTanggalJatah($tanggal) !== null;
    }

    /**
     * Daftar 8 Wilayah Pemasaran (01 s.d. 08).
     */
    public function getDaftarWilayah(): Collection
    {
        return collect([
            (object)['kode' => '01', 'nama' => 'Tasikmalaya', 'keterangan' => 'Wilayah Distribusi Kota & Kab. Tasikmalaya', 'total_agen' => 38, 'alokasi_default' => 5200, 'tipe' => 'Komersil'],
            (object)['kode' => '02', 'nama' => 'Garut', 'keterangan' => 'Wilayah Distribusi Kab. Garut (Kota & Garut Selatan)', 'total_agen' => 22, 'alokasi_default' => 2400, 'tipe' => 'Komersil'],
            (object)['kode' => '03', 'nama' => 'Ciamis', 'keterangan' => 'Wilayah Distribusi Kab. Ciamis & Panjalu', 'total_agen' => 26, 'alokasi_default' => 3100, 'tipe' => 'Komersil'],
            (object)['kode' => '04', 'nama' => 'Banjar & Pangandaran', 'keterangan' => 'Wilayah Distribusi Kota Banjar & Pesisir Pangandaran', 'total_agen' => 19, 'alokasi_default' => 2100, 'tipe' => 'Komersil'],
            (object)['kode' => '05', 'nama' => 'Sumedang & Sekitarnya', 'keterangan' => 'Wilayah Distribusi Kab. Sumedang, Majalengka, Kuningan', 'total_agen' => 14, 'alokasi_default' => 1400, 'tipe' => 'Komersil'],
            (object)['kode' => '06', 'nama' => 'Gratis / Kuota Promo', 'keterangan' => 'Sampel Bukti Iklan, Redaksi, Arsip Legal, Forkopimda & Mitra Dinas', 'total_agen' => 6, 'alokasi_default' => 450, 'tipe' => 'Non-Komersil (Gratis)'],
            (object)['kode' => '07', 'nama' => 'Bandung Raya', 'keterangan' => 'Perwakilan Bandung, Kios Stasiun & Jalur Cileunyi', 'total_agen' => 11, 'alokasi_default' => 1250, 'tipe' => 'Komersil'],
            (object)['kode' => '08', 'nama' => 'Insidentil & Even Khusus', 'keterangan' => 'Pesanan Eksemplar Tambahan Suplemen, Acara Pemda & Kegiatan Akbar', 'total_agen' => 4, 'alokasi_default' => 600, 'tipe' => 'Insidentil'],
        ]);
    }

    /**
     * Standar Pengepakan Sirkulasi.
     */
    public function getDaftarPengepakan(): Collection
    {
        return collect([
            (object)['kode' => '01', 'unit' => 'Bendel Standar Agen (50 Eks)', 'kategori' => 'Distribusi Reguler', 'standar_bendel' => '50 Eks / Ikat', 'toleransi_ikat' => 'Maks. 50 Eks per Ikat Tali Rafia Silang Ganda Kencang', 'waktu_muat' => '01:30 - 02:30 WIB', 'pic_checker' => 'Koordinator Ekspedisi Gudang', 'keterangan' => 'Standar wajib untuk drop point agen besar dan sub agen.', 'status' => 'Standar Utama', 'badge_color' => 'emerald'],
            (object)['kode' => '02', 'unit' => 'Koran Lepas & Pecahan Eceran', 'kategori' => 'Sisa Jatah Agen', 'standar_bendel' => '1 s.d. 49 Eks', 'toleransi_ikat' => 'Label pita kertas khusus mencantumkan sisa pesanan per agen', 'waktu_muat' => '02:00 - 02:45 WIB', 'pic_checker' => 'Tim Checker & Sortir Sisa', 'keterangan' => 'Untuk pesanan agen yang bukan kelipatan 50 eksemplar.', 'status' => 'Pecahan Jatah', 'badge_color' => 'sky'],
            (object)['kode' => '03', 'unit' => 'Ekspedisi Jarak Jauh (Waterproof)', 'kategori' => 'Tahan Cuaca / Hujan', 'standar_bendel' => '50 / 100 Eks', 'toleransi_ikat' => 'Bungkus plastik tebal kedap air anti-hujan & debu jalanan', 'waktu_muat' => '01:00 - 02:00 WIB', 'pic_checker' => 'Petugas Ekspedisi Luar Kota', 'keterangan' => 'Prioritas rute luar kota dan pengiriman jarak jauh.', 'status' => 'Proteksi Cuaca', 'badge_color' => 'amber'],
            (object)['kode' => '04', 'unit' => 'Langganan Khusus / Instansi', 'kategori' => 'Home Delivery / Kantor', 'standar_bendel' => 'Per Paket Alamat', 'toleransi_ikat' => 'Kantong transparan berstempel alamat dinas / instansi pemda', 'waktu_muat' => '02:30 - 03:30 WIB', 'pic_checker' => 'Petugas Loper Pengantar', 'keterangan' => 'Pelanggan korporat, kantor dinas, bank, dan perhotelan.', 'status' => 'Langganan Tetap', 'badge_color' => 'purple'],
            (object)['kode' => '05', 'unit' => 'Bukti Muat Iklan & Arsip', 'kategori' => 'Internal & Rekanan', 'standar_bendel' => '10 s.d. 25 Eks', 'toleransi_ikat' => 'Stempel khusus Bukti Muat Iklan Priangan & arsip perpustakaan', 'waktu_muat' => '07:00 - 08:30 WIB', 'pic_checker' => 'Staf Administrasi Sirkulasi', 'keterangan' => 'Voucher bukti tayang pemasang iklan dan arsip redaksi.', 'status' => 'Internal & Iklan', 'badge_color' => 'slate'],
        ]);
    }

    /**
     * Daftar 3 Jenis Agen (00 Non Aktif, 11 Agen Besar, 99 Sub Agen).
     */
    public function getDaftarJenisAgen(): Collection
    {
        $cfg = $this->getConfig();
        $hargaBesarFmt = number_format($cfg['harga_agen_besar'], 0, ',', '.');
        $hargaEceranFmt = number_format($cfg['harga_eceran'], 0, ',', '.');

        return collect([
            (object)['kode' => '00', 'nama' => 'Agen Non Aktif', 'label' => '00 - Agen Non Aktif', 'harga_satuan' => 0, 'ketentuan' => 'Terkunci dari alokasi jatah harian. Data mutlak dipertahankan (soft delete/filter) untuk audit kartu piutang buku besar akuntansi.', 'sistem_distribusi' => 'Dihentikan / Non-Aktif', 'badge_color' => 'rose', 'status_aturan' => 'Dilarang Hard Delete'],
            (object)['kode' => '11', 'nama' => 'Agen Besar', 'label' => '11 - Agen Besar', 'harga_satuan' => $cfg['harga_agen_besar'], 'ketentuan' => "Mitra distribusi langsung drop point mobil subuh (Tarif Nett Rp {$hargaBesarFmt}/eks). Induk pembina jaringan sub agen dan loper kios.", 'sistem_distribusi' => 'Drop Point Distribusi Utama (Jatah > 300 eks)', 'badge_color' => 'blue', 'status_aturan' => 'Komersil Utama'],
            (object)['kode' => '99', 'nama' => 'Sub Agen / Pengecer', 'label' => '99 - Sub Agen', 'harga_satuan' => $cfg['harga_eceran'], 'ketentuan' => 'Pengecer, kios pasar, loper keliling di bawah koordinasi Agen Besar atau drop rute sepeda motor.', 'sistem_distribusi' => "Eceran & Loper Kios (Konsinyasi Rp {$hargaEceranFmt})", 'badge_color' => 'emerald', 'status_aturan' => 'Pengecer Resmi'],
        ]);
    }

    /**
     * Daftar Jurusan Armada Distribusi (dari database rutes dengan fallback default).
     */
    public function getDaftarJurusan(): Collection
    {
        if (Schema::hasTable('rutes')) {
            $routes = Rute::orderBy('kode')->get();
            if ($routes->isNotEmpty()) {
                return $routes->map(function ($r) {
                    $totalAgen = Agen::where('rute_kode', $r->kode)->count();
                    return (object)[
                        'id'            => $r->id,
                        'kode'          => $r->kode,
                        'kode_jurusan'  => $r->kode,
                        'nama_jurusan'  => $r->nama_rute,
                        'nama_rute'     => $r->nama_rute,
                        'armada'        => $r->armada ?? '-',
                        'sopir'         => $r->sopir ?? '-',
                        'jam_berangkat' => $r->jam_berangkat ?? '02:30 WIB',
                        'titik_tujuan'  => $r->titik_tujuan ?? '-',
                        'total_agen'    => $totalAgen > 0 ? $totalAgen : (int) ($r->total_agen ?? 10),
                        'alokasi_oplah' => (int) $r->alokasi_oplah,
                        'status'        => $r->status === 'aktif' ? 'Siap Jalan' : 'Non-Aktif',
                    ];
                });
            }
        }

        return collect([
            (object)['id' => 1, 'kode' => '01', 'kode_jurusan' => '01', 'nama_jurusan' => 'Ciamis - Pangandaran', 'armada' => 'Mobil L300 Box 01 (Z 8344 KC)', 'sopir' => 'Pak Tatang Supriatna', 'jam_berangkat' => '02:00 WIB', 'titik_tujuan' => 'Ciamis, Banjarsari, Kalipucang, Pangandaran, Parigi, Cijulang', 'total_agen' => 16, 'alokasi_oplah' => 2200, 'status' => 'Siap Jalan'],
            (object)['id' => 2, 'kode' => '02', 'kode_jurusan' => '02', 'nama_jurusan' => 'Tasikmalaya - Cipatujah', 'armada' => 'Mobil L300 Box 02 (Z 8190 HN)', 'sopir' => 'Pak Dedi Sutisna', 'jam_berangkat' => '02:15 WIB', 'titik_tujuan' => 'Sukawening, Karangnunggal, Cibalong, Pamijahan, Cipatujah', 'total_agen' => 14, 'alokasi_oplah' => 1650, 'status' => 'Siap Jalan'],
            (object)['id' => 3, 'kode' => '03', 'kode_jurusan' => '03', 'nama_jurusan' => 'Garut - Pameungpeuk', 'armada' => 'Mobil Gran Max Box 03 (Z 8621 DA)', 'sopir' => 'Kang Agus Mulyadi', 'jam_berangkat' => '02:15 WIB', 'titik_tujuan' => 'Tarogong, Garut Kota, Bayongbong, Cikajang, Cisurupan, Pameungpeuk', 'total_agen' => 15, 'alokasi_oplah' => 1950, 'status' => 'Siap Jalan'],
            (object)['id' => 4, 'kode' => '04', 'kode_jurusan' => '04', 'nama_jurusan' => 'NPTS Bandung - Panjalu', 'armada' => 'Mobil Carry Box 04 (Z 8812 HP)', 'sopir' => 'Kang Asep Kurniawan', 'jam_berangkat' => '02:30 WIB', 'titik_tujuan' => 'Rajapolah, Jamanis, Cihaurbeuti, Sukamantri, Situ Panjalu', 'total_agen' => 12, 'alokasi_oplah' => 1400, 'status' => 'Siap Jalan'],
            (object)['id' => 5, 'kode' => '05', 'kode_jurusan' => '05', 'nama_jurusan' => 'Bandung - Cirebon', 'armada' => 'Mobil Isuzu Elf 05 (Z 7720 EA)', 'sopir' => 'Pak Hendra Gunawan', 'jam_berangkat' => '01:45 WIB', 'titik_tujuan' => 'Cileunyi, Sumedang, Tomo, Kadipaten, Majalengka, Sumber Cirebon', 'total_agen' => 13, 'alokasi_oplah' => 1550, 'status' => 'Siap Jalan'],
            (object)['id' => 6, 'kode' => '06', 'kode_jurusan' => '06', 'nama_jurusan' => 'Tasikmalaya Kota - Singaparna', 'armada' => 'Mobil Carry Box 06 (Z 8421 HB)', 'sopir' => 'Kang Iwan Ridwan', 'jam_berangkat' => '03:00 WIB', 'titik_tujuan' => 'Mitra Batik, Pasar Wetan, Cihideung, Alun-alun Singaparna, Kudang', 'total_agen' => 24, 'alokasi_oplah' => 3800, 'status' => 'Siap Jalan'],
            (object)['id' => 7, 'kode' => '07', 'kode_jurusan' => '07', 'nama_jurusan' => 'Ciamis Kota - Kawali', 'armada' => 'Mobil Luxio Box 07 (Z 1450 TA)', 'sopir' => 'Kang Yana Suryana', 'jam_berangkat' => '02:45 WIB', 'titik_tujuan' => 'Alun-alun Ciamis, Pasar Manis, Baregbeg, Kawali, Lumbung', 'total_agen' => 17, 'alokasi_oplah' => 2100, 'status' => 'Siap Jalan'],
            (object)['id' => 8, 'kode' => '08', 'kode_jurusan' => '08', 'nama_jurusan' => 'Banjar Patroman - Banjarsari', 'armada' => 'Mobil Carry Box 08 (Z 8091 KA)', 'sopir' => 'Pak Nanang Sumarna', 'jam_berangkat' => '02:30 WIB', 'titik_tujuan' => 'Terminal Banjar, Langensari, Pataruman, Pamarican, Banjarsari', 'total_agen' => 11, 'alokasi_oplah' => 1300, 'status' => 'Siap Jalan'],
            (object)['id' => 9, 'kode' => '09', 'kode_jurusan' => '09', 'nama_jurusan' => 'Malangbong - Garut Kota', 'armada' => 'Mobil Gran Max 09 (Z 8234 DG)', 'sopir' => 'Kang Cecep Solihin', 'jam_berangkat' => '02:20 WIB', 'titik_tujuan' => 'Gentong, Malangbong, Bandrek, Cibatu, Wanaraja, Garut Kota', 'total_agen' => 12, 'alokasi_oplah' => 1250, 'status' => 'Siap Jalan'],
            (object)['id' => 10, 'kode' => '10', 'kode_jurusan' => '10', 'nama_jurusan' => 'Rajapolah - Cihaurbeuti - Panjalu', 'armada' => 'Mobil Carry Box 10 (Z 8555 TA)', 'sopir' => 'Pak Oding Rohidin', 'jam_berangkat' => '03:15 WIB', 'titik_tujuan' => 'Pasar Rajapolah, Ciawi, Sukamantri, Alun-alun Panjalu', 'total_agen' => 10, 'alokasi_oplah' => 950, 'status' => 'Siap Jalan'],
            (object)['id' => 11, 'kode' => '11', 'kode_jurusan' => '11', 'nama_jurusan' => 'Karangnunggal - Cibalong', 'armada' => 'Mobil L300 Box 11 (Z 8667 HP)', 'sopir' => 'Kang Ujang Mulyana', 'jam_berangkat' => '02:10 WIB', 'titik_tujuan' => 'Simpang, Karangnunggal, Bantarkalong, Cipatujah Pesisir', 'total_agen' => 9, 'alokasi_oplah' => 850, 'status' => 'Siap Jalan'],
            (object)['id' => 12, 'kode' => '12', 'kode_jurusan' => '12', 'nama_jurusan' => 'Sumedang - Wado - Darmaraja', 'armada' => 'Mobil Gran Max 12 (Z 8112 SD)', 'sopir' => 'Pak Encep Suhendar', 'jam_berangkat' => '02:00 WIB', 'titik_tujuan' => 'Kirihim, Wado, Waduk Jatigede, Darmaraja, Situraja, Sumedang', 'total_agen' => 10, 'alokasi_oplah' => 1100, 'status' => 'Siap Jalan'],
            (object)['id' => 13, 'kode' => '13', 'kode_jurusan' => '13', 'nama_jurusan' => 'Majalengka - Kuningan', 'armada' => 'Mobil Luxio Box 13 (Z 8712 MJ)', 'sopir' => 'Kang Rudi Hermawan', 'jam_berangkat' => '01:50 WIB', 'titik_tujuan' => 'Cikijing, Talaga, Maja, Majalengka Kota, Kadugede, Kuningan', 'total_agen' => 11, 'alokasi_oplah' => 1200, 'status' => 'Siap Jalan'],
            (object)['id' => 14, 'kode' => '14', 'kode_jurusan' => '14', 'nama_jurusan' => 'Bandung Timur - Cileunyi', 'armada' => 'Mobil Gran Max Box 14 (D 8452 BD)', 'sopir' => 'Pak Bambang Irawan', 'jam_berangkat' => '01:45 WIB', 'titik_tujuan' => 'Rancaekek, Jatinangor, Cileunyi, Ujungberung, Cibiru Bandung', 'total_agen' => 13, 'alokasi_oplah' => 1450, 'status' => 'Siap Jalan'],
            (object)['id' => 15, 'kode' => '15', 'kode_jurusan' => '15', 'nama_jurusan' => 'Jalur Khusus Kantor / Ekspedisi Subuh', 'armada' => 'Mobil Operasional Kantor 15 (Z 1001 KP)', 'sopir' => 'Tim Ekspedisi Internal KP', 'jam_berangkat' => '03:30 WIB', 'titik_tujuan' => 'Bale Kota, Gedung DPRD, Polres, Kodim, Kejaksaan, Agen Khusus', 'total_agen' => 8, 'alokasi_oplah' => 750, 'status' => 'Siap Jalan'],
        ]);
    }

    /**
     * Data default awal 10 agen untuk sinkronisasi ke tabel `agens`.
     */
    public function getDefaultAgenData(): array
    {
        return [
            ['id' => 1, 'tanggal_daftar' => '2024-01-10', 'kode_agen' => 'AG-11-001', 'nama_agen' => 'Kios Koran H. Dadang (Pasar Wetan)', 'alamat' => 'Jl. Pasar Wetan No. 14, Kota Tasikmalaya', 'kategori' => '11', 'kategori_label' => '11 - Agen Besar', 'induk_agen' => '-', 'wilayah_kode' => '01', 'wilayah_nama' => 'Tasikmalaya', 'jurusan_kode' => '06', 'jurusan_nama' => 'Tasikmalaya Kota - Singaparna', 'telepon' => '0812-2345-6789', 'jatah_default' => 450, 'plafon_piutang' => 15000000, 'saldo_piutang' => 3850000, 'status' => 'aktif', 'status_label' => 'Aktif'],
            ['id' => 2, 'tanggal_daftar' => '2024-02-15', 'kode_agen' => 'AG-99-011', 'nama_agen' => 'Loper Mamat Cihideung', 'alamat' => 'Komplek Pertokoan Cihideung Blok B-3, Kota Tasikmalaya', 'kategori' => '99', 'kategori_label' => '99 - Sub Agen', 'induk_agen' => 'Kios Koran H. Dadang (AG-11-001)', 'wilayah_kode' => '01', 'wilayah_nama' => 'Tasikmalaya', 'jurusan_kode' => '06', 'jurusan_nama' => 'Tasikmalaya Kota - Singaparna', 'telepon' => '0852-9876-1234', 'jatah_default' => 80, 'plafon_piutang' => 2000000, 'saldo_piutang' => 640000, 'status' => 'aktif', 'status_label' => 'Aktif'],
            ['id' => 3, 'tanggal_daftar' => '2024-01-12', 'kode_agen' => 'AG-11-002', 'nama_agen' => 'Agen Berkah Ciamis Kota', 'alamat' => 'Jl. Jenderal Sudirman No. 45, Alun-alun Ciamis', 'kategori' => '11', 'kategori_label' => '11 - Agen Besar', 'induk_agen' => '-', 'wilayah_kode' => '03', 'wilayah_nama' => 'Ciamis', 'jurusan_kode' => '07', 'jurusan_nama' => 'Ciamis Kota - Kawali', 'telepon' => '0813-8899-7711', 'jatah_default' => 600, 'plafon_piutang' => 20000000, 'saldo_piutang' => 5200000, 'status' => 'aktif', 'status_label' => 'Aktif'],
            ['id' => 4, 'tanggal_daftar' => '2024-03-01', 'kode_agen' => 'AG-99-021', 'nama_agen' => 'Kios Alun-alun Kawali', 'alamat' => 'Depan Pendopo Kawali, Pasar Kawali, Ciamis', 'kategori' => '99', 'kategori_label' => '99 - Sub Agen', 'induk_agen' => 'Agen Berkah Ciamis Kota (AG-11-002)', 'wilayah_kode' => '03', 'wilayah_nama' => 'Ciamis', 'jurusan_kode' => '07', 'jurusan_nama' => 'Ciamis Kota - Kawali', 'telepon' => '0877-1122-3344', 'jatah_default' => 120, 'plafon_piutang' => 3000000, 'saldo_piutang' => 890000, 'status' => 'aktif', 'status_label' => 'Aktif'],
            ['id' => 5, 'tanggal_daftar' => '2024-01-20', 'kode_agen' => 'AG-11-003', 'nama_agen' => 'Agen Tarogong Prima Garut', 'alamat' => 'Jl. Raya Tarogong No. 102, Simpang Lima Garut', 'kategori' => '11', 'kategori_label' => '11 - Agen Besar', 'induk_agen' => '-', 'wilayah_kode' => '02', 'wilayah_nama' => 'Garut', 'jurusan_kode' => '03', 'jurusan_nama' => 'Garut - Pameungpeuk', 'telepon' => '0812-4455-6677', 'jatah_default' => 550, 'plafon_piutang' => 18000000, 'saldo_piutang' => 4100000, 'status' => 'aktif', 'status_label' => 'Aktif'],
            ['id' => 6, 'tanggal_daftar' => '2024-03-10', 'kode_agen' => 'AG-99-031', 'nama_agen' => 'Kios Subur Pameungpeuk Pesisir', 'alamat' => 'Terminal Pameungpeuk, Garut Selatan', 'kategori' => '99', 'kategori_label' => '99 - Sub Agen', 'induk_agen' => 'Agen Tarogong Prima Garut (AG-11-003)', 'wilayah_kode' => '02', 'wilayah_nama' => 'Garut', 'jurusan_kode' => '03', 'jurusan_nama' => 'Garut - Pameungpeuk', 'telepon' => '0821-3344-5566', 'jatah_default' => 90, 'plafon_piutang' => 2500000, 'saldo_piutang' => 720000, 'status' => 'aktif', 'status_label' => 'Aktif'],
            ['id' => 7, 'tanggal_daftar' => '2024-01-18', 'kode_agen' => 'AG-11-004', 'nama_agen' => 'Kios Sinar Pagi Banjar Patroman', 'alamat' => 'Terminal Kota Banjar Kios No. 05, Banjar', 'kategori' => '11', 'kategori_label' => '11 - Agen Besar', 'induk_agen' => '-', 'wilayah_kode' => '04', 'wilayah_nama' => 'Banjar & Pangandaran', 'jurusan_kode' => '08', 'jurusan_nama' => 'Banjar Patroman - Banjarsari', 'telepon' => '0853-2211-9988', 'jatah_default' => 380, 'plafon_piutang' => 12000000, 'saldo_piutang' => 2950000, 'status' => 'aktif', 'status_label' => 'Aktif'],
            ['id' => 8, 'tanggal_daftar' => '2024-02-05', 'kode_agen' => 'AG-11-005', 'nama_agen' => 'Agen Karangnunggal Sejahtera (Tasela)', 'alamat' => 'Jl. Raya Karangnunggal KM 12, Tasikmalaya Selatan', 'kategori' => '11', 'kategori_label' => '11 - Agen Besar', 'induk_agen' => '-', 'wilayah_kode' => '01', 'wilayah_nama' => 'Tasikmalaya', 'jurusan_kode' => '02', 'jurusan_nama' => 'Tasikmalaya - Cipatujah', 'telepon' => '0813-7766-5544', 'jatah_default' => 320, 'plafon_piutang' => 10000000, 'saldo_piutang' => 2100000, 'status' => 'aktif', 'status_label' => 'Aktif'],
            ['id' => 9, 'tanggal_daftar' => '2023-11-12', 'kode_agen' => 'AG-00-991', 'nama_agen' => 'Ex-Kios Terminal Ciawi (Tutup Usaha)', 'alamat' => 'Ex-Terminal Lama Ciawi, Tasikmalaya Utara', 'kategori' => '00', 'kategori_label' => '00 - Agen Non Aktif', 'induk_agen' => '-', 'wilayah_kode' => '01', 'wilayah_nama' => 'Tasikmalaya', 'jurusan_kode' => '10', 'jurusan_nama' => 'Rajapolah - Cihaurbeuti - Panjalu', 'telepon' => '0811-9988-7766', 'jatah_default' => 0, 'plafon_piutang' => 5000000, 'saldo_piutang' => 1450000, 'status' => 'non-aktif', 'status_label' => 'Non-Aktif (Terkunci)'],
            ['id' => 10, 'tanggal_daftar' => '2023-12-05', 'kode_agen' => 'AG-00-992', 'nama_agen' => 'Ex-Loper Cipatujah Pesisir', 'alamat' => 'Pelabuhan Pamayangsari Cipatujah', 'kategori' => '00', 'kategori_label' => '00 - Agen Non Aktif', 'induk_agen' => 'Agen Karangnunggal Sejahtera (AG-11-005)', 'wilayah_kode' => '01', 'wilayah_nama' => 'Tasikmalaya', 'jurusan_kode' => '02', 'jurusan_nama' => 'Tasikmalaya - Cipatujah', 'telepon' => '0822-4433-2211', 'jatah_default' => 0, 'plafon_piutang' => 2000000, 'saldo_piutang' => 450000, 'status' => 'non-aktif', 'status_label' => 'Non-Aktif (Terkunci)'],
        ];
    }

    /**
     * Master Agen Kabar Priangan (tersinkronisasi 2 arah dengan tabel `agens`).
     */
    public function getDaftarAgen(): Collection
    {
        $defaults = $this->getDefaultAgenData();
        $defaultByKode = collect($defaults)->keyBy('kode_agen');

        if (!Schema::hasTable('agens')) {
            return collect($defaults)->map(fn($a) => (object) $a);
        }

        $deletedCodes = Cache::get('sirkulasi_deleted_agen_codes', []);
        $metaMap = Cache::get('sirkulasi_agen_meta', []);

        foreach ($defaults as $def) {
            if (in_array($def['kode_agen'], $deletedCodes, true)) {
                continue;
            }
            if (!isset($metaMap[$def['kode_agen']])) {
                $metaMap[$def['kode_agen']] = [
                    'tanggal_daftar' => $def['tanggal_daftar'],
                    'alamat'         => $def['alamat'],
                    'wilayah_kode'   => $def['wilayah_kode'],
                    'wilayah_nama'   => $def['wilayah_nama'],
                    'jurusan_kode'   => $def['jurusan_kode'],
                    'jurusan_nama'   => $def['jurusan_nama'],
                    'kategori_asli'  => $def['kategori'] === '00' ? '11' : $def['kategori'],
                ];
            }
            Agen::firstOrCreate(
                ['kode_agen' => $def['kode_agen']],
                [
                    'nama_agen'      => $def['nama_agen'],
                    'kategori'       => $def['kategori'],
                    'induk_agen'     => $def['induk_agen'],
                    'rute_kode'      => $def['jurusan_kode'],
                    'wilayah'        => $def['wilayah_nama'],
                    'telepon'        => $def['telepon'],
                    'jatah_default'  => $def['jatah_default'],
                    'plafon_piutang' => $def['plafon_piutang'],
                    'saldo_piutang'  => $def['saldo_piutang'],
                    'status'         => $def['status'] === 'aktif' ? 'aktif' : 'nonaktif',
                ]
            );
        }
        Cache::put('sirkulasi_agen_meta', $metaMap, now()->addDays(30));

        $wilayahByKode = $this->getDaftarWilayah()->keyBy('kode');
        $wilayahByNama = $this->getDaftarWilayah()->keyBy('nama');
        $jurusanByKode = $this->getDaftarJurusan()->keyBy('kode');

        return Agen::orderBy('id')->get()->map(function (Agen $dbAgen) use ($defaultByKode, $metaMap, $wilayahByKode, $wilayahByNama, $jurusanByKode) {
            $def = $defaultByKode->get($dbAgen->kode_agen, []);
            $meta = $metaMap[$dbAgen->kode_agen] ?? [];

            $isNonAktif = ($dbAgen->status !== 'aktif' || $dbAgen->kategori === '00');
            $kategori = $isNonAktif ? '00' : ($dbAgen->kategori ?: ($def['kategori'] ?? '11'));
            $kategoriLabel = match ($kategori) {
                '00' => '00 - Agen Non Aktif',
                '99' => '99 - Sub Agen',
                default => '11 - Agen Besar',
            };

            $jurusanKode = $meta['jurusan_kode'] ?? $dbAgen->rute_kode ?? ($def['jurusan_kode'] ?? '06');
            if (str_starts_with((string) $jurusanKode, 'R-')) {
                $jurusanKode = substr((string) $jurusanKode, 2);
            }
            $jurusanObj = $jurusanByKode->get($jurusanKode);
            $jurusanNama = $meta['jurusan_nama'] ?? ($jurusanObj->nama_jurusan ?? ($def['jurusan_nama'] ?? 'Tasikmalaya Kota - Singaparna'));

            $wilayahKode = $meta['wilayah_kode'] ?? ($def['wilayah_kode'] ?? null);
            if (!$wilayahKode && $dbAgen->wilayah) {
                $wilLower = strtolower($dbAgen->wilayah);
                $foundWil = $wilayahByNama->first(function ($w) use ($wilLower) {
                    $wNama = strtolower($w->nama);
                    return str_contains($wNama, $wilLower) || str_contains($wilLower, $wNama);
                });
                $wilayahKode = $foundWil->kode ?? '01';
            }
            $wilayahKode = $wilayahKode ?: '01';
            $wilayahObj = $wilayahByKode->get($wilayahKode);
            $wilayahNama = $meta['wilayah_nama'] ?? $dbAgen->wilayah ?? ($wilayahObj->nama ?? ($def['wilayah_nama'] ?? 'Tasikmalaya'));

            $alamat = $meta['alamat'] ?? ($def['alamat'] ?? ('Wilayah ' . $wilayahNama));
            $tanggalDaftar = $meta['tanggal_daftar'] ?? ($def['tanggal_daftar'] ?? ($dbAgen->created_at ? $dbAgen->created_at->format('Y-m-d') : date('Y-m-d')));

            return (object)[
                'id'             => $dbAgen->id,
                'tanggal_daftar' => $tanggalDaftar,
                'kode_agen'      => $dbAgen->kode_agen,
                'nama_agen'      => $dbAgen->nama_agen,
                'alamat'         => $alamat,
                'kategori'       => $kategori,
                'kategori_label' => $kategoriLabel,
                'induk_agen'     => $dbAgen->induk_agen ?: '-',
                'wilayah_kode'   => $wilayahKode,
                'wilayah_nama'   => $wilayahNama,
                'jurusan_kode'   => $jurusanKode,
                'jurusan_nama'   => $jurusanNama,
                'telepon'        => $dbAgen->telepon ?: '-',
                'jatah_default'  => $isNonAktif ? 0 : (int) $dbAgen->jatah_default,
                'plafon_piutang' => (float) $dbAgen->plafon_piutang,
                'saldo_piutang'  => (float) $dbAgen->saldo_piutang,
                'status'         => $isNonAktif ? 'non-aktif' : 'aktif',
                'status_label'   => $isNonAktif ? 'Non-Aktif (Terkunci)' : 'Aktif',
            ];
        })->values();
    }

    /**
     * Helper lookup Faktur Sirkulasi berdasarkan nomor faktur untuk integrasi scan Kasir.
     */
    public function getFakturPayloadByNo(string $noFaktur): ?array
    {
        $fakturBulanan = SirkulasiFakturBulanan::where('no_faktur', $noFaktur)->first();
        if ($fakturBulanan) {
            $agen = Agen::find($fakturBulanan->agen_id);
            return [
                'app'             => 'SI-KABARPRIANGAN',
                'tipe'            => 'FAKTUR_SIRKULASI',
                'sumber'          => 'sirkulasi',
                'no_faktur'       => $fakturBulanan->no_faktur,
                'faktur'          => $fakturBulanan->no_faktur,
                'nama_agen'       => $fakturBulanan->nama_agen,
                'kode_agen'       => $fakturBulanan->kode_agen,
                'penjualan_masuk' => (float) $fakturBulanan->total_tagihan_bersih,
                'harga_bayar'     => (float) $fakturBulanan->total_tagihan_bersih,
                'total_tagihan'   => (float) $fakturBulanan->total_tagihan_bersih,
                'total_penjualan' => (float) $fakturBulanan->total_tagihan_bersih,
                'terjual_eks'     => (int) $fakturBulanan->oplah_bersih,
                'nett_sales'      => (int) $fakturBulanan->oplah_bersih,
                'kirim_eks'       => (int) $fakturBulanan->total_oplah_kirim,
                'retur_eks'       => (int) $fakturBulanan->total_retur_eks,
                'harga_satuan'    => (float) $fakturBulanan->tarif_per_eks,
                'saldo_piutang'   => (float) $fakturBulanan->sisa_piutang,
                'customer'        => $fakturBulanan->nama_agen,
                'alamat'          => $agen ? $agen->alamat : '',
                'telepon'         => $agen ? $agen->telepon : '',
                'wilayah'         => $agen ? ($agen->wilayah ?? '') : '',
                'jurusan'         => $agen ? ($agen->rute_kode ?? '') : '',
                'kategori'        => 'sirkulasi',
                'status'          => $fakturBulanan->status_pembayaran === 'lunas' ? 'lunas' : 'belum_bayar',
                'periode'         => $fakturBulanan->periode_bulan,
                'ringkasan_kasir' => "FAKTUR: {$fakturBulanan->no_faktur} | AGEN: {$fakturBulanan->nama_agen} | TAGIHAN BERSIH: Rp " . number_format($fakturBulanan->total_tagihan_bersih, 0, ',', '.') . " ({$fakturBulanan->oplah_bersih} EKS NETTO)",
                'keterangan'      => "Tagihan Sirkulasi Koran Bulanan {$fakturBulanan->periode_bulan} Netto Rp " . number_format($fakturBulanan->total_tagihan_bersih, 0, ',', '.'),
                'rincian_pesanan' => [
                    [
                        'item'     => "Koran Harian Kabar Priangan ({$fakturBulanan->oplah_bersih} eks netto)",
                        'qty'      => $fakturBulanan->oplah_bersih,
                        'harga'    => $fakturBulanan->tarif_per_eks,
                        'subtotal' => $fakturBulanan->total_tagihan_bersih,
                    ],
                ],
                'api_url'         => url('/api/faktur/' . rawurlencode($fakturBulanan->no_faktur)),
            ];
        }

        $daftarAgen = $this->getDaftarAgen();
        if (preg_match('/FKT-SRK-(\d{4})(\d{2})-(\d+)/', $noFaktur, $matches)) {
            $tahun = $matches[1];
            $bulan = $matches[2];
            $idAgen = (int) $matches[3];
            $bulanPilihan = "{$tahun}-{$bulan}";

            $agen = $daftarAgen->firstWhere('id', $idAgen);
            if ($agen) {
                $kirim = $agen->jatah_default * 30;
                $retur = (int) ($kirim * 0.022);
                $nett = $kirim - $retur;
                $harga = $agen->kategori === '11' ? 2800 : 3000;
                $nilaiTagihan = $nett * $harga;

                return [
                    'app'             => 'SI-KABARPRIANGAN',
                    'tipe'            => 'FAKTUR_SIRKULASI',
                    'sumber'          => 'sirkulasi',
                    'no_faktur'       => $noFaktur,
                    'faktur'          => $noFaktur,
                    'nama_agen'       => $agen->nama_agen,
                    'kode_agen'       => $agen->kode_agen,
                    'penjualan_masuk' => (float) $nilaiTagihan,
                    'harga_bayar'     => (float) $nilaiTagihan,
                    'total_tagihan'   => (float) $nilaiTagihan,
                    'total_penjualan' => (float) $nilaiTagihan,
                    'terjual_eks'     => $nett,
                    'nett_sales'      => $nett,
                    'kirim_eks'       => $kirim,
                    'retur_eks'       => $retur,
                    'harga_satuan'    => $harga,
                    'saldo_piutang'   => (float) $agen->saldo_piutang,
                    'customer'        => $agen->nama_agen,
                    'alamat'          => $agen->alamat,
                    'telepon'         => $agen->telepon,
                    'wilayah'         => $agen->wilayah_nama,
                    'jurusan'         => $agen->jurusan_nama,
                    'kategori'        => 'sirkulasi',
                    'status'          => $agen->status === 'aktif' ? 'belum_bayar' : 'terkunci',
                    'periode'         => $bulanPilihan,
                    'ringkasan_kasir' => "FAKTUR: {$noFaktur} | AGEN: {$agen->nama_agen} | PENJUALAN MASUK: Rp " . number_format($nilaiTagihan, 0, ',', '.') . " ({$nett} EKS TERJUAL)",
                    'keterangan'      => "Penjualan koran masuk sebesar Rp " . number_format($nilaiTagihan, 0, ',', '.') . " ({$nett} eks)",
                    'rincian_pesanan' => [
                        [
                            'item'     => "Koran Harian Kabar Priangan ({$nett} eks)",
                            'qty'      => $nett,
                            'harga'    => $harga,
                            'subtotal' => $nilaiTagihan,
                        ],
                    ],
                    'api_url'         => url('/api/faktur/' . rawurlencode($noFaktur)),
                ];
            }
        }

        return null;
    }
}
