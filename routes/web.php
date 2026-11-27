<?php

use App\Http\Controllers\Accounting\BebanDanBankController as AccountingBebanDanBankController;
use App\Http\Controllers\Accounting\CetakSubledgerController as AccountingCetakSubledgerController;
use App\Http\Controllers\Accounting\DashboardController as AccountingDashboardController;
use App\Http\Controllers\Accounting\GeneralLedgerController as AccountingGeneralLedgerController;
use App\Http\Controllers\Accounting\HutangVendorController as AccountingHutangVendorController;
use App\Http\Controllers\Accounting\KartuIklanController as AccountingKartuIklanController;
use App\Http\Controllers\Accounting\KartuSirkulasiController as AccountingKartuSirkulasiController;
use App\Http\Controllers\Accounting\LaporanKeuanganController as AccountingLaporanKeuanganController;
use App\Http\Controllers\Accounting\PengaturanController as AccountingPengaturanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenisIklanController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\KategoriMediaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PembayaranIklanController;
use App\Http\Controllers\Keuangan\AnggaranController;
use App\Http\Controllers\Keuangan\FakturController;
use App\Http\Controllers\Keuangan\InkasoController;
use App\Http\Controllers\Keuangan\KasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Sirkulasi\AgenController as SirkulasiAgenController;
use App\Http\Controllers\Sirkulasi\AlamatAgenController as SirkulasiAlamatAgenController;
use App\Http\Controllers\Sirkulasi\DashboardController as SirkulasiDashboardController;
use App\Http\Controllers\Sirkulasi\JatahCopyController as SirkulasiJatahCopyController;
use App\Http\Controllers\Sirkulasi\JatahEditController as SirkulasiJatahEditController;
use App\Http\Controllers\Sirkulasi\JatahHitungController as SirkulasiJatahHitungController;
use App\Http\Controllers\Sirkulasi\JatahTutupController as SirkulasiJatahTutupController;
use App\Http\Controllers\Sirkulasi\JenisAgenController as SirkulasiJenisAgenController;
use App\Http\Controllers\Sirkulasi\LaporanPemasaranController as SirkulasiLaporanPemasaranController;
use App\Http\Controllers\Sirkulasi\LaporanPenjualanController as SirkulasiLaporanPenjualanController;
use App\Http\Controllers\Sirkulasi\MemoHistoryController as SirkulasiMemoHistoryController;
use App\Http\Controllers\Sirkulasi\NotaDistribusiController as SirkulasiNotaDistribusiController;
use App\Http\Controllers\Sirkulasi\PengepakanController as SirkulasiPengepakanController;
use App\Http\Controllers\Sirkulasi\PengaturanController as SirkulasiPengaturanController;
use App\Http\Controllers\Sirkulasi\RuteController as SirkulasiRuteController;
use App\Http\Controllers\Sirkulasi\WilayahController as SirkulasiWilayahController;
use App\Http\Controllers\SalesAeController;
use App\Http\Controllers\SistemConfigController;
use App\Http\Controllers\CutoffOperasionalController;
use App\Http\Controllers\KopIklanController;
use App\Http\Controllers\KopKotaController;
use App\Http\Controllers\RekapPracetakController;
use App\Http\Controllers\SppiController;
use App\Http\Controllers\TransaksiIklanPrianganController;
use App\Http\Controllers\TransaksiKoranController;
use App\Http\Controllers\TransaksiOnlineController;
use Illuminate\Support\Facades\Route;

// Halaman Depan
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

// Central Dispatcher Dashboard: Mengarahkan otomatis ke dashboard divisi pengguna yang login
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// API Bersama Lintas Divisi (Kalender Terbit & Hari Penting Nasional)
Route::middleware(['auth'])->group(function () {
    Route::get('/api/kalender-nasional', [\App\Http\Controllers\KalenderNasionalController::class, 'getBulan'])->name('api.kalender-nasional');
    Route::get('/inkaso', fn () => redirect()->route('keuangan.inkaso.mobile'))->name('inkaso.root');
});

