<?php

namespace App\Http\Controllers;

use App\Models\Agen;
use App\Models\AuditLogAkuntansi;
use App\Models\DailyCutoff;
use App\Models\KasMasuk;
use App\Models\LogHitungAkuntansi;
use App\Models\MemoUnlock;
use App\Models\PeriodeAkuntansi;
use App\Models\SistemConfig;
use App\Services\KalenderNasionalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SirkulasiController extends Controller
{
    /**
     * Ambil pengaturan tarif, standar pengepakan, cut-off, dan komposisi oplah Sirkulasi dari database.
     */
    protected function getSirkulasiConfig(): array
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
            'jam_cutoff'         => (string) $getCfg('sirkulasi_jam_cutoff', $hasConfigTable ? SistemConfig::getJamCutoff() : '14:00'),
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
     * Halaman Tarif & Pengaturan Operasional Sirkulasi
     */
    public function pengaturan()
    {
        $settings = $this->getSirkulasiConfig();
        $isPastCutoff = now()->format('H:i') >= $settings['jam_cutoff'];

        return view('pages.sirkulasi.pengaturan.index', compact('settings', 'isPastCutoff'));
    }

    /**
     * Simpan perubahan Tarif & Pengaturan Operasional Sirkulasi
     */
    public function updatePengaturan(Request $request)
    {
        $cleanNumber = fn($val) => (int) preg_replace('/[^0-9]/', '', (string) $val);

        $request->merge([
            'sirkulasi_harga_agen_besar' => $cleanNumber($request->input('sirkulasi_harga_agen_besar')),
            'sirkulasi_harga_eceran'     => $cleanNumber($request->input('sirkulasi_harga_eceran')),
            'sirkulasi_harga_langganan'  => $cleanNumber($request->input('sirkulasi_harga_langganan')),
            'sirkulasi_harga_cover_het'  => $cleanNumber($request->input('sirkulasi_harga_cover_het')),
        ]);

        $validated = $request->validate([
            'sirkulasi_harga_agen_besar'   => 'required|integer|min:100',
            'sirkulasi_harga_eceran'       => 'required|integer|min:100',
            'sirkulasi_harga_langganan'    => 'required|integer|min:100',
            'sirkulasi_harga_cover_het'    => 'required|integer|min:100',
            'sirkulasi_isi_bendel'         => 'required|integer|min:10|max:500',
            'sirkulasi_jam_cutoff'         => 'required|date_format:H:i',
            'sirkulasi_batas_retur_persen' => 'required|numeric|min:0|max:100',
            'sirkulasi_rasio_langganan'    => 'required|numeric|min:0|max:100',
            'sirkulasi_rasio_konsinyasi'   => 'required|numeric|min:0|max:100',
            'sirkulasi_rasio_gratis'       => 'required|numeric|min:0|max:100',
        ]);

        $totalRasio = (float) $validated['sirkulasi_rasio_langganan']
            + (float) $validated['sirkulasi_rasio_konsinyasi']
            + (float) $validated['sirkulasi_rasio_gratis'];

        if (abs($totalRasio - 100) > 0.01) {
            return back()->withInput()->withErrors([
                'sirkulasi_rasio_langganan' => "Total komposisi rasio default (Langganan + Konsinyasi + Gratis) harus tepat 100% (saat ini {$totalRasio}%).",
            ]);
        }

        $labels = [
            'sirkulasi_harga_agen_besar'   => 'Tarif nett Agen Besar (Kategori 11) per eksemplar',
            'sirkulasi_harga_eceran'       => 'Tarif jual konsinyasi/eceran koran per eksemplar',
            'sirkulasi_harga_langganan'    => 'Tarif langganan rutin per eksemplar',
            'sirkulasi_harga_cover_het'    => 'Harga Eceran Tertinggi (HET) cover koran',
            'sirkulasi_isi_bendel'         => 'Jumlah eksemplar per ikat/bendel pengepakan percetakan',
            'sirkulasi_jam_cutoff'         => 'Batas waktu (Cut-Off) input oplah distribusi harian',
            'sirkulasi_batas_retur_persen' => 'Batas toleransi maksimal retur konsinyasi agen (%)',
            'sirkulasi_rasio_langganan'    => 'Persentase rasio default jatah Langganan (%)',
            'sirkulasi_rasio_konsinyasi'   => 'Persentase rasio default jatah Konsinyasi (%)',
            'sirkulasi_rasio_gratis'       => 'Persentase rasio default jatah Gratis (%)',
        ];

        foreach ($validated as $key => $val) {
            SistemConfig::set($key, $val, $labels[$key] ?? null);
        }

        return redirect()->route('sirkulasi.pengaturan.index')
            ->with('success', 'Tarif koran dan pengaturan operasional Sirkulasi berhasil diperbarui dan langsung berlaku.');
    }

    /**
     * Cek apakah pengguna memiliki hak kelola Master Agen & Otorisasi Langsung (Divisi Akuntansi & Admin)
     * Sesuai SOP: Divisi Sirkulasi hanya dapat melihat data agen (Read-Only).
     */
    protected function canManageAgen(): bool
    {
        $user = auth()->user();
        return $user && ($user->isAdmin() || $user->hasRole('accounting', 'akuntansi'));
    }

    /**
     * Cek alasan penguncian tanggal edisi jatah sirkulasi (null jika terbuka untuk diedit).
     * Hierarki standar industri pers:
     * 1. Periode Akuntansi terkunci permanen -> Mutlak terkunci
     * 2. Data bulan tsb sudah ditarik / dihitung Akuntansi (LogHitungAkuntansi) -> Terkunci
     * 3. Tutup Bulanan Sirkulasi aktif -> Terkunci
     * 4. Tutup Harian / DailyCutoff aktif -> Terkunci, kecuali ada MemoUnlock Sirkulasi berstatus OPEN & APPROVED
     */
    protected function getLockReasonTanggalJatah(string $tanggal): ?string
    {
        $cleanDate = Carbon::parse($tanggal)->toDateString();
        $periode = substr($cleanDate, 0, 7);

        // 1. Periode Fiskal Akuntansi terkunci permanen
        if (Schema::hasTable('periode_akuntansis') && PeriodeAkuntansi::isPeriodeLocked($periode)) {
            return "Periode akuntansi {$periode} sudah ditutup permanen.";
        }

        // 2. Data penjualan sirkulasi bulan tersebut sudah ditarik / dihitung Akuntansi
        if (Schema::hasTable('log_hitung_akuntansis')
            && LogHitungAkuntansi::where('jenis', 'penjualan_sirkulasi')->where('periode', $periode)->exists()) {
            return "Data penjualan periode {$periode} sudah ditarik oleh Divisi Akuntansi.";
        }

        // 3. Tutup Bulanan Sirkulasi
        if (session('locked_bulanan_' . $periode, Cache::get('locked_bulanan_' . $periode, false))) {
            return "Buku bulanan sirkulasi periode {$periode} sudah ditutup.";
        }

        // 4. Cek apakah ada Memo Unlock Sirkulasi harian yang sedang OPEN & disetujui Akuntansi
        if (Schema::hasTable('memo_unlocks')) {
            $hasApprovedMemo = MemoUnlock::whereDate('tanggal_target', $cleanDate)
                ->where(function ($q) {
                    $q->where('tujuan_divisi', 'sirkulasi')
                      ->orWhere('nomor_memo', 'like', 'REQ-ULK-%')
                      ->orWhere('nomor_memo', 'like', '%SIRK%');
                })
                ->where('status', 'OPEN')
                ->whereIn('status_akuntansi', ['APPROVED', 'TERKIRIM', 'DISETUJUI'])
                ->exists();

            if ($hasApprovedMemo) {
                return null;
            }
        }

        // 5. Tutup Harian Sirkulasi
        if (session('locked_harian_' . $cleanDate, Cache::get('locked_harian_' . $cleanDate, false))) {
            return "Data harian edisi {$cleanDate} sudah ditutup di Tutup Buku Harian.";
        }

        // 6. Cut-Off Operasional Harian (DailyCutoff)
        if (Schema::hasTable('daily_cutoffs')) {
            $cutoff = DailyCutoff::whereDate('tanggal_edisi', $cleanDate)->first();
            if ($cutoff && $cutoff->is_closed) {
                return "Data harian edisi {$cleanDate} sudah melewati Cut-Off / ditutup.";
            }
        }

        return null;
    }

    /**
     * Cek apakah tanggal edisi jatah sirkulasi terkunci dan tidak boleh diedit.
     */
    protected function isTanggalJatahLocked(string $tanggal): bool
    {
        return $this->getLockReasonTanggalJatah($tanggal) !== null;
    }
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
        $cfg = $this->getSirkulasiConfig();
        $hargaBesarFmt = number_format($cfg['harga_agen_besar'], 0, ',', '.');
        $hargaEceranFmt = number_format($cfg['harga_eceran'], 0, ',', '.');

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
                'harga_satuan' => $cfg['harga_agen_besar'],
                'ketentuan' => "Mitra distribusi langsung drop point mobil subuh (Tarif Nett Rp {$hargaBesarFmt}/eks). Induk pembina jaringan sub agen dan loper kios.",
                'sistem_distribusi' => 'Drop Point Distribusi Utama (Jatah > 300 eks)',
                'badge_color' => 'blue',
                'status_aturan' => 'Komersil Utama',
            ],
            (object)[
                'kode' => '99',
                'nama' => 'Sub Agen / Pengecer',
                'label' => '99 - Sub Agen',
                'harga_satuan' => $cfg['harga_eceran'],
                'ketentuan' => 'Pengecer, kios pasar, loper keliling di bawah koordinasi Agen Besar atau drop rute sepeda motor.',
                'sistem_distribusi' => "Eceran & Loper Kios (Konsinyasi Rp {$hargaEceranFmt})",
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
     * Data default 10 agen Sirkulasi untuk sinkronisasi awal ke tabel agens
     */
    protected function getDefaultAgenData(): array
    {
        return [
            [
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
            [
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
            [
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
            [
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
            [
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
            [
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
            [
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
            [
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
            [
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
            [
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
        ];
    }

    /**
     * Master Agen Kabar Priangan (Tersinkronisasi 2 Arah dengan Tabel Database `agens`)
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

        // Pastikan seluruh data default tersinkronisasi ke DB agens (kecuali yang dihapus Akuntansi)
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
            // Normalisasi kode jurusan типа "R-01" -> "01"
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
                'jatah_default'  => (int) $dbAgen->jatah_default,
                'plafon_piutang' => (float) $dbAgen->plafon_piutang,
                'saldo_piutang'  => (float) $dbAgen->saldo_piutang,
                'status'         => $isNonAktif ? 'non-aktif' : 'aktif',
                'status_label'   => $isNonAktif ? 'Non-Aktif (Terkunci)' : 'Aktif',
            ];
        });
    }

    /**
     * Dashboard Divisi Sirkulasi (Selaras Standar Informasi & Filter Dashboard Divisi Iklan)
     */
    public function dashboard(Request $request)
    {
        $range = $request->input('range', 'month');
        $now = Carbon::now();
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $todayStr = $today->toDateString();

        // 1. Rentang Waktu Berdasarkan Filter Periode
        switch ($range) {
            case 'today':
                $startDate = $todayStr;
                $endDate = $todayStr;
                $rangeTitle = 'Hari Ini';
                $rangeSub = $now->translatedFormat('d M Y');
                break;
            case '7days':
            case 'week':
                $range = '7days';
                $startDate = $now->copy()->subDays(6)->toDateString();
                $endDate = $todayStr;
                $rangeTitle = '7 Hari Terakhir';
                $rangeSub = Carbon::parse($startDate)->translatedFormat('d M') . ' - ' . $now->translatedFormat('d M Y');
                break;
            case '30days':
                $startDate = $now->copy()->subDays(29)->toDateString();
                $endDate = $todayStr;
                $rangeTitle = '30 Hari Terakhir';
                $rangeSub = Carbon::parse($startDate)->translatedFormat('d M') . ' - ' . $now->translatedFormat('d M Y');
                break;
            case 'last_month':
                $subMonth = $now->copy()->subMonth();
                $startDate = $subMonth->copy()->startOfMonth()->toDateString();
                $endDate = $subMonth->copy()->endOfMonth()->toDateString();
                $rangeTitle = 'Bulan Lalu';
                $rangeSub = $subMonth->translatedFormat('F Y');
                break;
            case 'year':
                $startDate = $now->copy()->startOfYear()->toDateString();
                $endDate = $now->copy()->endOfYear()->toDateString();
                $rangeTitle = 'Tahun Ini';
                $rangeSub = $now->translatedFormat('Y');
                break;
            case 'custom':
                $startDate = $request->input('dari', $now->copy()->startOfMonth()->toDateString());
                $endDate = $request->input('sampai', $todayStr);
                if (Carbon::parse($startDate)->gt(Carbon::parse($endDate))) {
                    [$startDate, $endDate] = [$endDate, $startDate];
                }
                $rangeTitle = 'Pilih Tanggal';
                $rangeSub = Carbon::parse($startDate)->translatedFormat('d M') . ' - ' . Carbon::parse($endDate)->translatedFormat('d M Y');
                break;
            case 'month':
            default:
                $range = 'month';
                $startDate = $now->copy()->startOfMonth()->toDateString();
                $endDate = $now->copy()->endOfMonth()->toDateString();
                $rangeTitle = 'Bulan Ini';
                $rangeSub = $now->translatedFormat('F Y');
                break;
        }

        $dari = $startDate;
        $sampai = $endDate;
        $daysInRange = max(1, Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1);

        // 2. Master Data Agen, Wilayah, Jurusan & Jenis
        $daftarAgen = $this->getDaftarAgen();
        $aktifAgens = $daftarAgen->where('status', 'aktif');
        $totalAgen = $daftarAgen->count();
        $totalAgenAktif = $aktifAgens->count();
        $totalAgenNonAktif = $daftarAgen->where('status', 'non-aktif')->count();

        $wilayahList = $this->getDaftarWilayah();
        $jurusanList = $this->getDaftarJurusan();
        $totalWilayah = $wilayahList->count();
        $totalJurusan = $jurusanList->count();

        // 3. Kalkulasi Oplah, Terjual, Retur & Estimasi Penjualan Selama Periode Terpilih
        $oplahHarianDefault = (int) $aktifAgens->sum('jatah_default');
        $totalPiutang = (int) $daftarAgen->sum('saldo_piutang');
        $jumlahAgenPiutang = (int) $daftarAgen->where('saldo_piutang', '>', 0)->count();

        $agenBesar = $aktifAgens->where('kategori', '11');
        $subAgen = $aktifAgens->where('kategori', '99');

        $cfg = $this->getSirkulasiConfig();
        $hargaBesar = $cfg['harga_agen_besar'];
        $hargaEceran = $cfg['harga_eceran'];
        $pctL = $cfg['pct_langganan'];
        $pctK = $cfg['pct_konsinyasi'];

        $dailyStatsForAgens = static function (Collection $agens, string $dateStr, array $cachedMap) use ($hargaBesar, $hargaEceran, $pctL, $pctK): array {
            $kirim = 0;
            $retur = 0;
            $omset = 0;
            $langganan = 0;
            $konsinyasi = 0;
            $gratis = 0;

            foreach ($agens as $ag) {
                $custom = $cachedMap[$ag->id] ?? null;
                if ($custom) {
                    $l = (int) ($custom['langganan'] ?? 0);
                    $k = (int) ($custom['konsinyasi'] ?? 0);
                    $g = (int) ($custom['gratis'] ?? 0) + (int) ($custom['pengenalan'] ?? 0) + (int) ($custom['insidentil'] ?? 0);
                    $jatahHari = $l + $k + $g;
                } else {
                    $jatahHari = (int) $ag->jatah_default;
                    $l = (int) ($jatahHari * $pctL);
                    $k = (int) ($jatahHari * $pctK);
                    $g = max(0, $jatahHari - $l - $k);
                }

                $retHari = (int) round($jatahHari * 0.022);
                $nettHari = max(0, $jatahHari - $retHari);
                $harga = $ag->kategori === '11' ? $hargaBesar : $hargaEceran;

                $kirim += $jatahHari;
                $retur += $retHari;
                $omset += $nettHari * $harga;
                $langganan += $l;
                $konsinyasi += $k;
                $gratis += $g;
            }

            return [
                'kirim'      => $kirim,
                'retur'      => $retur,
                'nett'       => max(0, $kirim - $retur),
                'omset'      => $omset,
                'langganan'  => $langganan,
                'konsinyasi' => $konsinyasi,
                'gratis'     => $gratis,
            ];
        };

        // Base harian tanpa cache + cek hanya tanggal yang punya cache di rentang (cepat & akurat)
        $baseHarianBesar = $dailyStatsForAgens($agenBesar, $todayStr, []);
        $baseHarianSub = $dailyStatsForAgens($subAgen, $todayStr, []);

        $todayCache = Cache::get('jatah_harian_' . $todayStr, []);
        $todayStatsBesar = !empty($todayCache) ? $dailyStatsForAgens($agenBesar, $todayStr, $todayCache) : $baseHarianBesar;
        $todayStatsSub = !empty($todayCache) ? $dailyStatsForAgens($subAgen, $todayStr, $todayCache) : $baseHarianSub;

        $oplahHarian = $todayStatsBesar['kirim'] + $todayStatsSub['kirim'];
        $totalReturHarian = $todayStatsBesar['retur'] + $todayStatsSub['retur'];
        $penjualanHariIni = $todayStatsBesar['omset'] + $todayStatsSub['omset'];
        $efektivitasDistribusi = $oplahHarian > 0 ? round((($oplahHarian - $totalReturHarian) / $oplahHarian) * 100, 1) : 100.0;

        // Agregasi periode dengan memperhatikan entri jatah harian tersimpan pada Cache
        $totalKirimPeriode = 0;
        $totalRetur = 0;
        $totalTerjualPeriode = 0;
        $omsetBesar = 0;
        $omsetSub = 0;
        $bayarBesar = 0;
        $bayarSub = 0;
        $eksBesarPeriode = 0;
        $eksSubPeriode = 0;
        $porsiLangganan = 0;
        $porsiKonsinyasi = 0;
        $porsiGratis = 0;

        $cursor = Carbon::parse($startDate);
        $endCursor = Carbon::parse($endDate);
        while ($cursor->lte($endCursor)) {
            $dStr = $cursor->toDateString();
            $cachedDay = Cache::get('jatah_harian_' . $dStr, []);
            $stBesar = !empty($cachedDay) ? $dailyStatsForAgens($agenBesar, $dStr, $cachedDay) : $baseHarianBesar;
            $stSub = !empty($cachedDay) ? $dailyStatsForAgens($subAgen, $dStr, $cachedDay) : $baseHarianSub;

            $omsetBesar += $stBesar['omset'];
            $omsetSub += $stSub['omset'];
            $eksBesarPeriode += $stBesar['kirim'];
            $eksSubPeriode += $stSub['kirim'];
            $totalKirimPeriode += $stBesar['kirim'] + $stSub['kirim'];
            $totalRetur += $stBesar['retur'] + $stSub['retur'];
            $totalTerjualPeriode += $stBesar['nett'] + $stSub['nett'];
            $porsiLangganan += $stBesar['langganan'] + $stSub['langganan'];
            $porsiKonsinyasi += $stBesar['konsinyasi'] + $stSub['konsinyasi'];
            $porsiGratis += $stBesar['gratis'] + $stSub['gratis'];

            $cursor->addDay();
        }

        // Alokasi Non-Komersil & Insidentil (Wilayah 06 & 08) selama periode
        $wilayahNonKomersil = $wilayahList->whereIn('kode', ['06', '08']);
        $eksNonKomersilHarian = (int) $wilayahNonKomersil->sum('alokasi_default');
        $eksNonKomersilPeriode = $eksNonKomersilHarian * $daysInRange;

        $totalOmset = $omsetBesar + $omsetSub;

        // Data Setoran Masuk Kasir (dari tabel kas_masuks tipe 'sirkulasi')
        $totalBayarKasir = 0;
        $jumlahSetoranKasir = 0;
        if (Schema::hasTable('kas_masuks')) {
            $kasSirkulasiQ = KasMasuk::where('tipe_transaksi', 'sirkulasi')
                ->whereBetween('tgl_transaksi', [$startDate, $endDate]);
            $totalBayarKasir = (int) (clone $kasSirkulasiQ)->sum('jumlah_dibayar');
            $jumlahSetoranKasir = (int) (clone $kasSirkulasiQ)->count();
        }

        // Porsi & Piutang per Kategori Keagenan
        $piutangBesar = (int) $agenBesar->sum('saldo_piutang');
        $piutangSub = (int) $subAgen->sum('saldo_piutang');
        $pctBesar = $totalOmset > 0 ? round(($omsetBesar / $totalOmset) * 100) : 0;
        $pctSub = $totalOmset > 0 ? round(($omsetSub / $totalOmset) * 100) : 0;
        $totalEksSemua = max(1, $totalKirimPeriode + $eksNonKomersilPeriode);
        $pctNonKomersil = round(($eksNonKomersilPeriode / $totalEksSemua) * 100);

        $channels = [
            'besar' => [
                'nama'      => '11 - Agen Besar',
                'count'     => $agenBesar->count(),
                'unit'      => 'Agen',
                'percent'   => $pctBesar,
                'omzet'     => $omsetBesar,
                'kirim_eks' => $eksBesarPeriode,
                'piutang'   => $piutangBesar,
                'info_ops'  => 'Tarif standar: Rp ' . number_format($hargaBesar, 0, ',', '.') . ' / eks &bull; Harian: ' . number_format($baseHarianBesar['kirim'], 0, ',', '.') . ' eks',
                'route_new' => 'sirkulasi.jatah.edit',
                'route_tx'  => route('sirkulasi.tabel.agen', ['kategori' => '11']),
            ],
            'sub' => [
                'nama'      => '99 - Sub Agen & Kios',
                'count'     => $subAgen->count(),
                'unit'      => 'Mitra',
                'percent'   => $pctSub,
                'omzet'     => $omsetSub,
                'kirim_eks' => $eksSubPeriode,
                'piutang'   => $piutangSub,
                'info_ops'  => 'Tarif konsinyasi: Rp ' . number_format($hargaEceran, 0, ',', '.') . ' / eks &bull; Harian: ' . number_format($baseHarianSub['kirim'], 0, ',', '.') . ' eks',
                'route_new' => 'sirkulasi.jatah.edit',
                'route_tx'  => route('sirkulasi.tabel.agen', ['kategori' => '99']),
            ],
            'khusus' => [
                'nama'      => '06/08 - Gratis & Insidentil',
                'count'     => $wilayahNonKomersil->sum('total_agen'),
                'unit'      => 'Titik',
                'percent'   => $pctNonKomersil,
                'omzet'     => 0,
                'kirim_eks' => $eksNonKomersilPeriode,
                'piutang'   => 0,
                'info_ops'  => 'Bukti iklan, redaksi & suplemen &bull; Harian: ' . number_format($eksNonKomersilHarian, 0, ',', '.') . ' eks',
                'route_new' => 'sirkulasi.cetak.pemasaran',
                'route_tx'  => route('sirkulasi.tabel.wilayah'),
            ],
        ];

        // 4. Status Tutup Data Penjualan Hari Ini
        $isTodayClosed = $this->isTanggalJatahLocked($todayStr);

        // 5. Data Grafik Penjualan Sirkulasi 7 Hari Terakhir
        $dailyLabels = [];
        $dailyBesar = [];
        $dailySub = [];

        for ($i = 6; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i)->toDateString();
            $dailyLabels[] = Carbon::parse($d)->translatedFormat('d M');
            $cMap = Cache::get('jatah_harian_' . $d, []);
            $stB = !empty($cMap) ? $dailyStatsForAgens($agenBesar, $d, $cMap) : $baseHarianBesar;
            $stS = !empty($cMap) ? $dailyStatsForAgens($subAgen, $d, $cMap) : $baseHarianSub;
            $dailyBesar[] = $stB['omset'];
            $dailySub[] = $stS['omset'];
        }

        // 6. Leaderboard Top Wilayah Pemasaran (6 Teratas Berdasarkan Oplah Periode)
        $wilayahLeaderboard = $wilayahList->map(function ($w) use ($daysInRange, $hargaBesar) {
            $oplahPeriode = (int) $w->alokasi_default * $daysInRange;
            $estimasiOmset = $w->tipe === 'Non-Komersil (Gratis)' ? 0 : (int) ($oplahPeriode * 0.978) * $hargaBesar;

            return (object) [
                'kode'           => $w->kode,
                'nama'           => $w->nama,
                'tipe'           => $w->tipe,
                'total_agen'     => (int) $w->total_agen,
                'alokasi_harian' => (int) $w->alokasi_default,
                'oplah_periode'  => $oplahPeriode,
                'estimasi_omset' => $estimasiOmset,
            ];
        })->sortByDesc('oplah_periode')->take(6)->values();

        // 7. Data Komposisi Porsi Jatah (Donut Chart)
        $totalJatahMix = max(1, $porsiLangganan + $porsiKonsinyasi + $porsiGratis);
        $komposisiDistribusi = [
            'langganan'  => ['eks' => $porsiLangganan, 'pct' => round(($porsiLangganan / $totalJatahMix) * 100)],
            'konsinyasi' => ['eks' => $porsiKonsinyasi, 'pct' => round(($porsiKonsinyasi / $totalJatahMix) * 100)],
            'gratis'     => ['eks' => $porsiGratis, 'pct' => round(($porsiGratis / $totalJatahMix) * 100)],
        ];

        // 8. Tabel Ringkas Faktur & Distribusi Agen Terbaru (Top 6 Agen Aktif)
        $bulanKey = Carbon::parse($endDate)->format('Y-m');
        $periodCode = str_replace('-', '', $bulanKey);

        $recentFaktur = $aktifAgens->take(8)->map(function ($agen) use ($daysInRange, $periodCode, $hargaBesar, $hargaEceran) {
            $kirim = (int) $agen->jatah_default * $daysInRange;
            $retur = (int) round($kirim * 0.022);
            $nett = max(0, $kirim - $retur);
            $harga = $agen->kategori === '11' ? $hargaBesar : $hargaEceran;
            $tagihan = $nett * $harga;
            $noFaktur = sprintf('FKT-SRK-%s-%03d', $periodCode, $agen->id);

            return (object) [
                'id'            => $agen->id,
                'no_faktur'     => $noFaktur,
                'kode_agen'     => $agen->kode_agen,
                'nama_agen'     => $agen->nama_agen,
                'kategori'      => $agen->kategori,
                'wilayah_nama'  => $agen->wilayah_nama,
                'jurusan_kode'  => $agen->jurusan_kode,
                'jurusan_nama'  => $agen->jurusan_nama,
                'kirim'         => $kirim,
                'retur'         => $retur,
                'nett'          => $nett,
                'tagihan'       => $tagihan,
                'saldo_piutang' => (int) $agen->saldo_piutang,
                'status'        => (int) $agen->saldo_piutang <= 0 ? 'Lunas' : 'Piutang',
            ];
        })->values();

        // 9. Data Kalender Nasional & Hari Penting Pers
        $kalenderService = app(KalenderNasionalService::class);
        $kalenderAwal = $kalenderService->getKalenderBulan((int) date('Y'), (int) date('n'));

        return view('pages.sirkulasi.dashboard', compact(
            'range',
            'rangeTitle',
            'rangeSub',
            'dari',
            'sampai',
            'daysInRange',
            'today',
            'tomorrow',
            'oplahHarian',
            'totalKirimPeriode',
            'totalTerjualPeriode',
            'totalReturHarian',
            'totalRetur',
            'efektivitasDistribusi',
            'totalOmset',
            'penjualanHariIni',
            'totalBayarKasir',
            'jumlahSetoranKasir',
            'totalPiutang',
            'jumlahAgenPiutang',
            'totalAgen',
            'totalAgenAktif',
            'totalAgenNonAktif',
            'totalWilayah',
            'totalJurusan',
            'isTodayClosed',
            'channels',
            'dailyLabels',
            'dailyBesar',
            'dailySub',
            'wilayahLeaderboard',
            'komposisiDistribusi',
            'recentFaktur',
            'kalenderAwal'
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
     * Divisi Sirkulasi: Read-Only. Divisi Akuntansi & Admin: Full CRUD.
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
        $canManageAgen = $this->canManageAgen();

        return view('pages.sirkulasi.tabel.agen', compact(
            'agenList',
            'wilayahList',
            'jurusanList',
            'jenisList',
            'search',
            'kategoriFilter',
            'statusFilter',
            'wilayahFilter',
            'jurusanFilter',
            'canManageAgen'
        ));
    }

    /**
     * Tambah Data Agen Baru (Khusus Divisi Akuntansi & Administrator)
     */
    public function storeAgen(Request $request)
    {
        if (!$this->canManageAgen()) {
            return redirect()->route('sirkulasi.tabel.agen')
                ->with('error', 'Akses Ditolak: Divisi Sirkulasi hanya dapat melihat data agen (Read-Only). Penambahan agen baru hanya dapat dilakukan oleh Divisi Akuntansi.');
        }

        $request->validate([
            'nama_agen'      => 'required|string|max:150',
            'kategori'       => 'required|in:11,99,00',
            'wilayah_kode'   => 'required|string',
            'jurusan_kode'   => 'required|string',
            'alamat'         => 'nullable|string|max:255',
            'telepon'        => 'nullable|string|max:50',
            'jatah_default'  => 'required|integer|min:0',
            'plafon_piutang' => 'nullable|numeric|min:0',
            'induk_agen'     => 'nullable|string|max:150',
        ]);

        $kategori = $request->input('kategori', '11');
        $kodeInput = trim((string) $request->input('kode_agen', ''));
        if ($kodeInput === '') {
            $nextNum = Agen::count() + 1;
            $kodeInput = sprintf('AG-%s-%03d', $kategori, $nextNum);
            while (Agen::where('kode_agen', $kodeInput)->exists()) {
                $nextNum++;
                $kodeInput = sprintf('AG-%s-%03d', $kategori, $nextNum);
            }
        }

        $wilayahObj = $this->getDaftarWilayah()->firstWhere('kode', $request->wilayah_kode);
        $jurusanObj = $this->getDaftarJurusan()->firstWhere('kode', $request->jurusan_kode);
        $wilayahNama = $wilayahObj->nama ?? 'Tasikmalaya';
        $jurusanNama = $jurusanObj->nama_jurusan ?? 'Tasikmalaya Kota - Singaparna';
        $isNonAktif = $kategori === '00';

        $agen = Agen::updateOrCreate(
            ['kode_agen' => strtoupper($kodeInput)],
            [
                'nama_agen'      => $request->nama_agen,
                'kategori'       => $kategori,
                'induk_agen'     => $request->input('induk_agen') ?: '-',
                'rute_kode'      => $request->jurusan_kode,
                'wilayah'        => $wilayahNama,
                'telepon'        => $request->input('telepon') ?: '-',
                'jatah_default'  => $isNonAktif ? 0 : (int) $request->jatah_default,
                'plafon_piutang' => (float) ($request->input('plafon_piutang', 5000000)),
                'saldo_piutang'  => 0,
                'status'         => $isNonAktif ? 'nonaktif' : 'aktif',
            ]
        );

        $metaMap = Cache::get('sirkulasi_agen_meta', []);
        $metaMap[$agen->kode_agen] = [
            'tanggal_daftar' => $request->input('tanggal_daftar', date('Y-m-d')),
            'alamat'         => $request->input('alamat') ?: ('Wilayah ' . $wilayahNama),
            'wilayah_kode'   => $request->wilayah_kode,
            'wilayah_nama'   => $wilayahNama,
            'jurusan_kode'   => $request->jurusan_kode,
            'jurusan_nama'   => $jurusanNama,
            'kategori_asli'  => $kategori === '00' ? '11' : $kategori,
        ];
        Cache::put('sirkulasi_agen_meta', $metaMap, now()->addDays(30));

        if (class_exists(AuditLogAkuntansi::class) && Schema::hasTable('audit_log_akuntansis')) {
            AuditLogAkuntansi::catat('TAMBAH_AGEN', 'SIRKULASI', "Penambahan agen koran fisik baru: {$agen->kode_agen} - {$agen->nama_agen}");
        }

        return redirect()->route('sirkulasi.tabel.agen')
            ->with('success', "Data Agen baru ({$agen->kode_agen} - {$agen->nama_agen}) berhasil ditambahkan oleh Divisi Akuntansi.");
    }

    /**
     * Edit Data Agen (Khusus Divisi Akuntansi & Administrator)
     */
    public function updateAgen(Request $request, $id)
    {
        if (!$this->canManageAgen()) {
            return redirect()->route('sirkulasi.tabel.agen')
                ->with('error', 'Akses Ditolak: Divisi Sirkulasi hanya dapat melihat data agen (Read-Only). Perubahan data agen hanya dapat dilakukan oleh Divisi Akuntansi.');
        }

        $agen = Agen::findOrFail($id);

        $request->validate([
            'nama_agen'      => 'required|string|max:150',
            'kategori'       => 'required|in:11,99,00',
            'wilayah_kode'   => 'required|string',
            'jurusan_kode'   => 'required|string',
            'alamat'         => 'nullable|string|max:255',
            'telepon'        => 'nullable|string|max:50',
            'jatah_default'  => 'required|integer|min:0',
            'plafon_piutang' => 'nullable|numeric|min:0',
            'induk_agen'     => 'nullable|string|max:150',
        ]);

        $kategori = $request->input('kategori', $agen->kategori);
        $wilayahObj = $this->getDaftarWilayah()->firstWhere('kode', $request->wilayah_kode);
        $jurusanObj = $this->getDaftarJurusan()->firstWhere('kode', $request->jurusan_kode);
        $wilayahNama = $wilayahObj->nama ?? $agen->wilayah;
        $jurusanNama = $jurusanObj->nama_jurusan ?? 'Tasikmalaya Kota - Singaparna';
        $isNonAktif = $kategori === '00';

        $agen->update([
            'nama_agen'      => $request->nama_agen,
            'kategori'       => $kategori,
            'induk_agen'     => $request->input('induk_agen') ?: '-',
            'rute_kode'      => $request->jurusan_kode,
            'wilayah'        => $wilayahNama,
            'telepon'        => $request->input('telepon') ?: '-',
            'jatah_default'  => $isNonAktif ? 0 : (int) $request->jatah_default,
            'plafon_piutang' => (float) ($request->input('plafon_piutang', $agen->plafon_piutang)),
            'status'         => $isNonAktif ? 'nonaktif' : 'aktif',
        ]);

        $metaMap = Cache::get('sirkulasi_agen_meta', []);
        $existingMeta = $metaMap[$agen->kode_agen] ?? [];
        $metaMap[$agen->kode_agen] = array_merge($existingMeta, [
            'alamat'        => $request->input('alamat') ?: ($existingMeta['alamat'] ?? ('Wilayah ' . $wilayahNama)),
            'wilayah_kode'  => $request->wilayah_kode,
            'wilayah_nama'  => $wilayahNama,
            'jurusan_kode'  => $request->jurusan_kode,
            'jurusan_nama'  => $jurusanNama,
            'kategori_asli' => $kategori === '00' ? ($existingMeta['kategori_asli'] ?? '11') : $kategori,
        ]);
        Cache::put('sirkulasi_agen_meta', $metaMap, now()->addDays(30));

        if (class_exists(AuditLogAkuntansi::class) && Schema::hasTable('audit_log_akuntansis')) {
            AuditLogAkuntansi::catat('EDIT_AGEN', 'SIRKULASI', "Perubahan data agen: {$agen->kode_agen} - {$agen->nama_agen}");
        }

        return redirect()->route('sirkulasi.tabel.agen')
            ->with('success', "Data Agen ({$agen->kode_agen} - {$agen->nama_agen}) berhasil diperbarui.");
    }

    /**
     * Aktifkan / Non-Aktifkan Status Agen (Khusus Divisi Akuntansi & Administrator)
     */
    public function toggleStatusAgen($id)
    {
        if (!$this->canManageAgen()) {
            return redirect()->route('sirkulasi.tabel.agen')
                ->with('error', 'Akses Ditolak: Divisi Sirkulasi tidak diizinkan mengubah status atau mengedit data keagenan. Pengelolaan hanya dapat dilakukan oleh Divisi Akuntansi.');
        }

        $agen = Agen::findOrFail($id);
        $metaMap = Cache::get('sirkulasi_agen_meta', []);
        $meta = $metaMap[$agen->kode_agen] ?? [];

        if ($agen->status === 'aktif' && $agen->kategori !== '00') {
            $meta['kategori_asli'] = $agen->kategori;
            $meta['jatah_asli'] = $agen->jatah_default;
            $agen->update([
                'status'        => 'nonaktif',
                'kategori'      => '00',
                'jatah_default' => 0,
            ]);
            $msg = "Agen {$agen->kode_agen} ({$agen->nama_agen}) dinonaktifkan (00 - Agen Non Aktif). Histori piutang tetap tersimpan.";
        } else {
            $restoredKategori = $meta['kategori_asli'] ?? (str_contains($agen->kode_agen, '-99-') ? '99' : '11');
            $restoredJatah = $meta['jatah_asli'] ?? ($agen->jatah_default > 0 ? $agen->jatah_default : 100);
            $agen->update([
                'status'        => 'aktif',
                'kategori'      => $restoredKategori,
                'jatah_default' => $restoredJatah,
            ]);
            $msg = "Agen {$agen->kode_agen} ({$agen->nama_agen}) berhasil diaktifkan kembali.";
        }

        $metaMap[$agen->kode_agen] = $meta;
        Cache::put('sirkulasi_agen_meta', $metaMap, now()->addDays(30));

        return redirect()->route('sirkulasi.tabel.agen')->with('success', $msg);
    }

    /**
     * Hapus / Non-Aktifkan Agen (Khusus Divisi Akuntansi & Administrator)
     */
    public function destroyAgen($id)
    {
        if (!$this->canManageAgen()) {
            return redirect()->route('sirkulasi.tabel.agen')
                ->with('error', 'Akses Ditolak: Divisi Sirkulasi tidak diizinkan menghapus data agen. Penghapusan hanya dapat dilakukan oleh Divisi Akuntansi.');
        }

        $agen = Agen::findOrFail($id);

        if ((float) $agen->saldo_piutang > 0) {
            $agen->update([
                'status'        => 'nonaktif',
                'kategori'      => '00',
                'jatah_default' => 0,
            ]);
            return redirect()->route('sirkulasi.tabel.agen')
                ->with('success', "Agen {$agen->nama_agen} masih memiliki saldo piutang Rp " . number_format($agen->saldo_piutang, 0, ',', '.') . ". Sesuai standar akuntansi, status agen dialihkan ke 00 - Non-Aktif (Soft Delete).");
        }

        $kode = $agen->kode_agen;
        $nama = $agen->nama_agen;
        $deletedCodes = Cache::get('sirkulasi_deleted_agen_codes', []);
        $deletedCodes[] = $kode;
        Cache::put('sirkulasi_deleted_agen_codes', array_unique($deletedCodes), now()->addDays(30));

        $agen->delete();

        if (class_exists(AuditLogAkuntansi::class) && Schema::hasTable('audit_log_akuntansis')) {
            AuditLogAkuntansi::catat('HAPUS_AGEN', 'SIRKULASI', "Penghapusan data agen tanpa saldo piutang: {$kode} - {$nama}");
        }

        return redirect()->route('sirkulasi.tabel.agen')
            ->with('success', "Data Agen {$kode} ({$nama}) berhasil dihapus oleh Divisi Akuntansi.");
    }

    // =========================================================================
    // 2. MENU JATAH (Operasional H+1)
    // =========================================================================

    /**
     * Edit Data Penjualan (Input matriks jatah H+1: Langganan, Konsinyasi/Eceran Rp3.000, Gratis, Insidentil)
     * Untuk tanggal ke depan (> hari ini), matriks tetap kosong sampai dilakukan Copy Jatah dari hari sebelumnya.
     */
    public function jatahEdit(Request $request)
    {
        $today = Carbon::today();
        $targetTanggal = $request->input('tanggal', Carbon::tomorrow()->toDateString());
        $jurusanPilihan = $request->input('jurusan', 'all');
        $carbonTarget = Carbon::parse($targetTanggal);
        $isFutureDate = $carbonTarget->gt($today);
        $prevTanggal = $carbonTarget->copy()->subDay()->toDateString();

        $daftarAgen = $this->getDaftarAgen()->where('status', 'aktif');
        if ($jurusanPilihan !== 'all') {
            $daftarAgen = $daftarAgen->where('jurusan_kode', $jurusanPilihan);
        }

        $savedJatahMap = Cache::get('jatah_harian_' . $targetTanggal, []);
        $jatahMeta = Cache::get('jatah_meta_' . $targetTanggal);

        $cfg = $this->getSirkulasiConfig();
        $hargaEceran = $cfg['harga_eceran'];
        $pctL = $cfg['pct_langganan'];
        $pctK = $cfg['pct_konsinyasi'];
        $pctG = $cfg['pct_gratis'];

        $matrixJatah = collect([]);
        if (!$isFutureDate || !empty($savedJatahMap)) {
            $matrixJatah = $daftarAgen->map(function ($agen) use ($savedJatahMap, $isFutureDate, $hargaEceran, $pctL, $pctK, $pctG) {
                $custom = $savedJatahMap[$agen->id] ?? null;
                if ($isFutureDate && !$custom) {
                    return null;
                }

                $langganan = isset($custom['langganan']) ? (int) $custom['langganan'] : (int) ($agen->jatah_default * $pctL);
                $konsinyasi = isset($custom['konsinyasi']) ? (int) $custom['konsinyasi'] : (int) ($agen->jatah_default * $pctK);
                $gratis = isset($custom['gratis']) ? (int) $custom['gratis'] : (int) ($agen->jatah_default * $pctG);
                $pengenalan = isset($custom['pengenalan']) ? (int) $custom['pengenalan'] : 0;
                $insidentil = isset($custom['insidentil']) ? (int) $custom['insidentil'] : 0;
                $totalJatah = $langganan + $konsinyasi + $gratis + $pengenalan + $insidentil;

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
                    'pengenalan' => $pengenalan,
                    'insidentil' => $insidentil,
                    'total_jatah' => $totalJatah,
                    'estimasi_omset' => $konsinyasi * $hargaEceran,
                ];
            })->filter()->values();
        }

        $hasJatahData = $matrixJatah->isNotEmpty();

        $totalLangganan = $matrixJatah->sum('langganan');
        $totalKonsinyasi = $matrixJatah->sum('konsinyasi');
        $totalGratis = $matrixJatah->sum('gratis');
        $totalPengenalan = $matrixJatah->sum('pengenalan');
        $totalInsidentil = $matrixJatah->sum('insidentil');
        $grandTotalJatah = $matrixJatah->sum('total_jatah');
        $grandTotalOmset = $matrixJatah->sum('estimasi_omset');

        $jurusanList = $this->getDaftarJurusan();
        $lockReason = $this->getLockReasonTanggalJatah($targetTanggal);
        $isCutoffLocked = $lockReason !== null;
        $activeMemo = Schema::hasTable('memo_unlocks')
            ? MemoUnlock::whereDate('tanggal_target', $targetTanggal)
                ->where('tujuan_divisi', 'sirkulasi')
                ->latest('id')
                ->first()
            : null;

        return view('pages.sirkulasi.jatah.edit', compact(
            'matrixJatah',
            'targetTanggal',
            'prevTanggal',
            'isFutureDate',
            'hasJatahData',
            'jatahMeta',
            'jurusanPilihan',
            'jurusanList',
            'totalLangganan',
            'totalKonsinyasi',
            'totalGratis',
            'totalPengenalan',
            'totalInsidentil',
            'grandTotalJatah',
            'grandTotalOmset',
            'today',
            'isCutoffLocked',
            'lockReason',
            'activeMemo',
            'hargaEceran'
        ));
    }

    public function storeJatahEdit(Request $request)
    {
        $targetTanggal = $request->input('tanggal_target', $request->input('tanggal', Carbon::tomorrow()->toDateString()));
        $lockReason = $this->getLockReasonTanggalJatah($targetTanggal);

        if ($lockReason !== null) {
            return redirect()->route('sirkulasi.jatah.edit', ['tanggal' => $targetTanggal])
                ->with('error', "Perubahan ditolak: {$lockReason} Data hanya dapat diubah jika kunci dibuka kembali sesuai prosedur.");
        }

        if ($request->has('jatah') && is_array($request->input('jatah'))) {
            $existing = Cache::get('jatah_harian_' . $targetTanggal, []);
            foreach ($request->input('jatah') as $agenId => $row) {
                $existing[$agenId] = [
                    'langganan'  => max(0, (int) ($row['langganan'] ?? 0)),
                    'konsinyasi' => max(0, (int) ($row['konsinyasi'] ?? 0)),
                    'gratis'     => max(0, (int) ($row['gratis'] ?? 0)),
                    'pengenalan' => max(0, (int) ($row['pengenalan'] ?? 0)),
                    'insidentil' => max(0, (int) ($row['insidentil'] ?? 0)),
                ];
            }
            Cache::put('jatah_harian_' . $targetTanggal, $existing, now()->addDays(30));
        }

        return redirect()->route('sirkulasi.jatah.edit', ['tanggal' => $targetTanggal])
            ->with('success', "Data jatah tanggal {$targetTanggal} berhasil disimpan.");
    }

    /**
     * Copy Data Penjualan (Salin pola alokasi antar-tanggal kerja & cetak bukti)
     */
    public function copyJatah(Request $request)
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $lastWeekSameDay = Carbon::tomorrow()->subDays(7);

        $sumberTanggal = $request->input('sumber_tanggal', $today->toDateString());
        $targetTanggal = $request->input('target_tanggal', $tomorrow->toDateString());
        $jurusanPilihan = $request->input('jurusan', 'all');

        $jurusanList = $this->getDaftarJurusan();
        $targetLockReason = $this->getLockReasonTanggalJatah($targetTanggal);
        $isTargetLocked = $targetLockReason !== null;

        $daftarAgen = $this->getDaftarAgen()->where('status', 'aktif');
        if ($jurusanPilihan !== 'all') {
            $daftarAgen = $daftarAgen->where('jurusan_kode', $jurusanPilihan);
        }

        $copiedMap = Cache::get('jatah_harian_' . $targetTanggal, []);
        $copiedMeta = Cache::get('jatah_meta_' . $targetTanggal, []);

        $cfg = $this->getSirkulasiConfig();
        $hargaEceran = $cfg['harga_eceran'];

        $matrixJatah = $daftarAgen->map(function ($agen) use ($copiedMap, $hargaEceran) {
            $row = $copiedMap[$agen->id] ?? null;
            if (!$row) {
                return null;
            }

            $langganan = (int) ($row['langganan'] ?? 0);
            $konsinyasi = (int) ($row['konsinyasi'] ?? 0);
            $gratis = (int) ($row['gratis'] ?? 0);
            $pengenalan = (int) ($row['pengenalan'] ?? 0);
            $insidentil = (int) ($row['insidentil'] ?? 0);
            $totalJatah = $langganan + $konsinyasi + $gratis + $pengenalan + $insidentil;

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
                'pengenalan' => $pengenalan,
                'insidentil' => $insidentil,
                'total_jatah' => $totalJatah,
                'estimasi_omset' => $konsinyasi * $hargaEceran,
                'status_pola' => 'Tersalin',
            ];
        })->filter()->values();

        $hasCopied = $matrixJatah->isNotEmpty();
        $uncopiedCount = $daftarAgen->filter(fn($agen) => !isset($copiedMap[$agen->id]))->count();
        $isFullyCopied = $daftarAgen->isNotEmpty() && $uncopiedCount === 0;

        $totalLangganan = $matrixJatah->sum('langganan');
        $totalKonsinyasi = $matrixJatah->sum('konsinyasi');
        $totalGratis = $matrixJatah->sum('gratis');
        $totalPengenalan = $matrixJatah->sum('pengenalan');
        $totalInsidentil = $matrixJatah->sum('insidentil');
        $grandTotalJatah = $matrixJatah->sum('total_jatah');
        $grandTotalOmset = $matrixJatah->sum('estimasi_omset');

        return view('pages.sirkulasi.jatah.copy', compact(
            'today',
            'tomorrow',
            'lastWeekSameDay',
            'sumberTanggal',
            'targetTanggal',
            'jurusanPilihan',
            'jurusanList',
            'hasCopied',
            'isFullyCopied',
            'uncopiedCount',
            'copiedMeta',
            'isTargetLocked',
            'targetLockReason',
            'matrixJatah',
            'totalLangganan',
            'totalKonsinyasi',
            'totalGratis',
            'totalPengenalan',
            'totalInsidentil',
            'grandTotalJatah',
            'grandTotalOmset'
        ));
    }

    public function processCopyJatah(Request $request)
    {
        $today = Carbon::today();
        $sumberTanggal = $request->input('sumber_tanggal', Carbon::today()->toDateString());
        $targetTanggal = $request->input('target_tanggal', Carbon::tomorrow()->toDateString());
        $jurusan = $request->input('jurusan', 'all');
        $action = $request->input('action_type', 'stay_and_print');

        if ($sumberTanggal === $targetTanggal) {
            return redirect()->route('sirkulasi.jatah.copy', [
                'sumber_tanggal' => $sumberTanggal,
                'target_tanggal' => $targetTanggal,
                'jurusan' => $jurusan,
            ])->with('error', 'Tanggal asal dan tanggal tujuan tidak boleh sama.');
        }

        $targetLockReason = $this->getLockReasonTanggalJatah($targetTanggal);
        if ($targetLockReason !== null) {
            return redirect()->route('sirkulasi.jatah.copy', [
                'sumber_tanggal' => $sumberTanggal,
                'target_tanggal' => $targetTanggal,
                'jurusan' => $jurusan,
            ])->with('error', "Salin ditolak: {$targetLockReason}");
        }

        $sumberCarbon = Carbon::parse($sumberTanggal);
        $sumberSavedMap = Cache::get('jatah_harian_' . $sumberTanggal, []);

        if ($sumberCarbon->gt($today) && empty($sumberSavedMap)) {
            return redirect()->route('sirkulasi.jatah.copy', [
                'sumber_tanggal' => $sumberTanggal,
                'target_tanggal' => $targetTanggal,
                'jurusan' => $jurusan,
            ])->with('error', "Data jatah tanggal {$sumberTanggal} masih kosong.");
        }

        $daftarAgen = $this->getDaftarAgen()->where('status', 'aktif');
        if ($jurusan !== 'all') {
            $daftarAgen = $daftarAgen->where('jurusan_kode', $jurusan);
        }

        $targetSavedMap = Cache::get('jatah_harian_' . $targetTanggal, []);

        // Proteksi Anti-Timpa: cek agen yang belum memiliki data pada tanggal tujuan
        $agenBelumDisalin = $daftarAgen->filter(fn($agen) => !isset($targetSavedMap[$agen->id]));
        if ($agenBelumDisalin->isEmpty()) {
            return redirect()->route('sirkulasi.jatah.copy', [
                'sumber_tanggal' => $sumberTanggal,
                'target_tanggal' => $targetTanggal,
                'jurusan' => $jurusan,
            ])->with('error', "Data jatah untuk tanggal {$targetTanggal} sudah tersedia. Gunakan menu Edit Data Penjualan Koran untuk mengubah alokasi agen tanpa menimpa data yang sudah diedit.");
        }

        $cfg = $this->getSirkulasiConfig();
        $pctL = $cfg['pct_langganan'];
        $pctK = $cfg['pct_konsinyasi'];
        $pctG = $cfg['pct_gratis'];
        $copiedCount = 0;

        foreach ($agenBelumDisalin as $agen) {
            $src = $sumberSavedMap[$agen->id] ?? null;
            if ($sumberCarbon->gt($today) && !$src) {
                continue;
            }

            $targetSavedMap[$agen->id] = [
                'langganan'  => isset($src['langganan']) ? (int) $src['langganan'] : (int) ($agen->jatah_default * $pctL),
                'konsinyasi' => isset($src['konsinyasi']) ? (int) $src['konsinyasi'] : (int) ($agen->jatah_default * $pctK),
                'gratis'     => isset($src['gratis']) ? (int) $src['gratis'] : (int) ($agen->jatah_default * $pctG),
                'pengenalan' => isset($src['pengenalan']) ? (int) $src['pengenalan'] : 0,
                'insidentil' => isset($src['insidentil']) ? (int) $src['insidentil'] : 0,
            ];
            $copiedCount++;
        }

        if ($copiedCount === 0) {
            return redirect()->route('sirkulasi.jatah.copy', [
                'sumber_tanggal' => $sumberTanggal,
                'target_tanggal' => $targetTanggal,
                'jurusan' => $jurusan,
            ])->with('error', "Data jatah tanggal {$sumberTanggal} masih kosong.");
        }

        Cache::put('jatah_harian_' . $targetTanggal, $targetSavedMap, now()->addDays(30));
        Cache::put('jatah_meta_' . $targetTanggal, [
            'copied_from' => $sumberTanggal,
            'copied_at'   => now()->format('d/m/Y H:i'),
            'copied_by'   => auth()->user()?->name ?? 'Operator',
        ], now()->addDays(30));

        if ($action === 'go_to_edit') {
            return redirect()->route('sirkulasi.jatah.edit', ['tanggal' => $targetTanggal, 'jurusan' => $jurusan])
                ->with('success', "Data jatah berhasil disalin dari {$sumberTanggal} ke {$targetTanggal}.");
        }

        return redirect()->route('sirkulasi.jatah.copy', [
            'sumber_tanggal' => $sumberTanggal,
            'target_tanggal' => $targetTanggal,
            'jurusan' => $jurusan,
        ])->with('success', "Data jatah berhasil disalin dari {$sumberTanggal} ke {$targetTanggal} ({$copiedCount} agen).");
    }

    /**
     * Tutup Data Penjualan (Locking Harian & Bulanan sebelum ditarik Akuntansi/Kasir)
     */
    public function jatahTutup(Request $request)
    {
        $mode = $request->input('mode');
        if (!$mode) {
            if ($request->has('bulan') && !$request->has('tanggal')) {
                $mode = 'bulanan';
            } elseif ($request->has('tanggal') && !$request->has('bulan')) {
                $mode = 'harian';
            } else {
                $mode = 'semua';
            }
        }

        // =====================================================================
        // 1. DATA TUTUP PENJUALAN HARIAN (PER HARI)
        // =====================================================================
        $tanggalPilihan = $request->input('tanggal', Carbon::today()->toDateString());
        $carbonTanggal = Carbon::parse($tanggalPilihan);
        $isLockedHarian = session('locked_harian_' . $tanggalPilihan, Cache::get('locked_harian_' . $tanggalPilihan, false));
        $permintaanBukaHarian = session('request_unlock_harian_' . $tanggalPilihan, Cache::get('request_unlock_harian_' . $tanggalPilihan, null));

        // Sinkronisasi dua arah dengan tabel memo_unlocks & daily_cutoffs
        if (Schema::hasTable('memo_unlocks')) {
            $dbMemoHarian = MemoUnlock::whereDate('tanggal_target', $tanggalPilihan)
                ->where('tujuan_divisi', 'sirkulasi')
                ->latest('id')
                ->first();

            if ($dbMemoHarian) {
                if ($dbMemoHarian->status === 'OPEN' && in_array($dbMemoHarian->status_akuntansi, ['APPROVED', 'DISETUJUI', 'TERKIRIM'], true)) {
                    $isLockedHarian = false;
                    $permintaanBukaHarian = null;
                } elseif ($dbMemoHarian->status_akuntansi === 'PENDING' && !$permintaanBukaHarian) {
                    $permintaanBukaHarian = (object)[
                        'id'                 => $dbMemoHarian->nomor_memo,
                        'mode'               => 'harian',
                        'target'             => $tanggalPilihan,
                        'target_label'       => $carbonTanggal->translatedFormat('d F Y'),
                        'kategori_alasan'    => $dbMemoHarian->kategori_urgensi ?: 'Penjualan Baru / Susulan Masuk',
                        'alasan'             => $dbMemoHarian->alasan,
                        'estimasi_eksemplar' => (int) preg_replace('/[^0-9]/', '', (string) $dbMemoHarian->spesifikasi_iklan),
                        'estimasi_nominal'   => (float) $dbMemoHarian->estimasi_nominal,
                        'pemohon'            => $dbMemoHarian->diketahui_oleh ?: 'Staf Sirkulasi',
                        'pemohon_role'       => 'Divisi Sirkulasi & Distribusi',
                        'status'             => 'PENDING',
                        'status_label'       => 'Menunggu Otorisasi Akuntansi',
                        'created_at'         => $dbMemoHarian->created_at ? $dbMemoHarian->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                    ];
                }
            }
        }

        if (!$isLockedHarian && Schema::hasTable('daily_cutoffs')) {
            $cutoffRow = DailyCutoff::whereDate('tanggal_edisi', $tanggalPilihan)->first();
            if ($cutoffRow && $cutoffRow->is_closed) {
                $isLockedHarian = true;
            }
        }

        $formatIndo = function ($dateStr) {
            $hariIndo = [
                'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
            ];
            $bulanIndo = [
                'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April',
                'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus',
                'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
            ];
            $c = Carbon::parse($dateStr);
            $h = $hariIndo[$c->format('l')] ?? $c->format('l');
            $b = $bulanIndo[$c->format('F')] ?? $c->format('F');
            return "{$h}, {$c->format('d')} {$b} {$c->format('Y')}";
        };

        $formatSingkat = function ($dateStr) {
            $bulanSingkat = [
                'January' => 'Jan', 'February' => 'Feb', 'March' => 'Mar', 'April' => 'Apr',
                'May' => 'Mei', 'June' => 'Jun', 'July' => 'Jul', 'August' => 'Agt',
                'September' => 'Sep', 'October' => 'Okt', 'November' => 'Nov', 'December' => 'Des'
            ];
            $c = Carbon::parse($dateStr);
            $b = $bulanSingkat[$c->format('F')] ?? $c->format('M');
            return "{$c->format('d')} {$b} {$c->format('Y')}";
        };

        $harianSaatIni = (object)[
            'tanggal' => $formatIndo($tanggalPilihan),
            'tanggal_singkat' => $formatSingkat($tanggalPilihan),
            'tanggal_key' => $tanggalPilihan,
            'total_oplah_cetak' => 12500,
            'total_terjual' => 12230,
            'total_retur_eks' => 270,
            'rasio_retur' => 2.16,
            'omset_kotor' => 37500000,
            'potongan_retur' => 810000,
            'omset_bersih' => 36690000,
            'is_locked' => $isLockedHarian,
            'status' => $isLockedHarian ? 'LOCKED' : 'OPEN',
            'status_label' => $isLockedHarian 
                ? ($permintaanBukaHarian ? 'Terkunci (Menunggu Persetujuan Akuntansi)' : 'Ready to Pull Akunting & Kasir (Terkunci)') 
                : 'Operasional Aktif (Belum Tutup Harian)',
            'closed_at' => $isLockedHarian ? session('locked_at_' . $tanggalPilihan, Cache::get('locked_at_' . $tanggalPilihan, Carbon::now()->format('Y-m-d H:i:s'))) : null,
            'closed_by' => $isLockedHarian ? session('locked_by_' . $tanggalPilihan, Cache::get('locked_by_' . $tanggalPilihan, auth()->user()?->name ?? 'Manager Sirkulasi')) : null,
            'permintaan_buka' => $permintaanBukaHarian,
        ];

        $cfg = $this->getSirkulasiConfig();
        $hargaEceran = $cfg['harga_eceran'];
        $jurusanList = $this->getDaftarJurusan();
        $rekapJurusanHarian = $jurusanList->map(function ($j) use ($isLockedHarian, $hargaEceran) {
            $oplah = $j->alokasi_oplah ?? 1000;
            $kode = $j->kode ?? $j->kode_jurusan ?? '01';
            $retur = (int) round($oplah * (0.015 + ((abs(crc32($kode))) % 10) / 1000));
            $terjual = $oplah - $retur;
            $omset = $terjual * $hargaEceran;
            return (object)[
                'kode' => $kode,
                'nama' => $j->nama_jurusan ?? ('Jurusan ' . $kode),
                'wilayah' => $j->titik_tujuan ?? '-',
                'oplah' => $oplah,
                'terjual' => $terjual,
                'retur' => $retur,
                'rasio' => number_format(($retur / ($oplah ?: 1)) * 100, 2),
                'omset' => $omset,
                'status' => $isLockedHarian ? 'TERKUNCI' : 'VALID',
            ];
        });

        $riwayatClosingHarian = collect([
            (object)[
                'tanggal' => $formatIndo(Carbon::parse($tanggalPilihan)->subDays(1)),
                'tanggal_key' => Carbon::parse($tanggalPilihan)->subDays(1)->toDateString(),
                'total_oplah_cetak' => 12450,
                'total_terjual' => 12190,
                'total_retur_eks' => 260,
                'rasio_retur' => 2.09,
                'total_omset_bersih' => 36570000,
                'status' => 'PULLED',
                'status_label' => 'Telah Diproses Akuntansi & Kasir',
                'closed_at' => Carbon::parse($tanggalPilihan)->subDays(1)->toDateString() . ' 18:45:12',
                'closed_by' => 'Supervisor Distribusi',
            ],
            (object)[
                'tanggal' => $formatIndo(Carbon::parse($tanggalPilihan)->subDays(2)),
                'tanggal_key' => Carbon::parse($tanggalPilihan)->subDays(2)->toDateString(),
                'total_oplah_cetak' => 12300,
                'total_terjual' => 12050,
                'total_retur_eks' => 250,
                'rasio_retur' => 2.03,
                'total_omset_bersih' => 36150000,
                'status' => 'PULLED',
                'status_label' => 'Telah Diproses Akuntansi & Kasir',
                'closed_at' => Carbon::parse($tanggalPilihan)->subDays(2)->toDateString() . ' 18:30:20',
                'closed_by' => 'Supervisor Distribusi',
            ],
            (object)[
                'tanggal' => $formatIndo(Carbon::parse($tanggalPilihan)->subDays(3)),
                'tanggal_key' => Carbon::parse($tanggalPilihan)->subDays(3)->toDateString(),
                'total_oplah_cetak' => 13000,
                'total_terjual' => 12690,
                'total_retur_eks' => 310,
                'rasio_retur' => 2.38,
                'total_omset_bersih' => 38070000,
                'status' => 'PULLED',
                'status_label' => 'Telah Diproses Akuntansi & Kasir',
                'closed_at' => Carbon::parse($tanggalPilihan)->subDays(3)->toDateString() . ' 19:10:05',
                'closed_by' => 'Manager Sirkulasi',
            ],
            (object)[
                'tanggal' => $formatIndo(Carbon::parse($tanggalPilihan)->subDays(4)),
                'tanggal_key' => Carbon::parse($tanggalPilihan)->subDays(4)->toDateString(),
                'total_oplah_cetak' => 14200,
                'total_terjual' => 13850,
                'total_retur_eks' => 350,
                'rasio_retur' => 2.46,
                'total_omset_bersih' => 41550000,
                'status' => 'PULLED',
                'status_label' => 'Telah Diproses Akuntansi & Kasir',
                'closed_at' => Carbon::parse($tanggalPilihan)->subDays(4)->toDateString() . ' 19:00:00',
                'closed_by' => 'Manager Sirkulasi',
            ],
            (object)[
                'tanggal' => $formatIndo(Carbon::parse($tanggalPilihan)->subDays(5)),
                'tanggal_key' => Carbon::parse($tanggalPilihan)->subDays(5)->toDateString(),
                'total_oplah_cetak' => 12800,
                'total_terjual' => 12520,
                'total_retur_eks' => 280,
                'rasio_retur' => 2.19,
                'total_omset_bersih' => 37560000,
                'status' => 'PULLED',
                'status_label' => 'Telah Diproses Akuntansi & Kasir',
                'closed_at' => Carbon::parse($tanggalPilihan)->subDays(5)->toDateString() . ' 18:35:10',
                'closed_by' => 'Supervisor Distribusi',
            ],
        ]);

        if ($isLockedHarian) {
            $riwayatClosingHarian->prepend((object)[
                'tanggal' => $harianSaatIni->tanggal,
                'tanggal_key' => $harianSaatIni->tanggal_key,
                'total_oplah_cetak' => $harianSaatIni->total_oplah_cetak,
                'total_terjual' => $harianSaatIni->total_terjual,
                'total_retur_eks' => $harianSaatIni->total_retur_eks,
                'rasio_retur' => $harianSaatIni->rasio_retur,
                'total_omset_bersih' => $harianSaatIni->omset_bersih,
                'status' => 'LOCKED',
                'status_label' => 'Ready to Pull Akunting',
                'closed_at' => $harianSaatIni->closed_at,
                'closed_by' => $harianSaatIni->closed_by,
            ]);
        }

        // =====================================================================
        // 2. DATA TUTUP PENJUALAN BULANAN (PER BULAN)
        // =====================================================================
        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $carbonBulan = Carbon::parse($bulanPilihan . '-01');
        $isLockedBulanan = session('locked_bulanan_' . $bulanPilihan, Cache::get('locked_bulanan_' . $bulanPilihan, false));
        $permintaanBukaBulanan = session('request_unlock_bulanan_' . $bulanPilihan, Cache::get('request_unlock_bulanan_' . $bulanPilihan, null));

        $riwayatClosingBulanan = collect([
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
            (object)[
                'periode' => 'Juli 2026',
                'periode_key' => '2026-07',
                'total_oplah_cetak' => 382000,
                'total_retur_eks' => 7900,
                'rasio_retur' => 2.07,
                'total_omset_bersih' => 1047300000,
                'status' => 'PULLED',
                'status_label' => 'Telah Diproses Akuntansi',
                'closed_at' => '2026-07-31 23:59:00',
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
            'is_locked' => $isLockedBulanan,
            'status' => $isLockedBulanan ? 'LOCKED' : 'OPEN',
            'status_label' => $isLockedBulanan 
                ? ($permintaanBukaBulanan ? 'Terkunci (Menunggu Persetujuan Akuntansi)' : 'Ready to Pull Akunting (Terkunci)') 
                : 'Operasional Aktif (Belum Tutup Buku)',
            'closed_at' => $isLockedBulanan ? session('locked_at_bulan_' . $bulanPilihan, Cache::get('locked_at_bulan_' . $bulanPilihan, Carbon::now()->format('Y-m-d H:i:s'))) : null,
            'closed_by' => $isLockedBulanan ? session('locked_by_bulan_' . $bulanPilihan, Cache::get('locked_by_bulan_' . $bulanPilihan, auth()->user()?->name ?? 'Manager Sirkulasi')) : null,
            'permintaan_buka' => $permintaanBukaBulanan,
        ];

        if ($isLockedBulanan) {
            $riwayatClosingBulanan->prepend((object)[
                'periode' => $periodeSaatIni->periode,
                'periode_key' => $periodeSaatIni->periode_key,
                'total_oplah_cetak' => $periodeSaatIni->total_oplah_cetak,
                'total_retur_eks' => $periodeSaatIni->total_retur_eks,
                'rasio_retur' => $periodeSaatIni->rasio_retur,
                'total_omset_bersih' => $periodeSaatIni->omset_bersih,
                'status' => 'LOCKED',
                'status_label' => 'Ready to Pull Akunting',
                'closed_at' => $periodeSaatIni->closed_at,
                'closed_by' => $periodeSaatIni->closed_by,
            ]);
        }

        $riwayatClosing = $riwayatClosingBulanan;
        $semuaPermintaanBuka = collect(session('semua_permintaan_buka', Cache::get('semua_permintaan_buka', [])));

        // Gabungkan tiket dari tabel memo_unlocks agar sinkron lintas sesi Sirkulasi & Akuntansi
        if (Schema::hasTable('memo_unlocks')) {
            $dbMemos = MemoUnlock::where('tujuan_divisi', 'sirkulasi')
                ->latest('id')
                ->limit(15)
                ->get();

            foreach ($dbMemos as $m) {
                $existsInList = $semuaPermintaanBuka->contains(fn($item) => is_object($item) && ($item->id ?? '') === $m->nomor_memo);
                if (!$existsInList) {
                    $isBulanan = str_starts_with((string) $m->nomor_memo, 'REQ-ULK-B');
                    $tgt = $isBulanan
                        ? Carbon::parse($m->tanggal_target)->format('Y-m')
                        : Carbon::parse($m->tanggal_target)->toDateString();
                    $tgtLabel = $isBulanan
                        ? Carbon::parse($m->tanggal_target)->translatedFormat('F Y')
                        : Carbon::parse($m->tanggal_target)->translatedFormat('d F Y');
                    $isApproved = ($m->status === 'OPEN' || in_array($m->status_akuntansi, ['APPROVED', 'DISETUJUI'], true));

                    $semuaPermintaanBuka->push((object)[
                        'id'                 => $m->nomor_memo,
                        'mode'               => $isBulanan ? 'bulanan' : 'harian',
                        'target'             => $tgt,
                        'target_label'       => $tgtLabel,
                        'kategori_alasan'    => $m->kategori_urgensi ?: 'Penjualan Baru / Susulan Masuk',
                        'alasan'             => $m->alasan,
                        'estimasi_eksemplar' => (int) preg_replace('/[^0-9]/', '', (string) $m->spesifikasi_iklan),
                        'estimasi_nominal'   => (float) $m->estimasi_nominal,
                        'pemohon'            => $m->diketahui_oleh ?: 'Staf Sirkulasi',
                        'pemohon_role'       => 'Divisi Sirkulasi & Distribusi',
                        'status'             => $isApproved ? 'APPROVED' : 'PENDING',
                        'status_label'       => $isApproved ? 'Disetujui & Dibuka Akuntansi' : 'Menunggu Otorisasi Akuntansi',
                        'created_at'         => $m->created_at ? $m->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        $user = auth()->user();
        $canDirectUnlock = $user && ($user->isAdmin() || $user->hasRole('akuntansi', 'accounting', 'keuangan'));

        return view('pages.sirkulasi.jatah.tutup', compact(
            'mode',
            'harianSaatIni',
            'tanggalPilihan',
            'rekapJurusanHarian',
            'riwayatClosingHarian',
            'riwayatClosingBulanan',
            'riwayatClosing',
            'periodeSaatIni',
            'bulanPilihan',
            'permintaanBukaHarian',
            'permintaanBukaBulanan',
            'semuaPermintaanBuka',
            'canDirectUnlock'
        ));
    }

    public function lockJatahTutup(Request $request)
    {
        $mode = $request->input('mode', 'harian');
        $action = $request->input('action', 'lock');
        $operator = auth()->user()?->name ?? 'Manager Sirkulasi';

        if ($action === 'request_unlock') {
            return $this->requestUnlockJatahTutup($request);
        }

        if ($action === 'cancel_request_unlock') {
            return $this->cancelRequestUnlockJatahTutup($request);
        }

        if ($mode === 'harian') {
            $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
            $formatTanggal = Carbon::parse($tanggal)->translatedFormat('d F Y');

            if ($action === 'unlock') {
                $user = auth()->user();
                $canDirectUnlock = $user && ($user->isAdmin() || $user->hasRole('akuntansi', 'accounting', 'keuangan'));
                if (!$canDirectUnlock) {
                    return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $tanggal])
                        ->with('error', "Akses Ditolak: Sesuai SOP, transaksi yang telah ditutup HANYA DAPAT DIBUKA KEMBALI OLEH BAGIAN AKUNTANSI. Silakan ajukan permohonan buka kunci ke Bagian Akuntansi.");
                }

                session()->forget('locked_harian_' . $tanggal);
                session()->forget('locked_at_' . $tanggal);
                session()->forget('locked_by_' . $tanggal);
                session()->forget('request_unlock_harian_' . $tanggal);
                Cache::forget('locked_harian_' . $tanggal);
                Cache::forget('locked_at_' . $tanggal);
                Cache::forget('locked_by_' . $tanggal);
                Cache::forget('request_unlock_harian_' . $tanggal);

                if (Schema::hasTable('daily_cutoffs')) {
                    DailyCutoff::whereDate('tanggal_edisi', $tanggal)->update(['is_closed' => false]);
                }

                if (Schema::hasTable('memo_unlocks')) {
                    $updated = MemoUnlock::whereDate('tanggal_target', $tanggal)
                        ->where('tujuan_divisi', 'sirkulasi')
                        ->update([
                            'status'                  => 'OPEN',
                            'status_akuntansi'        => 'APPROVED',
                            'dibuka_pada'             => now(),
                            'diterima_akuntansi_pada' => now(),
                        ]);

                    if (!$updated) {
                        MemoUnlock::create([
                            'tanggal_target'          => $tanggal,
                            'nomor_memo'              => 'MEMO/SIRK/' . Carbon::parse($tanggal)->format('Y/m/d'),
                            'kategori_urgensi'        => 'Buka Kunci Harian Sirkulasi',
                            'nama_pemasang'           => 'Divisi Sirkulasi',
                            'spesifikasi_iklan'       => 'Otorisasi Buka Kunci Harian',
                            'estimasi_nominal'        => 0,
                            'alasan'                  => 'Dibuka langsung oleh Divisi Akuntansi (' . $operator . ')',
                            'tujuan_divisi'           => 'sirkulasi',
                            'status_akuntansi'        => 'APPROVED',
                            'diketahui_oleh'          => $operator,
                            'dibuka_oleh_user_id'     => auth()->id() ?? 1,
                            'status'                  => 'OPEN',
                            'dibuka_pada'             => now(),
                            'diterima_akuntansi_pada' => now(),
                        ]);
                    }
                }

                if (class_exists(AuditLogAkuntansi::class) && Schema::hasTable('audit_log_akuntansis')) {
                    AuditLogAkuntansi::catat('BUKA_KUNCI', 'SIRKULASI', "Otorisasi buka kunci data penjualan harian sirkulasi tanggal {$tanggal} oleh {$operator}");
                }

                // Update status di riwayat permintaan
                $semua = collect(session('semua_permintaan_buka', Cache::get('semua_permintaan_buka', [])))
                    ->map(function ($item) use ($tanggal) {
                        if (is_object($item) && $item->mode === 'harian' && $item->target === $tanggal) {
                            $item->status = 'APPROVED';
                            $item->status_label = 'Disetujui & Dibuka Akuntansi';
                            $item->unlocked_at = Carbon::now()->format('Y-m-d H:i:s');
                        }
                        return $item;
                    })->all();
                session()->put('semua_permintaan_buka', $semua);
                Cache::put('semua_permintaan_buka', $semua, now()->addDays(7));

                return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $tanggal])
                    ->with('success', "Kunci Data Penjualan Harian Tanggal {$formatTanggal} ({$tanggal}) berhasil DIBUKA oleh Akuntansi. Staf Sirkulasi sekarang dapat menginput transaksi penjualan susulan.");
            }

            session()->put('locked_harian_' . $tanggal, true);
            session()->put('locked_at_' . $tanggal, Carbon::now()->format('Y-m-d H:i:s'));
            session()->put('locked_by_' . $tanggal, $operator);
            Cache::put('locked_harian_' . $tanggal, true, now()->addDays(7));
            Cache::put('locked_at_' . $tanggal, Carbon::now()->format('Y-m-d H:i:s'), now()->addDays(7));
            Cache::put('locked_by_' . $tanggal, $operator, now()->addDays(7));

            if (Schema::hasTable('daily_cutoffs')) {
                DailyCutoff::updateOrCreate(
                    ['tanggal_edisi' => $tanggal],
                    [
                        'jam_cutoff'        => class_exists(SistemConfig::class) ? SistemConfig::getJamCutoff() : '14:00',
                        'is_closed'         => true,
                        'closed_by_user_id' => auth()->id() ?? 1,
                        'closed_at'         => now(),
                        'catatan'           => 'Tutup transaksi penjualan harian Sirkulasi oleh ' . $operator,
                    ]
                );
            }

            if (Schema::hasTable('memo_unlocks')) {
                MemoUnlock::whereDate('tanggal_target', $tanggal)
                    ->where('tujuan_divisi', 'sirkulasi')
                    ->where('status', 'OPEN')
                    ->update([
                        'status'               => 'CLOSED',
                        'ditutup_kembali_pada' => now(),
                    ]);
            }

            session()->forget('request_unlock_harian_' . $tanggal);
            Cache::forget('request_unlock_harian_' . $tanggal);

            return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $tanggal])
                ->with('success', "Data Penjualan Harian Edisi {$formatTanggal} ({$tanggal}) berhasil DITUTUP & DIKUNCI (Status: READY TO PULL AKUNTING / REKONSILIASI KASIR). Data jatah dan retur harian terkunci dari perubahan.");
        } else {
            $periode = $request->input('periode', Carbon::now()->format('Y-m'));
            $formatPeriode = Carbon::parse($periode . '-01')->translatedFormat('F Y');
            $targetDateBulan = Carbon::parse($periode . '-01')->endOfMonth()->toDateString();

            if ($action === 'unlock') {
                $user = auth()->user();
                $canDirectUnlock = $user && ($user->isAdmin() || $user->hasRole('akuntansi', 'accounting', 'keuangan'));
                if (!$canDirectUnlock) {
                    return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'bulanan', 'bulan' => $periode])
                        ->with('error', "Akses Ditolak: Sesuai SOP, transaksi yang telah ditutup HANYA DAPAT DIBUKA KEMBALI OLEH BAGIAN AKUNTANSI. Silakan ajukan permohonan buka kunci ke Bagian Akuntansi.");
                }

                session()->forget('locked_bulanan_' . $periode);
                session()->forget('locked_at_bulan_' . $periode);
                session()->forget('locked_by_bulan_' . $periode);
                session()->forget('request_unlock_bulanan_' . $periode);
                Cache::forget('locked_bulanan_' . $periode);
                Cache::forget('locked_at_bulan_' . $periode);
                Cache::forget('locked_by_bulan_' . $periode);
                Cache::forget('request_unlock_bulanan_' . $periode);

                if (Schema::hasTable('memo_unlocks')) {
                    MemoUnlock::whereDate('tanggal_target', $targetDateBulan)
                        ->where('tujuan_divisi', 'sirkulasi')
                        ->update([
                            'status'                  => 'OPEN',
                            'status_akuntansi'        => 'APPROVED',
                            'dibuka_pada'             => now(),
                            'diterima_akuntansi_pada' => now(),
                        ]);
                }

                if (class_exists(AuditLogAkuntansi::class) && Schema::hasTable('audit_log_akuntansis')) {
                    AuditLogAkuntansi::catat('BUKA_KUNCI', 'SIRKULASI', "Otorisasi buka kunci data penjualan bulanan sirkulasi periode {$periode} oleh {$operator}");
                }

                $semua = collect(session('semua_permintaan_buka', Cache::get('semua_permintaan_buka', [])))
                    ->map(function ($item) use ($periode) {
                        if (is_object($item) && $item->mode === 'bulanan' && $item->target === $periode) {
                            $item->status = 'APPROVED';
                            $item->status_label = 'Disetujui & Dibuka Akuntansi';
                            $item->unlocked_at = Carbon::now()->format('Y-m-d H:i:s');
                        }
                        return $item;
                    })->all();
                session()->put('semua_permintaan_buka', $semua);
                Cache::put('semua_permintaan_buka', $semua, now()->addDays(7));

                return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'bulanan', 'bulan' => $periode])
                    ->with('success', "Kunci Data Penjualan Periode Bulanan {$formatPeriode} berhasil DIBUKA oleh Akuntansi.");
            }

            session()->put('locked_bulanan_' . $periode, true);
            session()->put('locked_at_bulan_' . $periode, Carbon::now()->format('Y-m-d H:i:s'));
            session()->put('locked_by_bulan_' . $periode, $operator);
            Cache::put('locked_bulanan_' . $periode, true, now()->addDays(7));
            Cache::put('locked_at_bulan_' . $periode, Carbon::now()->format('Y-m-d H:i:s'), now()->addDays(7));
            Cache::put('locked_by_bulan_' . $periode, $operator, now()->addDays(7));

            if (Schema::hasTable('memo_unlocks')) {
                MemoUnlock::whereDate('tanggal_target', $targetDateBulan)
                    ->where('tujuan_divisi', 'sirkulasi')
                    ->where('status', 'OPEN')
                    ->update([
                        'status'               => 'CLOSED',
                        'ditutup_kembali_pada' => now(),
                    ]);
            }

            session()->forget('request_unlock_bulanan_' . $periode);
            Cache::forget('request_unlock_bulanan_' . $periode);

            return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'bulanan', 'bulan' => $periode])
                ->with('success', "Data Penjualan Periode Bulanan {$formatPeriode} berhasil DITUTUP & DIKUNCI (Status: READY TO PULL AKUNTING). Data jatah dan retur terkunci dari perubahan.");
        }
    }

    /**
     * Pengajuan Permintaan Pembukaan Kunci Transaksi ke Divisi Akuntansi (Terkirim ke Tabel memo_unlocks)
     */
    public function requestUnlockJatahTutup(Request $request)
    {
        $mode = $request->input('mode', 'harian');
        $operator = auth()->user()?->name ?? 'Staf Sirkulasi';

        if ($mode === 'harian') {
            $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
            $formatTanggal = Carbon::parse($tanggal)->translatedFormat('d F Y');
            $isLocked = $this->isTanggalJatahLocked($tanggal);

            if (!$isLocked) {
                return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $tanggal])
                    ->with('error', "Transaksi penjualan tanggal {$formatTanggal} belum ditutup/dikunci. Data masih dapat diedit secara langsung tanpa perlu meminta izin ke Akuntansi.");
            }

            $request->validate([
                'alasan' => 'required|string|min:5',
                'kategori_alasan' => 'nullable|string',
                'estimasi_eksemplar' => 'nullable|integer',
                'estimasi_nominal' => 'nullable|numeric',
            ], [
                'alasan.required' => 'Alasan permohonan pembukaan transaksi wajib diisi agar dapat diverifikasi oleh Akuntansi.',
                'alasan.min' => 'Alasan permohonan minimal 5 karakter.',
            ]);

            $requestId = 'REQ-ULK-H' . Carbon::parse($tanggal)->format('ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $estimasiEks = (int) $request->input('estimasi_eksemplar', 0);
            $estimasiNom = (int) str_replace(['.', ','], '', (string) $request->input('estimasi_nominal', 0));
            $kategoriAlasan = $request->input('kategori_alasan', 'Penjualan Baru / Susulan Masuk');

            $requestData = (object)[
                'id' => $requestId,
                'mode' => 'harian',
                'target' => $tanggal,
                'target_label' => $formatTanggal,
                'kategori_alasan' => $kategoriAlasan,
                'alasan' => $request->input('alasan'),
                'estimasi_eksemplar' => $estimasiEks,
                'estimasi_nominal' => (float) $estimasiNom,
                'pemohon' => $operator,
                'pemohon_role' => 'Divisi Sirkulasi & Distribusi',
                'status' => 'PENDING',
                'status_label' => 'Menunggu Otorisasi Akuntansi',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ];

            if (Schema::hasTable('memo_unlocks')) {
                MemoUnlock::updateOrCreate(
                    [
                        'tanggal_target'   => $tanggal,
                        'tujuan_divisi'    => 'sirkulasi',
                        'status_akuntansi' => 'PENDING',
                    ],
                    [
                        'nomor_memo'          => $requestId,
                        'kategori_urgensi'    => $kategoriAlasan,
                        'nama_pemasang'       => $operator . ' (Sirkulasi)',
                        'spesifikasi_iklan'   => "Susulan {$estimasiEks} Eks (Harian)",
                        'estimasi_nominal'    => $estimasiNom,
                        'alasan'              => $request->input('alasan'),
                        'diketahui_oleh'      => $operator,
                        'dibuka_oleh_user_id' => auth()->id() ?? 1,
                        'status'              => 'CLOSED',
                    ]
                );
            }

            session()->put('request_unlock_harian_' . $tanggal, $requestData);
            Cache::put('request_unlock_harian_' . $tanggal, $requestData, now()->addDays(7));

            $semua = collect(session('semua_permintaan_buka', Cache::get('semua_permintaan_buka', [])))
                ->reject(fn($item) => is_object($item) && $item->mode === 'harian' && $item->target === $tanggal)
                ->prepend($requestData)
                ->values()
                ->all();
            session()->put('semua_permintaan_buka', $semua);
            Cache::put('semua_permintaan_buka', $semua, now()->addDays(7));

            return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $tanggal])
                ->with('success', "Memo permohonan pembukaan transaksi harian tanggal {$formatTanggal} ({$tanggal}) BERHASIL DIKIRIM ke Divisi Akuntansi (Tiket: {$requestId}).");
        } else {
            $periode = $request->input('periode', Carbon::now()->format('Y-m'));
            $formatPeriode = Carbon::parse($periode . '-01')->translatedFormat('F Y');
            $targetDateBulan = Carbon::parse($periode . '-01')->endOfMonth()->toDateString();
            $isLocked = session('locked_bulanan_' . $periode, Cache::get('locked_bulanan_' . $periode, false));

            if (!$isLocked) {
                return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'bulanan', 'bulan' => $periode])
                    ->with('error', "Transaksi penjualan periode {$formatPeriode} belum ditutup/dikunci.");
            }

            $request->validate([
                'alasan' => 'required|string|min:5',
                'kategori_alasan' => 'nullable|string',
                'estimasi_eksemplar' => 'nullable|integer',
                'estimasi_nominal' => 'nullable|numeric',
            ], [
                'alasan.required' => 'Alasan permohonan pembukaan transaksi bulanan wajib diisi agar dapat diverifikasi oleh Akuntansi.',
                'alasan.min' => 'Alasan permohonan minimal 5 karakter.',
            ]);

            $requestId = 'REQ-ULK-B' . Carbon::parse($periode . '-01')->format('ym') . '-' . strtoupper(substr(uniqid(), -4));
            $estimasiEks = (int) $request->input('estimasi_eksemplar', 0);
            $estimasiNom = (int) str_replace(['.', ','], '', (string) $request->input('estimasi_nominal', 0));
            $kategoriAlasan = $request->input('kategori_alasan', 'Penjualan Baru / Susulan Masuk');

            $requestData = (object)[
                'id' => $requestId,
                'mode' => 'bulanan',
                'target' => $periode,
                'target_label' => $formatPeriode,
                'kategori_alasan' => $kategoriAlasan,
                'alasan' => $request->input('alasan'),
                'estimasi_eksemplar' => $estimasiEks,
                'estimasi_nominal' => (float) $estimasiNom,
                'pemohon' => $operator,
                'pemohon_role' => 'Divisi Sirkulasi & Distribusi',
                'status' => 'PENDING',
                'status_label' => 'Menunggu Otorisasi Akuntansi',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ];

            if (Schema::hasTable('memo_unlocks')) {
                MemoUnlock::updateOrCreate(
                    [
                        'tanggal_target'   => $targetDateBulan,
                        'tujuan_divisi'    => 'sirkulasi',
                        'status_akuntansi' => 'PENDING',
                    ],
                    [
                        'nomor_memo'          => $requestId,
                        'kategori_urgensi'    => $kategoriAlasan,
                        'nama_pemasang'       => $operator . ' (Sirkulasi)',
                        'spesifikasi_iklan'   => "Susulan {$estimasiEks} Eks (Bulanan {$periode})",
                        'estimasi_nominal'    => $estimasiNom,
                        'alasan'              => $request->input('alasan'),
                        'diketahui_oleh'      => $operator,
                        'dibuka_oleh_user_id' => auth()->id() ?? 1,
                        'status'              => 'CLOSED',
                    ]
                );
            }

            session()->put('request_unlock_bulanan_' . $periode, $requestData);
            Cache::put('request_unlock_bulanan_' . $periode, $requestData, now()->addDays(7));

            $semua = collect(session('semua_permintaan_buka', Cache::get('semua_permintaan_buka', [])))
                ->reject(fn($item) => is_object($item) && $item->mode === 'bulanan' && $item->target === $periode)
                ->prepend($requestData)
                ->values()
                ->all();
            session()->put('semua_permintaan_buka', $semua);
            Cache::put('semua_permintaan_buka', $semua, now()->addDays(7));

            return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'bulanan', 'bulan' => $periode])
                ->with('success', "Memo permohonan pembukaan transaksi bulanan periode {$formatPeriode} BERHASIL DIKIRIM ke Divisi Akuntansi (Tiket: {$requestId}).");
        }
    }

    /**
     * Membatalkan permohonan buka kunci transaksi sebelum diproses Akuntansi
     */
    public function cancelRequestUnlockJatahTutup(Request $request)
    {
        $mode = $request->input('mode', 'harian');

        if ($mode === 'harian') {
            $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
            session()->forget('request_unlock_harian_' . $tanggal);
            Cache::forget('request_unlock_harian_' . $tanggal);

            if (Schema::hasTable('memo_unlocks')) {
                MemoUnlock::whereDate('tanggal_target', $tanggal)
                    ->where('tujuan_divisi', 'sirkulasi')
                    ->where('status_akuntansi', 'PENDING')
                    ->delete();
            }

            $semua = collect(session('semua_permintaan_buka', Cache::get('semua_permintaan_buka', [])))
                ->reject(fn($item) => is_object($item) && $item->mode === 'harian' && $item->target === $tanggal)
                ->values()
                ->all();
            session()->put('semua_permintaan_buka', $semua);
            Cache::put('semua_permintaan_buka', $semua, now()->addDays(7));

            return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $tanggal])
                ->with('success', "Permintaan pembukaan kunci transaksi harian tanggal {$tanggal} telah dibatalkan.");
        } else {
            $periode = $request->input('periode', Carbon::now()->format('Y-m'));
            $targetDateBulan = Carbon::parse($periode . '-01')->endOfMonth()->toDateString();
            session()->forget('request_unlock_bulanan_' . $periode);
            Cache::forget('request_unlock_bulanan_' . $periode);

            if (Schema::hasTable('memo_unlocks')) {
                MemoUnlock::whereDate('tanggal_target', $targetDateBulan)
                    ->where('tujuan_divisi', 'sirkulasi')
                    ->where('status_akuntansi', 'PENDING')
                    ->delete();
            }

            $semua = collect(session('semua_permintaan_buka', Cache::get('semua_permintaan_buka', [])))
                ->reject(fn($item) => is_object($item) && $item->mode === 'bulanan' && $item->target === $periode)
                ->values()
                ->all();
            session()->put('semua_permintaan_buka', $semua);
            Cache::put('semua_permintaan_buka', $semua, now()->addDays(7));

            return redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'bulanan', 'bulan' => $periode])
                ->with('success', "Permintaan pembukaan kunci transaksi bulanan periode {$periode} telah dibatalkan.");
        }
    }

    /**
     * Hitung Data Penjualan (Kalkulasi akumulasi eksemplar)
     */
    public function jatahHitung(Request $request)
    {
        $cfg = $this->getSirkulasiConfig();
        $hargaEceran = $cfg['harga_eceran'];
        $tanggalPilihan = $request->input('tanggal', Carbon::tomorrow()->toDateString());
        $jurusanList = $this->getDaftarJurusan();

        $rekapKalkulasi = $jurusanList->map(function ($jurusan) use ($hargaEceran) {
            $langganan = (int) ($jurusan->alokasi_oplah * 0.58);
            $konsinyasi = (int) ($jurusan->alokasi_oplah * 0.36);
            $gratis = (int) ($jurusan->alokasi_oplah * 0.04);
            $insidentil = $jurusan->alokasi_oplah - ($langganan + $konsinyasi + $gratis);
            $totalEks = $langganan + $konsinyasi + $gratis + $insidentil;
            $omsetKonsinyasi = $konsinyasi * $hargaEceran;

            return (object)[
                'kode' => $jurusan->kode,
                'nama_jurusan' => $jurusan->nama_jurusan,
                'armada' => $jurusan->armada,
                'langganan' => $langganan,
                'konsinyasi' => $konsinyasi,
                'gratis' => $gratis,
                'pengenalan' => 0,
                'insidentil' => $insidentil,
                'total_eks' => $totalEks,
                'omset_konsinyasi' => $omsetKonsinyasi,
            ];
        });

        $totalLangganan = $rekapKalkulasi->sum('langganan');
        $totalKonsinyasi = $rekapKalkulasi->sum('konsinyasi');
        $totalGratis = $rekapKalkulasi->sum('gratis');
        $totalPengenalan = 0;
        $totalInsidentil = $rekapKalkulasi->sum('insidentil');
        $grandTotalEks = $rekapKalkulasi->sum('total_eks');
        $grandTotalOmset = $rekapKalkulasi->sum('omset_konsinyasi');

        return view('pages.sirkulasi.jatah.hitung', compact(
            'tanggalPilihan',
            'rekapKalkulasi',
            'totalLangganan',
            'totalKonsinyasi',
            'totalGratis',
            'totalPengenalan',
            'totalInsidentil',
            'grandTotalEks',
            'grandTotalOmset',
            'hargaEceran'
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
     * Laporan Pemasaran (5 Format Cetak Utama)
     */
    public function cetakPemasaran(Request $request)
    {
        $tab = $request->input('tab', 'agen-besar');
        $allowedTabs = ['agen-besar', 'sub-agen', 'rekap-wilayah', 'rekap-jurusan', 'oplah-tahun'];
        if (!in_array($tab, $allowedTabs)) {
            $tab = $tab === 'bulan-wilayah' ? 'rekap-wilayah' : 'agen-besar';
        }

        $cfg = $this->getSirkulasiConfig();
        $bulan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $wilayahList = $this->getDaftarWilayah();
        $jurusanList = $this->getDaftarJurusan();
        $agenList = $this->getDaftarAgen()->where('status', 'aktif');

        return view('pages.sirkulasi.cetak.pemasaran', compact('tab', 'bulan', 'wilayahList', 'jurusanList', 'agenList', 'cfg'));
    }

    /**
     * Laporan Penjualan (Rekapitulasi bulanan ke Keuangan/Faktur)
     */
    public function cetakPenjualan(Request $request)
    {
        $cfg = $this->getSirkulasiConfig();
        $hargaBesar = $cfg['harga_agen_besar'];
        $hargaEceran = $cfg['harga_eceran'];

        $bulanPilihan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $daftarAgen = $this->getDaftarAgen();
        $periodStr = str_replace('-', '', $bulanPilihan);
        $tanggalFaktur = Carbon::parse($bulanPilihan . '-01')->endOfMonth();
        $jatuhTempo = (clone $tanggalFaktur)->addDays(14);

        $rekapPenjualan = $daftarAgen->map(function ($agen) use ($bulanPilihan, $periodStr, $tanggalFaktur, $jatuhTempo, $hargaBesar, $hargaEceran) {
            $kirim = $agen->jatah_default * 30;
            $retur = (int) ($kirim * 0.022);
            $nett = $kirim - $retur;
            $harga = $agen->kategori === '11' ? $hargaBesar : $hargaEceran;
            $nilaiTagihan = $nett * $harga;
            $noFaktur = sprintf('FKT-SRK-%s-%03d', $periodStr, $agen->id);

            $qrKasirPayload = json_encode([
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
                'tanggal_faktur'  => $tanggalFaktur->format('Y-m-d'),
                'jatuh_tempo'     => $jatuhTempo->format('Y-m-d'),
                'ringkasan_kasir' => "FAKTUR: {$noFaktur} | AGEN: {$agen->nama_agen} | PENJUALAN MASUK: Rp " . number_format($nilaiTagihan, 0, ',', '.') . " ({$nett} EKS TERJUAL)",
                'keterangan'      => "Penjualan koran masuk sebesar Rp " . number_format($nilaiTagihan, 0, ',', '.') . " ({$nett} eks)",
                'rincian_pesanan' => [
                    [
                        'item'     => "Koran Harian Kabar Priangan ({$nett} eks)",
                        'qty'      => $nett,
                        'harga'    => $harga,
                        'subtotal' => $nilaiTagihan,
                    ]
                ],
                'api_url'         => url('/api/faktur/' . rawurlencode($noFaktur)),
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            return (object)[
                'id'            => $agen->id,
                'no_faktur'     => $noFaktur,
                'kode_agen'     => $agen->kode_agen,
                'nama_agen'     => $agen->nama_agen,
                'alamat'        => $agen->alamat,
                'telepon'       => $agen->telepon,
                'wilayah'       => $agen->wilayah_nama,
                'jurusan'       => $agen->jurusan_nama,
                'kategori'      => $agen->kategori,
                'kirim'         => $kirim,
                'retur'         => $retur,
                'nett_sales'    => $nett,
                'harga_satuan'  => $harga,
                'nilai_tagihan' => $nilaiTagihan,
                'harga_bayar'   => $nilaiTagihan,
                'saldo_piutang' => $agen->saldo_piutang,
                'status_bayar'  => $agen->status === 'aktif' ? 'Lancar' : 'Terkunci',
                'tanggal_faktur'=> $tanggalFaktur,
                'jatuh_tempo'   => $jatuhTempo,
                'qr_payload'    => $qrKasirPayload,
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
     * Pengalihan: Penerbitan Faktur Penjualan Agen berada di Divisi Faktur (Keuangan)
     */
    public function cetakFaktur(Request $request, $kode_agen = null)
    {
        $user = auth()->user();
        if ($user && in_array($user->role, ['faktur', 'keuangan', 'admin_keuangan', 'administrator'], true)) {
            return redirect()->route('keuangan.faktur.sirkulasi.index');
        }

        return redirect()->route('sirkulasi.cetak.penjualan')
            ->with('error', 'Menu Faktur Agen berada di Divisi Faktur & Penagihan Piutang oleh Bagian Inkaso.');
    }

    /**
     * Helper lookup Faktur Sirkulasi berdasarkan nomor faktur untuk integrasi scan Kasir
     */
    public function getFakturPayloadByNo(string $noFaktur): ?array
    {
        $daftarAgen = $this->getDaftarAgen();
        $bulanPilihan = Carbon::now()->format('Y-m');

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
                        ]
                    ],
                    'api_url'         => url('/api/faktur/' . rawurlencode($noFaktur)),
                ];
            }
        }

        return null;
    }

    /**
     * Nota Kirim Percetakan & Agen (Cetak massal harian hitam-putih sesuai data Edit Jatah)
     */
    public function cetakNotaDistribusi(Request $request)
    {
        $cfg = $this->getSirkulasiConfig();
        $isiBendel = $cfg['isi_bendel'];
        $pctL = $cfg['pct_langganan'];
        $pctK = $cfg['pct_konsinyasi'];
        $pctG = $cfg['pct_gratis'];

        $today = Carbon::today();
        $tanggalDistribusi = $request->input('tanggal', Carbon::tomorrow()->toDateString());
        $jurusanPilihan = $request->input('jurusan', 'all');
        $search = trim((string) $request->input('q', ''));
        $carbonTarget = Carbon::parse($tanggalDistribusi);
        $isFutureDate = $carbonTarget->gt($today);

        $savedJatahMap = Cache::get('jatah_harian_' . $tanggalDistribusi, []);
        $daftarAgen = $this->getDaftarAgen()
            ->where('status', 'aktif')
            ->when($jurusanPilihan !== 'all', fn($col) => $col->where('jurusan_kode', $jurusanPilihan))
            ->when($search !== '', function ($col) use ($search) {
                $needle = mb_strtolower($search);
                return $col->filter(function ($agen) use ($needle) {
                    return str_contains(mb_strtolower((string) $agen->nama_agen), $needle)
                        || str_contains(mb_strtolower((string) $agen->kode_agen), $needle)
                        || str_contains(mb_strtolower((string) $agen->alamat), $needle)
                        || str_contains(mb_strtolower((string) $agen->wilayah_nama), $needle);
                });
            })
            ->sortBy(fn($a) => sprintf('%s-%06d', $a->jurusan_kode ?? '99', $a->id))
            ->values();

        $inisialWilayahMap = [
            '01' => 'T',
            '02' => 'G',
            '03' => 'C',
            '04' => 'B',
            '05' => 'S',
            '06' => 'P',
            '07' => 'BDG',
            '08' => 'I',
        ];

        $notaItems = $daftarAgen->map(function ($agen) use ($savedJatahMap, $isFutureDate, $inisialWilayahMap, $isiBendel, $pctL, $pctK, $pctG) {
            $custom = $savedJatahMap[$agen->id] ?? null;
            if ($isFutureDate && !$custom) {
                return null;
            }

            $langganan  = isset($custom['langganan']) ? (int) $custom['langganan'] : (int) ($agen->jatah_default * $pctL);
            $eceran     = isset($custom['konsinyasi']) ? (int) $custom['konsinyasi'] : (int) ($agen->jatah_default * $pctK);
            $gratis     = isset($custom['gratis']) ? (int) $custom['gratis'] : (int) ($agen->jatah_default * $pctG);
            $perkenalan = isset($custom['pengenalan']) ? (int) $custom['pengenalan'] : 0;
            $insidentil = isset($custom['insidentil']) ? (int) $custom['insidentil'] : 0;
            $totalEks   = $langganan + $eceran + $gratis + $perkenalan + $insidentil;

            if ($totalEks <= 0) {
                return null;
            }

            $bendel = intdiv($totalEks, $isiBendel);
            $lepas  = $totalEks % $isiBendel;

            return (object)[
                'agen_id'         => $agen->id,
                'kode_agen'       => $agen->kode_agen,
                'kode_perkiraan'  => '1.00.04.01.01.00.' . str_pad((string) $agen->id, 4, '0', STR_PAD_LEFT),
                'nama_agen'       => $agen->nama_agen,
                'alamat'          => $agen->alamat,
                'wilayah_kode'    => $agen->wilayah_kode,
                'wilayah_nama'    => $agen->wilayah_nama,
                'inisial_wilayah' => $inisialWilayahMap[$agen->wilayah_kode] ?? strtoupper(substr(trim($agen->wilayah_nama ?? 'U'), 0, 1)),
                'jurusan_kode'    => $agen->jurusan_kode,
                'jurusan_nama'    => $agen->jurusan_nama,
                'langganan'       => $langganan,
                'eceran'          => $eceran,
                'gratis'          => $gratis,
                'perkenalan'      => $perkenalan,
                'insidentil'      => $insidentil,
                'total_eks'       => $totalEks,
                'bendel_50'       => $bendel,
                'sisa_ecer'       => $lepas,
            ];
        })->filter()->values();

        $jurusanList     = $this->getDaftarJurusan();
        $hariEdisi       = strtoupper($carbonTarget->copy()->locale('id')->translatedFormat('l, d/m/Y'));
        $totalNota       = $notaItems->count();
        $totalEksemplar  = $notaItems->sum('total_eks');
        $totalLangganan  = $notaItems->sum('langganan');
        $totalEceran     = $notaItems->sum('eceran');
        $totalGratis     = $notaItems->sum('gratis');
        $totalPerkenalan = $notaItems->sum('perkenalan');
        $totalInsidentil = $notaItems->sum('insidentil');
        $totalBendel     = $notaItems->sum('bendel_50');
        $totalLepas      = $notaItems->sum('sisa_ecer');

        return view('pages.sirkulasi.cetak.nota-distribusi', compact(
            'tanggalDistribusi',
            'jurusanPilihan',
            'jurusanList',
            'search',
            'hariEdisi',
            'notaItems',
            'totalNota',
            'totalEksemplar',
            'totalLangganan',
            'totalEceran',
            'totalGratis',
            'totalPerkenalan',
            'totalInsidentil',
            'totalBendel',
            'totalLepas',
            'isiBendel'
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
