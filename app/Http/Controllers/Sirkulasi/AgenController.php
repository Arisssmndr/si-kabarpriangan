<?php

namespace App\Http\Controllers\Sirkulasi;

use App\Http\Controllers\Controller;
use App\Models\Agen;
use App\Models\AuditLogAkuntansi;
use App\Services\SirkulasiDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class AgenController extends Controller
{
    public function __construct(protected SirkulasiDataService $sirkulasi)
    {
    }

    /**
     * Tabel Data Agen (Divisi Sirkulasi Read-Only; Akuntansi & Admin Full CRUD).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kategoriFilter = $request->input('kategori', 'all');
        $statusFilter = $request->input('status', 'all');
        $wilayahFilter = $request->input('wilayah', 'all');
        $jurusanFilter = $request->input('jurusan', 'all');

        $agenList = $this->sirkulasi->getDaftarAgen();

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

        $wilayahList = $this->sirkulasi->getDaftarWilayah();
        $jurusanList = $this->sirkulasi->getDaftarJurusan();
        $jenisList = $this->sirkulasi->getDaftarJenisAgen();
        $canManageAgen = $this->sirkulasi->canManageAgen();

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
     * Tambah Data Agen Baru (Khusus Divisi Akuntansi & Administrator).
     */
    public function store(Request $request)
    {
        if (!$this->sirkulasi->canManageAgen()) {
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

        $wilayahObj = $this->sirkulasi->getDaftarWilayah()->firstWhere('kode', $request->wilayah_kode);
        $jurusanObj = $this->sirkulasi->getDaftarJurusan()->firstWhere('kode', $request->jurusan_kode);
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
                'alamat'         => $request->input('alamat') ?: ('Wilayah ' . $wilayahNama),
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
     * Edit Data Agen (Khusus Divisi Akuntansi & Administrator).
     */
    public function update(Request $request, $id)
    {
        if (!$this->sirkulasi->canManageAgen()) {
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
        $wilayahObj = $this->sirkulasi->getDaftarWilayah()->firstWhere('kode', $request->wilayah_kode);
        $jurusanObj = $this->sirkulasi->getDaftarJurusan()->firstWhere('kode', $request->jurusan_kode);
        $wilayahNama = $wilayahObj->nama ?? $agen->wilayah;
        $jurusanNama = $jurusanObj->nama_jurusan ?? 'Tasikmalaya Kota - Singaparna';
        $isNonAktif = $kategori === '00';

        $agen->update([
            'nama_agen'      => $request->nama_agen,
            'kategori'       => $kategori,
            'induk_agen'     => $request->input('induk_agen') ?: '-',
            'rute_kode'      => $request->jurusan_kode,
            'wilayah'        => $wilayahNama,
            'alamat'         => $request->input('alamat') ?: $agen->alamat,
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
     * Aktifkan / Non-Aktifkan Status Agen (Khusus Divisi Akuntansi & Administrator).
     */
    public function toggleStatus($id)
    {
        if (!$this->sirkulasi->canManageAgen()) {
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
     * Hapus / Non-Aktifkan Agen (Khusus Divisi Akuntansi & Administrator).
     */
    public function destroy($id)
    {
        if (!$this->sirkulasi->canManageAgen()) {
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
}