// ==========================================
// 1. DASHBOARD & MANAJEMEN ADMINISTRATOR (Superadmin)
// ==========================================
Route::middleware(['auth', 'role:administrator'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('admin/users/api/online-status', [\App\Http\Controllers\Admin\UserController::class, 'getOnlineStatus'])->name('admin.users.online-status');
    Route::post('admin/users/{id}/force-logout', [\App\Http\Controllers\Admin\UserController::class, 'forceLogout'])->name('admin.users.force-logout');
    Route::resource('admin/users', \App\Http\Controllers\Admin\UserController::class)->names('admin.users');
    Route::patch('admin/users/{id}/status', [\App\Http\Controllers\Admin\UserController::class, 'updateStatus'])->name('admin.users.status');

    // Area 2: Security & Activity Logs Audit Trail
    Route::get('admin/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('admin.activity-logs.index');

    // Area 1: Manajemen Pencadangan Data & Disaster Recovery
    Route::get('admin/backups', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('admin.backups.index');
    Route::post('admin/backups/manual', [\App\Http\Controllers\Admin\BackupController::class, 'createManual'])->name('admin.backups.manual');
    Route::get('admin/backups/{id}/download', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('admin.backups.download');
    Route::post('admin/backups/{id}/verify', [\App\Http\Controllers\Admin\BackupController::class, 'verify'])->name('admin.backups.verify');

    // Standar Industri: Pengaturan Sistem Terpusat (System Configuration Center)
    Route::get('admin/pengaturan', [\App\Http\Controllers\Admin\SistemConfigAdminController::class, 'index'])->name('admin.pengaturan.index');
    Route::post('admin/pengaturan', [\App\Http\Controllers\Admin\SistemConfigAdminController::class, 'update'])->name('admin.pengaturan.update');

    // Konsolidasi: Laporan Eksekutif dialihkan langsung ke Dashboard Administrator Terpadu
    Route::get('admin/laporan', fn() => redirect()->route('admin.dashboard'))->name('admin.laporan.index');
    Route::get('admin/laporan/cetak', fn() => redirect()->route('admin.dashboard'))->name('admin.laporan.cetak');

    // Standar Industri: Pemantauan Kesehatan Sistem & Live Log Viewer (System Health Monitor)
    Route::get('admin/sistem', [\App\Http\Controllers\Admin\SistemMonitorController::class, 'index'])->name('admin.sistem.index');
    Route::post('admin/sistem/clear-cache', [\App\Http\Controllers\Admin\SistemMonitorController::class, 'clearCache'])->name('admin.sistem.clear-cache');
    Route::get('admin/sistem/download-log', [\App\Http\Controllers\Admin\SistemMonitorController::class, 'downloadLog'])->name('admin.sistem.download-log');
    Route::post('admin/sistem/clear-log', [\App\Http\Controllers\Admin\SistemMonitorController::class, 'clearLog'])->name('admin.sistem.clear-log');
});

// ==========================================
// 2. DIVISI IKLAN (Koran, Online, Priangan TV)
// Akses: Role Iklan, Kasir & Administrator
// ==========================================
Route::middleware(['auth', 'role:iklan,kasir'])->group(function () {
    Route::get('/iklan/dashboard', [DashboardController::class, 'iklan'])->name('iklan.dashboard');

    // Master Data Terpadu Iklan
    Route::resource('kategori-media', KategoriMediaController::class);
    Route::resource('jenis-iklan', JenisIklanController::class);
    Route::get('/api/kategori-media/{id}/next-code', [JenisIklanController::class, 'getNextCode'])
        ->name('kategori-media.next-code');

    // Pengalihan Rute Kategori Lama
    Route::get('/iklankoran', fn() => redirect()->route('jenis-iklan.index'))->name('iklankoran.index');
    Route::get('/iklanonline', fn() => redirect()->route('jenis-iklan.index'))->name('iklanonline.index');
    Route::get('/iklanpriangan', fn() => redirect()->route('jenis-iklan.index'))->name('iklanpriangan.index');

    // Master Pelanggan (CRM Periklanan)
    Route::resource('pelanggan', PelangganController::class);
    Route::get('/api/pelanggan/search', [PelangganController::class, 'search'])->name('pelanggan.search');

    // Transaksi Iklan Koran
    Route::resource('transaksikoran', TransaksiKoranController::class);
    Route::get('/transaksikoran/{id}/cetak', [TransaksiKoranController::class, 'cetak'])->name('transaksikoran.cetak');
    Route::get('/transaksikoran/{id}/lembar-kerja', [TransaksiKoranController::class, 'lembarKerjaPracetak'])->name('transaksikoran.lembar-kerja');
    Route::post('/transaksikoran/{id}/batalkan', [TransaksiKoranController::class, 'batalkan'])->name('transaksikoran.batalkan');
    Route::get('/transaksikoran/{id}/sppi', [SppiController::class, 'cetak'])->name('transaksikoran.sppi');
    Route::post('/sppi/{id}/konfirmasi-ttd', [SppiController::class, 'konfirmasiTtd'])->name('sppi.konfirmasi-ttd');

    // Modul Rekap Pracetak (Dummy Layout Sheet Harian per Tanggal Terbit)
    Route::get('/iklan/rekap-pracetak', [RekapPracetakController::class, 'index'])->name('iklan.rekap-pracetak.index');
    Route::get('/iklan/rekap-pracetak/cetak', [RekapPracetakController::class, 'cetak'])->name('iklan.rekap-pracetak.cetak');

    // Operasional Cut-Off & Memo Unlock Supervisor
    Route::post('/iklan/cutoff/tutup', [CutoffOperasionalController::class, 'tutup'])->name('iklan.cutoff.tutup');
    Route::post('/iklan/cutoff/memo-unlock', [CutoffOperasionalController::class, 'bukaMemo'])->name('iklan.cutoff.memo-unlock');
    Route::post('/iklan/cutoff/memo-unlock/{id}/tutup', [CutoffOperasionalController::class, 'tutupMemo'])->name('iklan.cutoff.memo-unlock.tutup');
    Route::get('/iklan/cutoff/memo-unlock/{id}/cetak', [CutoffOperasionalController::class, 'cetakMemo'])->name('iklan.cutoff.memo-unlock.cetak');
    Route::get('/iklan/memo/history', [CutoffOperasionalController::class, 'memoHistory'])->name('iklan.memo.history');

    // Pencatatan Pembayaran & Kwitansi Kasir Digital
    Route::get('/pembayaran-iklan', [PembayaranIklanController::class, 'index'])->name('pembayaran-iklan.index');
    Route::post('/pembayaran-iklan', [PembayaranIklanController::class, 'store'])->name('pembayaran-iklan.store');
    Route::get('/pembayaran-iklan/{id}/kwitansi', [PembayaranIklanController::class, 'kwitansi'])->name('pembayaran-iklan.kwitansi');

    // Master Data Sales / Account Executive
    Route::resource('master/sales-ae', SalesAeController::class)->names('sales-ae');
    Route::patch('/master/sales-ae/{salesAe}/toggle-status', [SalesAeController::class, 'toggleStatus'])->name('sales-ae.toggle-status');
    Route::get('/master/sales-ae/generate-kode', [SalesAeController::class, 'generateKode'])->name('sales-ae.generate-kode');

    // Master Data Kop Kota (Penempatan Halaman Koran & Wilayah Biro)
    Route::resource('master/kop-kota', KopKotaController::class)->names('kop-kota');
    Route::patch('/master/kop-kota/{kopKotum}/toggle-status', [KopKotaController::class, 'toggleStatus'])->name('kop-kota.toggle-status');

    // Master Data Kop Iklan (Rubrikasi Iklan Baris)
    Route::resource('master/kop-iklan', KopIklanController::class)->names('kop-iklan');
    Route::patch('/master/kop-iklan/{kopIklan}/toggle-status', [KopIklanController::class, 'toggleStatus'])->name('kop-iklan.toggle-status');

    // Konfigurasi Tarif, PPN, dan Operasional Periklanan
    Route::get('/iklan/pengaturan', [SistemConfigController::class, 'index'])->name('iklan.pengaturan.index');
    Route::post('/iklan/pengaturan', [SistemConfigController::class, 'update'])->name('iklan.pengaturan.update');

    // Transaksi Iklan Online
    Route::resource('transaksionline', TransaksiOnlineController::class);
    Route::get('/transaksionline/{id}/cetak', [TransaksiOnlineController::class, 'cetak'])->name('transaksionline.cetak');
    Route::get('/transaksionline/{id}/sppi', [SppiController::class, 'cetakOnline'])->name('transaksionline.sppi');
    Route::post('/transaksionline/{id}/bukti-tayang', [TransaksiOnlineController::class, 'simpanBuktiTayang'])->name('transaksionline.bukti-tayang');
    Route::post('/transaksionline/{id}/status-produksi', [TransaksiOnlineController::class, 'updateStatusProduksi'])->name('transaksionline.status-produksi');
    Route::post('/transaksionline/{id}/batalkan', [TransaksiOnlineController::class, 'batalkan'])->name('transaksionline.batalkan');

    // Transaksi Iklan Priangan TV
    Route::resource('transaksipriangan', TransaksiIklanPrianganController::class);
    Route::get('/transaksipriangan/{id}/cetak', [TransaksiIklanPrianganController::class, 'cetak'])->name('transaksipriangan.cetak');
    Route::get('/transaksipriangan/{id}/sppi', [SppiController::class, 'cetakPriangan'])->name('transaksipriangan.sppi');
    Route::post('/transaksipriangan/{id}/batalkan', [TransaksiIklanPrianganController::class, 'batalkan'])->name('transaksipriangan.batalkan');

    // Transaksi Paket Iklan Lintas Media (Cetak + Online + TV)
    Route::resource('transaksipaket', \App\Http\Controllers\PaketIklanController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::get('/transaksipaket/{id}/cetak', [\App\Http\Controllers\PaketIklanController::class, 'cetak'])->name('transaksipaket.cetak');

    // Laporan Keuangan Iklan Terpadu & Standar Pers IKL.EXE
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');
    Route::get('/laporan/rekap-sales', [LaporanController::class, 'rekapSales'])->name('laporan.rekap-sales');
    Route::get('/laporan/rekap-sales/cetak', [LaporanController::class, 'cetakRekapSales'])->name('laporan.rekap-sales.cetak');
    Route::get('/laporan/insentif-ae', [LaporanController::class, 'insentifAe'])->name('laporan.insentif-ae');
    Route::get('/laporan/insentif-ae/cetak', [LaporanController::class, 'cetakInsentifAe'])->name('laporan.insentif-ae.cetak');
    Route::get('/laporan/klasifikasi', [LaporanController::class, 'klasifikasi'])->name('laporan.klasifikasi');
    Route::get('/laporan/klasifikasi/cetak', [LaporanController::class, 'cetakKlasifikasi'])->name('laporan.klasifikasi.cetak');
    Route::get('/laporan/pembatalan', [LaporanController::class, 'pembatalan'])->name('laporan.pembatalan');
    Route::get('/laporan/pembatalan/cetak', [LaporanController::class, 'cetakPembatalan'])->name('laporan.pembatalan.cetak');

    // Pengalihan Rute Laporan Lama
    Route::match(['get', 'post'], '/laporankoran', fn() => redirect()->route('laporan.index', ['media' => 'koran']))->name('laporankoran.index');
    Route::match(['get', 'post'], '/laporanonline', fn() => redirect()->route('laporan.index', ['media' => 'online']))->name('laporanonline.index');
    Route::match(['get', 'post'], '/laporanpriangan', fn() => redirect()->route('laporan.index', ['media' => 'tv']))->name('laporanpriangan.index');
});

// Laporan Umur Piutang (Aging Piutang) — Divisi Faktur, Keuangan, Iklan & Administrator
Route::middleware(['auth', 'role:faktur,keuangan,iklan,kasir,administrator'])->group(function () {
    Route::get('/laporan/aging-piutang', [LaporanController::class, 'agingPiutang'])->name('laporan.aging-piutang');
    Route::get('/laporan/aging-piutang/cetak', [LaporanController::class, 'cetakAgingPiutang'])->name('laporan.aging-piutang.cetak');
});

// ==========================================
// 3. DIVISI FAKTUR, ANGGARAN & KEUANGAN
// Akses: Role Keuangan, Faktur, Anggaran, Admin Keuangan, Kasir, Petugas Inkaso, Kepala Divisi, & Administrator
// ==========================================
Route::middleware(['auth', 'role:keuangan,admin_keuangan,faktur,anggaran,kasir,petugas_inkaso,inkaso,kepala_divisi,administrator'])->prefix('keuangan')->name('keuangan.')->group(function () {
    // Dashboard & SOP Alur: Manajer Keuangan, Admin Keuangan, Faktur, Anggaran & Administrator
    Route::middleware('role:keuangan,admin_keuangan,faktur,anggaran,petugas_inkaso,inkaso,administrator')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'keuangan'])->name('dashboard');
        Route::get('/alur-faktur-inkaso', [DashboardController::class, 'alurFakturInkaso'])->name('alur');
    });

    // A. Modul Faktur & Piutang: Akses Faktur, Manajer Keuangan, Admin Keuangan & Administrator
    Route::middleware('role:faktur,keuangan,admin_keuangan,administrator')->prefix('faktur')->name('faktur.')->group(function () {
        Route::get('/', [FakturController::class, 'index'])->name('index');
        Route::get('/cetak-rekap', [FakturController::class, 'cetakRekap'])->name('cetak-rekap');
        Route::get('/cari-iklan', [FakturController::class, 'cariFakturIklan'])->name('cari-iklan');
        Route::get('/fungsi', [FakturController::class, 'cetakRekap'])->name('fungsi.index');
        Route::get('/create', [FakturController::class, 'create'])->name('create');
        Route::post('/', [FakturController::class, 'store'])->name('store');

        // Submodul Stock Opname & 7 Laporan Piutang Faktur Iklan (DOS faktur1.exe)
        Route::get('/stok-opname', [FakturController::class, 'stokOpname'])->name('stok-opname');
        Route::get('/stok-opname/cetak', [FakturController::class, 'cetakStokOpname'])->name('stok-opname.cetak');
        Route::get('/stock-opname', [FakturController::class, 'stokOpname'])->name('stock-opname');
        Route::get('/stock-opname/cetak', [FakturController::class, 'cetakStokOpname'])->name('stock-opname.cetak');
        Route::prefix('iklan')->name('iklan.')->group(function () {
            Route::get('/', [FakturController::class, 'index'])->name('index');
            Route::get('/susulan', function (\Illuminate\Http\Request $request) {
                return redirect()->route('keuangan.faktur.index', array_merge($request->all(), ['tab' => 'susulan']));
            })->name('susulan');
            Route::get('/rekap', function (\Illuminate\Http\Request $request) {
                return redirect()->route('keuangan.faktur.index', array_merge($request->all(), ['tab' => 'rekap_tanggal']));
            })->name('rekap');
            Route::get('/rekap/cetak', [FakturController::class, 'cetakRekap'])->name('rekap.cetak');
            Route::get('/tunai', function (\Illuminate\Http\Request $request) {
                return redirect()->route('keuangan.faktur.index', array_merge($request->all(), ['tab' => 'tunai']));
            })->name('tunai');
            Route::get('/piutang', function (\Illuminate\Http\Request $request) {
                return redirect()->route('keuangan.faktur.index', array_merge($request->all(), ['tab' => 'piutang']));
            })->name('piutang');
            Route::get('/piutang/cetak', [FakturController::class, 'cetakRekap'])->name('piutang.cetak');
            Route::get('/stopper-bonus', function (\Illuminate\Http\Request $request) {
                return redirect()->route('keuangan.faktur.index', array_merge($request->all(), ['tab' => 'stopper_bonus']));
            })->name('stopper-bonus');
            Route::get('/stok-opname', [FakturController::class, 'stokOpname'])->name('stok-opname');
            Route::get('/stok-opname/cetak', [FakturController::class, 'cetakStokOpname'])->name('stok-opname.cetak');
        });

        // Submodul Faktur Sirkulasi (DOS faktur1.exe > SIRKULASI)
        Route::prefix('sirkulasi')->name('sirkulasi.')->group(function () {
            Route::get('/', [FakturController::class, 'sirkulasiIndex'])->name('index');
            Route::get('/susulan', [FakturController::class, 'sirkulasiSusulan'])->name('susulan');
            Route::get('/rekap', [FakturController::class, 'sirkulasiRekap'])->name('rekap');
            Route::get('/rekap/cetak', [FakturController::class, 'cetakRekapSirkulasi'])->name('rekap.cetak');
            Route::get('/insidentil', [FakturController::class, 'sirkulasiInsidentil'])->name('insidentil');
        });

        Route::get('/{id}', [FakturController::class, 'show'])->where('id', '[0-9]+')->name('show');
        Route::post('/{id}/bayar', [FakturController::class, 'catatPembayaran'])->where('id', '[0-9]+')->name('bayar');
        Route::get('/{id}/cetak', [FakturController::class, 'cetak'])->where('id', '[0-9]+')->name('cetak');
        Route::get('/{id}/surat-tugas', [FakturController::class, 'cetakSuratTugas'])->where('id', '[0-9]+')->name('surat-tugas');
    });

    // B. Modul Penagihan Lapangan (Inkaso)
    // 1. Khusus Admin Faktur & Keuangan: Atur Penugasan & Daftar Faktur Lapangan Desktop
    Route::middleware('role:faktur,keuangan,admin_keuangan,administrator')->prefix('inkaso')->name('inkaso.')->group(function () {
        Route::get('/', [InkasoController::class, 'index'])->name('index');
        Route::post('/assign', [InkasoController::class, 'assign'])->name('assign');
        Route::post('/setoran', [InkasoController::class, 'storeSetoran'])->name('setoran.store');
        Route::post('/{id}/laporan-hasil', [InkasoController::class, 'simpanLaporanHasil'])->name('laporan-hasil.store');
        Route::get('/cetak-rekap-hasil', [InkasoController::class, 'cetakRekapHasil'])->name('rekap-hasil.cetak');
        Route::get('/surat-tugas/{id}/cetak', [InkasoController::class, 'cetakSuratTugas'])->name('surat-tugas.cetak');
        Route::get('/kwitansi/{id}/cetak', [InkasoController::class, 'kwitansi'])->name('kwitansi.cetak');
        Route::get('/rekap/cetak', [KasController::class, 'cetakRekapInkaso'])->name('rekap.cetak');
    });

    // 2. Khusus Petugas Inkaso (Mobile-First Lapangan): Terima Tugas, Tagihan Aktif & Setoran BSK
    Route::middleware('role:petugas_inkaso,inkaso,faktur,keuangan,admin_keuangan,administrator')->prefix('inkaso')->name('inkaso.')->group(function () {
        Route::get('/mobile', [InkasoController::class, 'mobileIndex'])->name('mobile');
        Route::post('/penugasan/{id}/konfirmasi', [InkasoController::class, 'konfirmasiPenugasan'])->name('penugasan.konfirmasi');
        Route::post('/simpan-setoran-lapangan', [InkasoController::class, 'simpanSetoranLapangan'])->name('setoran-lapangan.store');
        Route::put('/setoran-lapangan/{id}', [InkasoController::class, 'updateSetoranLapangan'])->name('setoran-lapangan.update');
        Route::get('/bsk/{no_bsk}', [InkasoController::class, 'showBsk'])->name('bsk.show');
        Route::post('/profile', [InkasoController::class, 'updateProfile'])->name('profile.update');
        // Halaman Khusus Rincian Surat Penugasan & Aksi Grup
        Route::get('/surat-penugasan/{noSuratGrup}', [InkasoController::class, 'showSuratPenugasan'])->name('surat-penugasan.show');
        Route::post('/penugasan/grup/{noSuratGrup}/konfirmasi', [InkasoController::class, 'konfirmasiGrup'])->name('penugasan.grup.konfirmasi');
        Route::post('/penugasan/grup/{noSuratGrup}/selesai', [InkasoController::class, 'selesaiGrup'])->name('penugasan.grup.selesai');
    });

    // C. Modul Pengajuan Anggaran (Cash Out)
    Route::prefix('anggaran')->name('anggaran.')->group(function () {
        // Akses Melihat & Mengajukan: Anggaran, Manajer Keuangan, Admin Keuangan, Kasir & Administrator
        Route::middleware('role:anggaran,keuangan,admin_keuangan,kasir,administrator')->group(function () {
            // Tabel Master DOS ANGGARAN.EXE
            Route::get('/tabel/bidang', [AnggaranController::class, 'tabelBidang'])->name('tabel.bidang');
            Route::get('/tabel/perkiraan', [AnggaranController::class, 'tabelPerkiraan'])->name('tabel.perkiraan');
            Route::post('/tabel/perkiraan', [AnggaranController::class, 'storePerkiraan'])->name('tabel.perkiraan.store');
            Route::put('/tabel/perkiraan/{id}', [AnggaranController::class, 'updatePerkiraan'])->name('tabel.perkiraan.update');
            Route::delete('/tabel/perkiraan/{id}', [AnggaranController::class, 'destroyPerkiraan'])->name('tabel.perkiraan.destroy');
            Route::get('/tabel/rekening-bank', [AnggaranController::class, 'tabelRekeningBank'])->name('tabel.rekening-bank');

            // Menu 2: ENTRY DOS ANGGARAN.EXE
            Route::get('/entry/rab', [AnggaranController::class, 'entryRab'])->name('entry.rab');
            Route::post('/entry/rab', [AnggaranController::class, 'storeRab'])->name('entry.rab.store');
            Route::get('/entry/tutup-anggaran', [AnggaranController::class, 'entryTutupAnggaran'])->name('entry.tutup-anggaran');
            Route::post('/entry/tutup-anggaran', [AnggaranController::class, 'prosesTutupAnggaran'])->name('entry.tutup-anggaran.proses');
            Route::get('/entry/dp-kasbon', [AnggaranController::class, 'entryDpKasbon'])->name('entry.dp-kasbon');
            Route::post('/entry/dp-kasbon', [AnggaranController::class, 'storeDpKasbon'])->name('entry.dp-kasbon.store');
            Route::post('/entry/dp-kasbon/{id}/selesai', [AnggaranController::class, 'selesaikanKasbon'])->name('entry.dp-kasbon.selesai');

            Route::get('/', [AnggaranController::class, 'index'])->name('index');
            Route::get('/create', [AnggaranController::class, 'create'])->name('create');
            Route::post('/', [AnggaranController::class, 'store'])->name('store');

            // Menu 3: CETAK DOS ANGGARAN.EXE (7 Laporan Resmi)
            Route::prefix('cetak')->name('cetak.')->group(function () {
                Route::get('/', [AnggaranController::class, 'cetakHub'])->name('hub');
                Route::get('/hub', [AnggaranController::class, 'cetakHub'])->name('index');
                Route::get('/seluruh-bidang', [AnggaranController::class, 'cetakSeluruhBidang'])->name('seluruh-bidang');
                Route::get('/per-bidang', [AnggaranController::class, 'cetakPerBidang'])->name('per-bidang');
                Route::get('/bp-tanggal', [AnggaranController::class, 'cetakBpTanggal'])->name('bp-tanggal');
                Route::get('/bp-bulan', [AnggaranController::class, 'cetakBpBulan'])->name('bp-bulan');
                Route::get('/rekap-bidang-coa', [AnggaranController::class, 'cetakRekapBidangCoa'])->name('rekap-bidang-coa');
                Route::get('/cetak-ulang-bp', [AnggaranController::class, 'cetakUlangBp'])->name('cetak-ulang-bp');
                Route::get('/tahunan', [AnggaranController::class, 'cetakTahunan'])->name('tahunan');
            });

            Route::get('/rekap-bp', [AnggaranController::class, 'rekapBp'])->name('rekap-bp');
            Route::get('/{id}/voucher', [AnggaranController::class, 'cetakVoucher'])->name('voucher');
        });

        // Akses Persetujuan (Approve/Reject): Khusus Manajer Keuangan, Admin Keuangan & Administrator
        Route::middleware('role:keuangan,admin_keuangan,administrator')->group(function () {
            Route::post('/{id}/approve', [AnggaranController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [AnggaranController::class, 'reject'])->name('reject');
        });

        // Akses Pencairan ke Kasir & Terbitkan BKK: Anggaran, Kasir, Manajer Keuangan & Administrator
        Route::middleware('role:anggaran,kasir,keuangan,admin_keuangan,administrator')->group(function () {
            Route::post('/{id}/cairkan', [AnggaranController::class, 'cairkan'])->name('cairkan');
        });
    });

    // D. Modul Buku Kas (Treasury): Akses Manajer Keuangan, Admin Keuangan, Kasir, Anggaran & Administrator
    Route::middleware('role:keuangan,admin_keuangan,kasir,anggaran,administrator')->prefix('kas')->name('kas.')->group(function () {
        Route::get('/', [KasController::class, 'index'])->name('index');
        Route::get('/harian', [KasController::class, 'harian'])->name('harian');
        Route::get('/bulanan', [KasController::class, 'bulanan'])->name('bulanan');
        Route::get('/cetak', [KasController::class, 'cetak'])->name('cetak');
    });

    // E. Modul Laporan Keuangan Terpadu: Akses Manajer Keuangan, Admin Keuangan, Kasir, Anggaran & Administrator
    Route::middleware('role:keuangan,admin_keuangan,kasir,anggaran,administrator')->group(function () {
        Route::get('/laporan', [KasController::class, 'laporan'])->name('laporan');
    });
});


// ==========================================
// 4. DIVISI ACCOUNTING
// Akses: Role Accounting & Administrator
// ==========================================
Route::middleware(['auth', 'role:accounting,administrator'])->prefix('accounting')->name('accounting.')->group(function () {
    // 0. Dashboard
    Route::get('/dashboard', [AccountingDashboardController::class, 'index'])->name('dashboard');

    // 1. Kartu Iklan
    Route::prefix('iklan')->name('iklan.')->group(function () {
        Route::get('/hitung-penjualan', [AccountingKartuIklanController::class, 'iklanHitungPenjualan'])->name('hitung-penjualan');
        Route::post('/hitung-penjualan', [AccountingKartuIklanController::class, 'prosesIklanHitungPenjualan'])->name('hitung-penjualan.proses');
        Route::post('/proses-hitung-penjualan', [AccountingKartuIklanController::class, 'prosesIklanHitungPenjualan'])->name('proses-hitung-penjualan');
        Route::get('/hitung-pembayaran', [AccountingKartuIklanController::class, 'iklanHitungPembayaran'])->name('hitung-pembayaran');
        Route::post('/hitung-pembayaran', [AccountingKartuIklanController::class, 'prosesIklanHitungPembayaran'])->name('hitung-pembayaran.proses');
        Route::post('/proses-hitung-pembayaran', [AccountingKartuIklanController::class, 'prosesIklanHitungPembayaran'])->name('proses-hitung-pembayaran');
        Route::get('/koreksi', [AccountingKartuIklanController::class, 'iklanKoreksi'])->name('koreksi');
        Route::get('/koreksi/cari-faktur', [AccountingKartuIklanController::class, 'apiCariFakturKoreksi'])->name('koreksi.cari-faktur');
        Route::post('/koreksi', [AccountingKartuIklanController::class, 'storeIklanKoreksi'])->name('koreksi.store');
        Route::post('/simpan-koreksi', [AccountingKartuIklanController::class, 'storeIklanKoreksi'])->name('simpan-koreksi');
        Route::get('/koreksi/{id}/cetak', [AccountingKartuIklanController::class, 'cetakIklanKoreksi'])->name('koreksi.cetak');
        Route::get('/cetak-koreksi/{id?}', [AccountingKartuIklanController::class, 'cetakIklanKoreksi'])->name('cetak-koreksi');
        Route::get('/buka-data', [AccountingKartuIklanController::class, 'iklanBukaData'])->name('buka-data');
        Route::post('/buka-data', [AccountingKartuIklanController::class, 'prosesIklanBukaData'])->name('buka-data.proses');
        Route::post('/proses-buka-data', [AccountingKartuIklanController::class, 'prosesIklanBukaData'])->name('proses-buka-data');
        Route::post('/batal-dp-insentif', [AccountingKartuIklanController::class, 'prosesBatalDpInsentif'])->name('batal-dp-insentif');
        Route::post('/unpull-pembayaran', [AccountingKartuIklanController::class, 'prosesUnpullPembayaranIklan'])->name('unpull-pembayaran');
        Route::get('/belanja', [AccountingKartuIklanController::class, 'iklanBelanja'])->name('belanja');
    });

    // 2. Kartu Sirkulasi
    Route::prefix('sirkulasi')->name('sirkulasi.')->group(function () {
        Route::get('/saldo-awal', [AccountingKartuSirkulasiController::class, 'sirkulasiSaldoAwal'])->name('saldo-awal');
        Route::post('/saldo-awal', [AccountingKartuSirkulasiController::class, 'prosesSirkulasiSaldoAwal'])->name('saldo-awal.proses');
        Route::get('/hitung-penjualan', [AccountingKartuSirkulasiController::class, 'sirkulasiHitungPenjualan'])->name('hitung-penjualan');
        Route::post('/hitung-penjualan', [AccountingKartuSirkulasiController::class, 'prosesSirkulasiHitungPenjualan'])->name('hitung-penjualan.proses');
        Route::get('/hitung-pembayaran', [AccountingKartuSirkulasiController::class, 'sirkulasiHitungPembayaran'])->name('hitung-pembayaran');
        Route::post('/hitung-pembayaran', [AccountingKartuSirkulasiController::class, 'prosesSirkulasiHitungPembayaran'])->name('hitung-pembayaran.proses');
        Route::get('/koreksi', [AccountingKartuSirkulasiController::class, 'sirkulasiKoreksi'])->name('koreksi');
        Route::post('/koreksi', [AccountingKartuSirkulasiController::class, 'storeSirkulasiKoreksi'])->name('koreksi.store');
        Route::get('/koreksi/{id}/cetak', [AccountingKartuSirkulasiController::class, 'cetakSirkulasiKoreksi'])->name('koreksi.cetak');
        Route::get('/faktur-bulanan', [AccountingKartuSirkulasiController::class, 'sirkulasiFakturBulanan'])->name('faktur-bulanan');
        Route::post('/faktur-bulanan/generate', [AccountingKartuSirkulasiController::class, 'prosesGenerateFakturBulanan'])->name('faktur-bulanan.generate');
        Route::post('/retur', [AccountingKartuSirkulasiController::class, 'storeVerifikasiRetur'])->name('retur.store');
        Route::get('/faktur-bulanan/{id}/cetak', [AccountingKartuSirkulasiController::class, 'cetakFakturBulanan'])->name('faktur-bulanan.cetak');
        Route::get('/agen', [AccountingKartuSirkulasiController::class, 'sirkulasiAgen'])->name('agen');
        Route::post('/agen', [AccountingKartuSirkulasiController::class, 'storeSirkulasiAgen'])->name('agen.store');
        Route::put('/agen/{id}', [AccountingKartuSirkulasiController::class, 'updateSirkulasiAgen'])->name('agen.update');
        Route::delete('/agen/{id}', [AccountingKartuSirkulasiController::class, 'destroySirkulasiAgen'])->name('agen.destroy');
        Route::patch('/agen/{id}/toggle-status', [AccountingKartuSirkulasiController::class, 'toggleStatusSirkulasiAgen'])->name('agen.toggle');
        Route::get('/agen/{id}/saldo', [AccountingKartuSirkulasiController::class, 'sirkulasiAgenSaldo'])->name('agen-saldo');
        Route::get('/buka-data', [AccountingKartuSirkulasiController::class, 'sirkulasiBukaData'])->name('buka-data');
        Route::post('/buka-data', [AccountingKartuSirkulasiController::class, 'prosesSirkulasiBukaData'])->name('buka-data.proses');
    });

    // Cetak Dokumen Memo Otorisasi Buka Data
    Route::get('/memo-unlock/{id}/cetak', [AccountingKartuSirkulasiController::class, 'cetakMemo'])->name('memo.cetak');
    Route::get('/memo/history', [AccountingKartuSirkulasiController::class, 'memoHistory'])->name('memo.history');

    // 3. Cetak Kartu (17 Laporan Sub-Ledger)
    Route::prefix('cetak')->name('cetak.')->group(function () {
        Route::get('/iklan', [AccountingCetakSubledgerController::class, 'cetakIklan'])->name('iklan');
        Route::get('/iklan/cetak', [AccountingCetakSubledgerController::class, 'cetakSubledgerIklan'])->name('iklan.cetak');
        Route::get('/sirkulasi', [AccountingCetakSubledgerController::class, 'cetakSirkulasi'])->name('sirkulasi');
        Route::get('/sirkulasi/cetak', [AccountingCetakSubledgerController::class, 'cetakSubledgerSirkulasi'])->name('sirkulasi.cetak');
    });

    // 4. Kas, Bank & Beban
    Route::prefix('beban')->name('beban.')->group(function () {
        Route::get('/verifikasi', [AccountingBebanDanBankController::class, 'bebanVerifikasi'])->name('verifikasi');
        Route::post('/verifikasi/{id}/post-gl', [AccountingBebanDanBankController::class, 'postingBebanGl'])->name('verifikasi.post');
        Route::get('/mutasi-bank', [AccountingBebanDanBankController::class, 'bebanMutasiBank'])->name('mutasi-bank');
        Route::post('/mutasi-bank/rekon', [AccountingBebanDanBankController::class, 'storeRekonBank'])->name('rekon.store');
        Route::post('/mutasi-bank/rekon/auto-match', [AccountingBebanDanBankController::class, 'autoMatchRekonBank'])->name('rekon.auto-match');
        Route::post('/mutasi-bank/rekon/{id}/adjust', [AccountingBebanDanBankController::class, 'adjustRekonBank'])->name('rekon.adjust');
        Route::delete('/mutasi-bank/rekon/{id}', [AccountingBebanDanBankController::class, 'destroyRekonBank'])->name('rekon.destroy');
        Route::get('/hutang-vendor', [AccountingHutangVendorController::class, 'index'])->name('hutang-vendor');
        Route::post('/hutang-vendor', [AccountingHutangVendorController::class, 'store'])->name('hutang-vendor.store');
        Route::post('/hutang-vendor/{id}/bayar', [AccountingHutangVendorController::class, 'bayar'])->name('hutang-vendor.bayar');
        Route::get('/akrual', [AccountingBebanDanBankController::class, 'bebanAkrual'])->name('akrual');
        Route::get('/aset-tetap', [AccountingBebanDanBankController::class, 'bebanAsetTetap'])->name('aset-tetap');
        Route::post('/aset-tetap', [AccountingBebanDanBankController::class, 'storeAsetTetap'])->name('aset-tetap.store');
        Route::delete('/aset-tetap/{kode}', [AccountingBebanDanBankController::class, 'destroyAsetTetap'])->name('aset-tetap.destroy');
        Route::post('/aset-tetap/posting-penyusutan', [AccountingBebanDanBankController::class, 'postingPenyusutanAset'])->name('aset-tetap.penyusutan');
        Route::get('/anggaran', [AccountingBebanDanBankController::class, 'bebanAnggaran'])->name('anggaran');
    });

    // 5. Jurnal & Buku Besar
    Route::prefix('gl')->name('gl.')->group(function () {
        Route::get('/jurnal', [AccountingGeneralLedgerController::class, 'glJurnal'])->name('jurnal');
        Route::get('/jurnal/cetak', [AccountingGeneralLedgerController::class, 'cetakGlJurnal'])->name('jurnal.cetak');
        Route::post('/jurnal/{id}/approve', [AccountingGeneralLedgerController::class, 'approveJurnal'])->name('jurnal.approve');
        Route::post('/jurnal/{id}/reverse', [AccountingGeneralLedgerController::class, 'reverseJurnal'])->name('jurnal.reverse');
        Route::get('/memorial', [AccountingGeneralLedgerController::class, 'glMemorial'])->name('memorial');
        Route::post('/memorial', [AccountingGeneralLedgerController::class, 'storeGlMemorial'])->name('memorial.store');
        Route::post('/realisasi-uang-muka', [AccountingGeneralLedgerController::class, 'realisasiUangMuka'])->name('realisasi-uang-muka');
        Route::get('/buku-besar', [AccountingGeneralLedgerController::class, 'glBukuBesar'])->name('buku-besar');
        Route::get('/buku-besar/cetak', [AccountingGeneralLedgerController::class, 'cetakGlBukuBesar'])->name('buku-besar.cetak');
        Route::get('/neraca-lajur', [AccountingGeneralLedgerController::class, 'glNeracaLajur'])->name('neraca-lajur');
        Route::get('/neraca-lajur/cetak', [AccountingGeneralLedgerController::class, 'cetakGlNeracaLajur'])->name('neraca-lajur.cetak');
    });

    // 6. Laporan Keuangan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/laba-rugi', [AccountingLaporanKeuanganController::class, 'labaRugi'])->name('laba-rugi');
        Route::get('/laba-rugi/cetak', [AccountingLaporanKeuanganController::class, 'cetakLabaRugi'])->name('laba-rugi.cetak');
        Route::get('/neraca', [AccountingLaporanKeuanganController::class, 'neraca'])->name('neraca');
        Route::get('/neraca/cetak', [AccountingLaporanKeuanganController::class, 'cetakNeraca'])->name('neraca.cetak');
        Route::get('/arus-kas', [AccountingLaporanKeuanganController::class, 'arusKas'])->name('arus-kas');
        Route::get('/arus-kas/cetak', [AccountingLaporanKeuanganController::class, 'cetakArusKas'])->name('arus-kas.cetak');
        Route::get('/ekuitas', [AccountingLaporanKeuanganController::class, 'ekuitas'])->name('ekuitas');
        Route::get('/ekuitas/cetak', [AccountingLaporanKeuanganController::class, 'cetakEkuitas'])->name('ekuitas.cetak');
        Route::get('/rincian-beban', [AccountingLaporanKeuanganController::class, 'rincianBeban'])->name('rincian-beban');
        Route::get('/rincian-beban/cetak', [AccountingLaporanKeuanganController::class, 'cetakRincianBeban'])->name('rincian-beban.cetak');
    });

    // 7. Pengaturan & Master
    Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
        Route::get('/coa', [AccountingPengaturanController::class, 'masterCoa'])->name('coa');
        Route::post('/coa', [AccountingPengaturanController::class, 'storeCoa'])->name('coa.store');
        Route::post('/coa/import', [AccountingPengaturanController::class, 'importCoa'])->name('coa.import');
        Route::patch('/coa/{id}/saldo-awal', [AccountingPengaturanController::class, 'updateSaldoAwalCoa'])->name('coa.saldo-awal');
        Route::get('/mapping', [AccountingPengaturanController::class, 'accountMapping'])->name('mapping');
        Route::post('/mapping', [AccountingPengaturanController::class, 'updateAccountMapping'])->name('mapping.update');
        Route::get('/tutup-buku', [AccountingPengaturanController::class, 'tutupBuku'])->name('tutup-buku');
        Route::post('/tutup-buku', [AccountingPengaturanController::class, 'prosesTutupBuku'])->name('tutup-buku.proses');
        Route::post('/tutup-buku/buka', [AccountingPengaturanController::class, 'bukaTutupBuku'])->name('tutup-buku.buka');
        Route::get('/audit-log', [AccountingPengaturanController::class, 'auditLog'])->name('audit-log');
    });

    // Backward-compatibility aliases
    Route::get('/tarik/iklan', fn() => redirect()->route('accounting.iklan.hitung-penjualan'))->name('tarik.iklan');
    Route::post('/tarik/iklan/proses', fn() => redirect()->route('accounting.iklan.hitung-penjualan'))->name('tarik.iklan.proses');
    Route::get('/tarik/sirkulasi', fn() => redirect()->route('accounting.sirkulasi.hitung-penjualan'))->name('tarik.sirkulasi');
    Route::post('/tarik/sirkulasi/proses', fn() => redirect()->route('accounting.sirkulasi.hitung-penjualan'))->name('tarik.sirkulasi.proses');
    Route::get('/tarik/kasir', fn() => redirect()->route('accounting.beban.mutasi-bank'))->name('tarik.kasir');
    Route::post('/tarik/kasir/proses', fn() => redirect()->route('accounting.beban.mutasi-bank'))->name('tarik.kasir.proses');
    Route::get('/kartu/iklan', fn() => redirect()->route('accounting.cetak.iklan'))->name('kartu.iklan');
    Route::get('/kartu/sirkulasi', fn() => redirect()->route('accounting.cetak.sirkulasi'))->name('kartu.sirkulasi');
    Route::get('/kartu/retur', fn() => redirect()->route('accounting.sirkulasi.koreksi'))->name('kartu.retur');
    Route::post('/kartu/retur/store', fn() => redirect()->route('accounting.sirkulasi.koreksi'))->name('kartu.retur.store');
    Route::get('/gl/coa', fn() => redirect()->route('accounting.pengaturan.coa'))->name('gl.coa');
    Route::get('/gl', fn() => redirect()->route('accounting.gl.jurnal'))->name('gl.index');
    Route::get('/laporan/buku-besar', fn() => redirect()->route('accounting.gl.buku-besar'))->name('laporan.buku-besar');
});

// ==========================================
// 5. DIVISI SIRKULASI (Sistem DOS Asli SIRK.EXE)
// Akses: Role Sirkulasi, Akuntansi & Administrator
// ==========================================
Route::middleware(['auth', 'role:sirkulasi,accounting,administrator'])->prefix('sirkulasi')->name('sirkulasi.')->group(function () {
    Route::get('/dashboard', [SirkulasiDashboardController::class, 'index'])->name('dashboard');

    // 1. MENU TABEL (Master Data)
    Route::prefix('tabel')->name('tabel.')->group(function () {
        Route::get('/wilayah', [SirkulasiWilayahController::class, 'index'])->name('wilayah');
        Route::get('/pengepakan', [SirkulasiPengepakanController::class, 'index'])->name('pengepakan');
        Route::get('/jenis-agen', [SirkulasiJenisAgenController::class, 'index'])->name('jenis-agen');
        Route::get('/rute', [SirkulasiRuteController::class, 'index'])->name('rute');
        Route::post('/rute', [SirkulasiRuteController::class, 'store'])->name('rute.store');
        Route::put('/rute/{id}', [SirkulasiRuteController::class, 'update'])->name('rute.update');
        Route::delete('/rute/{id}', [SirkulasiRuteController::class, 'destroy'])->name('rute.destroy');
        Route::patch('/rute/{id}/toggle-status', [SirkulasiRuteController::class, 'toggleStatus'])->name('rute.toggle');
        Route::get('/agen', [SirkulasiAgenController::class, 'index'])->name('agen');
        Route::post('/agen', [SirkulasiAgenController::class, 'store'])->name('agen.store');
        Route::put('/agen/{id}', [SirkulasiAgenController::class, 'update'])->name('agen.update');
        Route::delete('/agen/{id}', [SirkulasiAgenController::class, 'destroy'])->name('agen.destroy');
        Route::patch('/agen/{id}/toggle-status', [SirkulasiAgenController::class, 'toggleStatus'])->name('agen.toggle');
    });

    // 2. MENU JATAH (Operasional H+1)
    Route::prefix('jatah')->name('jatah.')->group(function () {
        Route::get('/edit', [SirkulasiJatahEditController::class, 'index'])->name('edit');
        Route::post('/edit', [SirkulasiJatahEditController::class, 'store'])->name('edit.store');
        Route::get('/copy', [SirkulasiJatahCopyController::class, 'index'])->name('copy');
        Route::post('/copy', [SirkulasiJatahCopyController::class, 'process'])->name('copy.process');
        Route::get('/tutup', [SirkulasiJatahTutupController::class, 'index'])->name('tutup');
        Route::post('/tutup', [SirkulasiJatahTutupController::class, 'lock'])->name('tutup.lock');
        Route::post('/tutup/minta-buka', [SirkulasiJatahTutupController::class, 'requestUnlock'])->name('tutup.request-unlock');
        Route::post('/tutup/batal-minta-buka', [SirkulasiJatahTutupController::class, 'cancelRequestUnlock'])->name('tutup.cancel-request-unlock');
        Route::get('/memo/{id}/cetak', [SirkulasiJatahTutupController::class, 'cetakMemo'])->name('memo.cetak');
        Route::get('/tutup/harian', fn() => redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'harian']))->name('tutup.harian');
        Route::get('/tutup/bulanan', fn() => redirect()->route('sirkulasi.jatah.tutup', ['mode' => 'bulanan']))->name('tutup.bulanan');
        Route::get('/hitung', [SirkulasiJatahHitungController::class, 'index'])->name('hitung');
        Route::post('/hitung', [SirkulasiJatahHitungController::class, 'process'])->name('hitung.process');

        // Aliases untuk backward compatibility jika diperlukan
        Route::get('/harian', fn() => redirect()->route('sirkulasi.jatah.edit'))->name('harian');
        Route::get('/quick-edit', fn() => redirect()->route('sirkulasi.jatah.edit'))->name('quick-edit');
    });

    // 3. MENU CETAK (Laporan & Distribusi)
    Route::prefix('cetak')->name('cetak.')->group(function () {
        Route::get('/pemasaran', [SirkulasiLaporanPemasaranController::class, 'index'])->name('pemasaran');
        Route::get('/pemasaran/cetak', [SirkulasiLaporanPemasaranController::class, 'print'])->name('pemasaran.print');
        Route::get('/penjualan', [SirkulasiLaporanPenjualanController::class, 'index'])->name('penjualan');
        Route::get('/faktur/{kode_agen?}', [SirkulasiLaporanPenjualanController::class, 'faktur'])->name('faktur');
        Route::get('/nota-distribusi', [SirkulasiNotaDistribusiController::class, 'index'])->name('nota-distribusi');
        Route::get('/alamat-agen', [SirkulasiAlamatAgenController::class, 'index'])->name('alamat-agen');
    });

    // Riwayat Memo Buka Data Sirkulasi
    Route::get('/memo/history', [SirkulasiMemoHistoryController::class, 'index'])->name('memo.history');

    // 4. MENU PENGATURAN TARIF & OPERASIONAL SIRKULASI
    Route::get('/pengaturan', [SirkulasiPengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('/pengaturan', [SirkulasiPengaturanController::class, 'update'])->name('pengaturan.update');
    Route::post('/pengaturan/cutoff', [SirkulasiPengaturanController::class, 'updateCutoffTime'])->name('pengaturan.update-cutoff');

    // Backward compatibility aliases untuk master data & laporan lama
    Route::get('/master/agen', fn() => redirect()->route('sirkulasi.tabel.agen'))->name('master.agen');
    Route::get('/master/rute', fn() => redirect()->route('sirkulasi.tabel.rute'))->name('master.rute');
    Route::get('/master/kalender', fn() => redirect()->route('sirkulasi.tabel.wilayah'))->name('master.kalender');
    Route::get('/master/tarif', fn() => redirect()->route('sirkulasi.tabel.jenis-agen'))->name('master.tarif');
    Route::get('/master/pelanggan', fn() => redirect()->route('sirkulasi.tabel.agen'))->name('master.pelanggan');
    Route::get('/logistik/surat-jalan', fn() => redirect()->route('sirkulasi.cetak.nota-distribusi'))->name('logistik.surat-jalan');
    Route::get('/logistik/retur', fn() => redirect()->route('sirkulasi.cetak.penjualan'))->name('logistik.retur');
    Route::get('/logistik/order-cetak', fn() => redirect()->route('sirkulasi.cetak.pemasaran'))->name('logistik.order-cetak');
    Route::get('/laporan/closing', fn() => redirect()->route('sirkulasi.jatah.tutup'))->name('laporan.closing');
    Route::get('/laporan/pemasaran', fn() => redirect()->route('sirkulasi.cetak.pemasaran'))->name('laporan.pemasaran');
    Route::get('/laporan/penjualan', fn() => redirect()->route('sirkulasi.cetak.penjualan'))->name('laporan.penjualan');
});

// ==========================================
// 6. DIVISI KASIR
// Akses: Role Kasir, Keuangan, Admin Keuangan, Accounting & Administrator
// ==========================================
Route::middleware(['auth', 'role:kasir,admin,administrator,keuangan,admin_keuangan,accounting'])->group(function () {
    // 1. Dashboard Meja Kasir & Buka/Tutup Shift
    Route::get('/kasir/dashboard', [KasirController::class, 'dashboard'])->name('kasir.dashboard');
    Route::post('/kasir/buka-shift', [KasirController::class, 'bukaShift'])->name('kasir.buka-shift');
    Route::post('/kasir/tutup-kasir', [KasirController::class, 'tutupKasir'])->name('kasir.tutup-kasir');

    // Grup Transaksi Harian (Dilindungi Anti-Bypass Kunci Shift Gantung: kasir.shift)
    Route::middleware(['kasir.shift'])->group(function () {
        // 2. Transaksi Kas Masuk (KI - Cash In)
        Route::get('/kasir/penerimaan/iklan', [KasirController::class, 'penerimaanIklan'])->name('kasir.penerimaan.iklan');
        Route::post('/kasir/penerimaan/iklan', [KasirController::class, 'storePenerimaanIklan'])->name('kasir.penerimaan.iklan.store');

        Route::get('/kasir/penerimaan/sirkulasi', [KasirController::class, 'penerimaanSirkulasi'])->name('kasir.penerimaan.sirkulasi');
        Route::post('/kasir/penerimaan/sirkulasi', [KasirController::class, 'storePenerimaanSirkulasi'])->name('kasir.penerimaan.sirkulasi.store');

        // Verifikasi & Otorisasi Setoran Inkaso Lapangan (Scan Barcode BSK)
        Route::get('/kasir/penerimaan/inkaso', [KasirController::class, 'penerimaanInkaso'])->name('kasir.penerimaan.inkaso');
        Route::post('/kasir/penerimaan/inkaso/{id}/approve', [KasirController::class, 'approveSetoranInkaso'])->name('kasir.penerimaan.inkaso.approve');
        Route::post('/kasir/penerimaan/inkaso/{id}/reject', [KasirController::class, 'rejectSetoranInkaso'])->name('kasir.penerimaan.inkaso.reject');
        Route::get('/kasir/api/scan-bsk/{token}', [KasirController::class, 'scanBskApi'])->name('kasir.api.scan-bsk');

        // Koran Sisa Cetak (Pendapatan Lain-lain KI-14)
        Route::get('/kasir/penerimaan/koran-sisa', [KasirController::class, 'penerimaanKoranSisa'])->name('kasir.penerimaan.koran-sisa');
        Route::post('/kasir/penerimaan/koran-sisa', [KasirController::class, 'storePenerimaanKoranSisa'])->name('kasir.penerimaan.koran-sisa.store');
        Route::get('/kasir/penerimaan/tunai', [KasirController::class, 'penerimaanTunai'])->name('kasir.penerimaan.tunai');
        Route::post('/kasir/penerimaan/tunai', [KasirController::class, 'storePenerimaanTunai'])->name('kasir.penerimaan.tunai.store');

        Route::get('/kasir/penerimaan/bank', [KasirController::class, 'penerimaanBank'])->name('kasir.penerimaan.bank');
        Route::post('/kasir/penerimaan/bank', [KasirController::class, 'storePenerimaanBank'])->name('kasir.penerimaan.bank.store');

        Route::get('/kasir/penerimaan/langganan', [KasirController::class, 'penerimaanLangganan'])->name('kasir.penerimaan.langganan');
        Route::post('/kasir/penerimaan/langganan', [KasirController::class, 'storePenerimaanLangganan'])->name('kasir.penerimaan.langganan.store');

        Route::get('/kasir/verifikasi-bilyet', [KasirController::class, 'verifikasiBilyet'])->name('kasir.verifikasi-bilyet');
        Route::post('/kasir/verifikasi-bilyet', [KasirController::class, 'storeVerifikasiBilyet'])->name('kasir.verifikasi-bilyet.store');

        // 3. Transaksi Kas Keluar (KO - Cash Out)
        Route::get('/kasir/pengeluaran/realisasi-bp', [KasirController::class, 'realisasiBp'])->name('kasir.pengeluaran.realisasi-bp');
        Route::post('/kasir/pengeluaran/realisasi-bp', [KasirController::class, 'storeRealisasiBp'])->name('kasir.pengeluaran.realisasi-bp.store');
        Route::post('/kasir/pengeluaran/realisasi-bp/{id}', [KasirController::class, 'storeRealisasiBp'])->name('kasir.pengeluaran.realisasi-bp.store-id');
        Route::post('/kasir/pengeluaran/realisasi-bp/{id}/tolak', [KasirController::class, 'tolakRealisasiBp'])->name('kasir.pengeluaran.realisasi-bp.tolak');

        Route::get('/kasir/voucher', [KasirController::class, 'voucherPengeluaran'])->name('kasir.voucher');
        Route::post('/kasir/voucher', [KasirController::class, 'storeVoucherPengeluaran'])->name('kasir.voucher.store');

        Route::get('/kasir/pengeluaran/komisi', [KasirController::class, 'pengeluaranKomisi'])->name('kasir.pengeluaran.komisi');
        Route::post('/kasir/pengeluaran/komisi', [KasirController::class, 'storePengeluaranKomisi'])->name('kasir.pengeluaran.komisi.store');

        // Kasbon / Uang Muka Meja Kasir Operasional (SOP Kas Kecil: BS, BKKC, LKK & KI-09)
        Route::get('/kasir/kasbon', [KasirController::class, 'pengeluaranKasbon'])->name('kasir.kasbon');
        Route::post('/kasir/kasbon', [KasirController::class, 'storePengeluaranKasbon'])->name('kasir.kasbon.store');
        Route::get('/kasir/pengeluaran/kasbon', [KasirController::class, 'pengeluaranKasbon'])->name('kasir.pengeluaran.kasbon');
        Route::post('/kasir/pengeluaran/kasbon', [KasirController::class, 'storePengeluaranKasbon'])->name('kasir.pengeluaran.kasbon.store');
        Route::post('/kasir/pengeluaran/kasbon/{id}/acc', [KasirController::class, 'accKasbon'])->name('kasir.pengeluaran.kasbon.acc');
        Route::post('/kasir/pengeluaran/kasbon/{id}/tolak', [KasirController::class, 'tolakKasbon'])->name('kasir.pengeluaran.kasbon.tolak');
        Route::post('/kasir/pengeluaran/kasbon/{id}/selesai', [KasirController::class, 'selesaikanKasbon'])->name('kasir.pengeluaran.kasbon.selesai');
        Route::post('/kasir/pengeluaran/kasbon/bkkc', [KasirController::class, 'storeBkkc'])->name('kasir.pengeluaran.kasbon.bkkc.store');
        Route::post('/kasir/pengeluaran/kasbon/lkk', [KasirController::class, 'storeLkk'])->name('kasir.pengeluaran.kasbon.lkk.store');

        Route::get('/kasir/setoran-bank', [KasirController::class, 'setoranBank'])->name('kasir.setoran-bank');
        Route::post('/kasir/setoran-bank', [KasirController::class, 'storeSetoranBank'])->name('kasir.setoran-bank.store');
    });

    // Endpoint Cetak Dokumen Kuitansi (KM) & Bukti Kas Keluar (BKK) Terproteksi Anti-IDOR
    Route::get('/kasir/penerimaan/cetak/{id}', [KasirController::class, 'cetakPenerimaan'])->name('kasir.penerimaan.cetak');
    Route::get('/kasir/pengeluaran/cetak-bkk/{id}', [KasirController::class, 'cetakBkk'])->name('kasir.pengeluaran.cetak-bkk');

    // API Barcode Scanner Kasir (Scan Cepat Faktur Iklan & Agen Sirkulasi)
    Route::get('/kasir/api/scan-iklan', [KasirController::class, 'scanIklan'])->name('kasir.api.scan-iklan');
    Route::get('/kasir/api/scan-sirkulasi', [KasirController::class, 'scanSirkulasi'])->name('kasir.api.scan-sirkulasi');

    // 4. Laporan & Mutasi Kas (Menu Cetak KAS)
    Route::get('/kasir/laporan/rekap', [KasirController::class, 'laporanRekap'])->name('kasir.laporan.rekap');
    Route::get('/kasir/laporan/rekap/export', [KasirController::class, 'exportLaporanRekap'])->name('kasir.laporan.rekap.export');
    Route::get('/kasir/laporan/mutasi-bank', [KasirController::class, 'laporanMutasiBank'])->name('kasir.laporan.mutasi-bank');
    Route::get('/kasir/laporan/perkiraan', [KasirController::class, 'laporanPerkiraan'])->name('kasir.laporan.perkiraan');
    Route::get('/kasir/laporan/posisi-kas', [KasirController::class, 'laporanPosisiKas'])->name('kasir.laporan.posisi-kas');
    Route::get('/kasir/laporan/kasbon', [KasirController::class, 'laporanKasbon'])->name('kasir.laporan.kasbon');
    Route::get('/kasir/laporan-harian', [KasirController::class, 'laporanHarian'])->name('kasir.laporan-harian');

    // 5. Fungsi & Utilitas Kontrol
    Route::get('/kasir/utilitas/closing', [KasirController::class, 'utilitasClosing'])->name('kasir.utilitas.closing');
    Route::post('/kasir/utilitas/closing', [KasirController::class, 'storeUtilitasClosing'])->name('kasir.utilitas.closing.store');
    Route::get('/kasir/buku-kas', [KasirController::class, 'bukuKas'])->name('kasir.buku-kas');
    Route::post('/kasir/buku-kas/tutup', [KasirController::class, 'tutupKasir'])->name('kasir.buku-kas.tutup');

    Route::get('/kasir/utilitas/koreksi', [KasirController::class, 'utilitasKoreksi'])->middleware('kasir.shift')->name('kasir.utilitas.koreksi');
    Route::post('/kasir/utilitas/koreksi/{id}', [KasirController::class, 'voidTransaksi'])->middleware('kasir.shift')->name('kasir.utilitas.koreksi.store');
    Route::post('/kasir/utilitas/koreksi/{id}/void', [KasirController::class, 'voidTransaksi'])->middleware('kasir.shift')->name('kasir.utilitas.koreksi.void');
    Route::post('/kasir/utilitas/koreksi/{id}/update', [KasirController::class, 'updateTransaksi'])->middleware('kasir.shift')->name('kasir.utilitas.koreksi.update');
    Route::match(['post', 'put'], '/kasir/utilitas/koreksi/{id}/edit', [KasirController::class, 'updateTransaksi'])->middleware('kasir.shift')->name('kasir.utilitas.koreksi.edit');

    Route::get('/kasir/utilitas/cetak-ulang', [KasirController::class, 'utilitasCetakUlang'])->name('kasir.utilitas.cetak-ulang');
    Route::get('/kasir/riwayat-cetak', [KasirController::class, 'riwayatCetak'])->name('kasir.riwayat-cetak');

    // 6. Pembukaan Kembali Kas (Reopen Closed Shift - Perizinan Manager Keuangan)
    Route::get('/kasir/utilitas/buka-kas', [KasirController::class, 'bukaKasIndex'])->name('kasir.utilitas.buka-kas');
    Route::post('/kasir/utilitas/buka-kas/{id}/ajukan', [KasirController::class, 'ajukanBukaKembaliKas'])->name('kasir.utilitas.buka-kas.ajukan');
    Route::post('/kasir/utilitas/buka-kas/{id}/setujui', [KasirController::class, 'setujuiBukaKembaliKas'])->name('kasir.utilitas.buka-kas.setujui');
    Route::post('/kasir/utilitas/buka-kas/{id}/tolak', [KasirController::class, 'tolakBukaKembaliKas'])->name('kasir.utilitas.buka-kas.tolak');

    // 7. Riwayat Memo Buka Kas & Cetak Memo Berkop
    Route::get('/kasir/memo/history', [KasirController::class, 'memoHistory'])->name('kasir.memo.history');
    Route::get('/kasir/memo/{id}/cetak', [KasirController::class, 'cetakMemo'])->name('kasir.memo.cetak');
});

// Profil Pengguna (Semua Role)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
