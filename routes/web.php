<?php

use App\Http\Controllers\AccountingController;
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
use App\Http\Controllers\SirkulasiController;
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

    // Standar Industri: Laporan Eksekutif Konsolidasi Lintas Divisi (Executive Report Center)
    Route::get('admin/laporan', [\App\Http\Controllers\Admin\LaporanEksekutifController::class, 'index'])->name('admin.laporan.index');
    Route::get('admin/laporan/cetak', [\App\Http\Controllers\Admin\LaporanEksekutifController::class, 'cetak'])->name('admin.laporan.cetak');

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
    Route::get('/api/kalender-nasional', [\App\Http\Controllers\KalenderNasionalController::class, 'getBulan'])->name('api.kalender-nasional');

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
    Route::post('/transaksionline/{id}/bukti-tayang', [TransaksiOnlineController::class, 'simpanBuktiTayang'])->name('transaksionline.bukti-tayang');
    Route::post('/transaksionline/{id}/status-produksi', [TransaksiOnlineController::class, 'updateStatusProduksi'])->name('transaksionline.status-produksi');
    Route::post('/transaksionline/{id}/batalkan', [TransaksiOnlineController::class, 'batalkan'])->name('transaksionline.batalkan');

    // Transaksi Iklan Priangan TV
    Route::resource('transaksipriangan', TransaksiIklanPrianganController::class);
    Route::get('/transaksipriangan/{id}/cetak', [TransaksiIklanPrianganController::class, 'cetak'])->name('transaksipriangan.cetak');
    Route::post('/transaksipriangan/{id}/batalkan', [TransaksiIklanPrianganController::class, 'batalkan'])->name('transaksipriangan.batalkan');

    // Laporan Keuangan Iklan Terpadu & Standar Pers IKL.EXE
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');
    Route::get('/laporan/rekap-sales', [LaporanController::class, 'rekapSales'])->name('laporan.rekap-sales');
    Route::get('/laporan/insentif-ae', [LaporanController::class, 'insentifAe'])->name('laporan.insentif-ae');
    Route::get('/laporan/klasifikasi', [LaporanController::class, 'klasifikasi'])->name('laporan.klasifikasi');
    Route::get('/laporan/pembatalan', [LaporanController::class, 'pembatalan'])->name('laporan.pembatalan');
    Route::get('/laporan/aging-piutang', [LaporanController::class, 'agingPiutang'])->name('laporan.aging-piutang');

    // Pengalihan Rute Laporan Lama
    Route::match(['get', 'post'], '/laporankoran', fn() => redirect()->route('laporan.index', ['media' => 'koran']))->name('laporankoran.index');
    Route::match(['get', 'post'], '/laporanonline', fn() => redirect()->route('laporan.index', ['media' => 'online']))->name('laporanonline.index');
    Route::match(['get', 'post'], '/laporanpriangan', fn() => redirect()->route('laporan.index', ['media' => 'tv']))->name('laporanpriangan.index');
});

// ==========================================
// 3. DIVISI FAKTUR, ANGGARAN & KEUANGAN
// Akses: Role Keuangan, Faktur, Anggaran, Admin Keuangan, Kasir, Petugas Inkaso, Kepala Divisi, & Administrator
// ==========================================
Route::middleware(['auth', 'role:keuangan,admin_keuangan,faktur,anggaran,kasir,petugas_inkaso,kepala_divisi,administrator'])->prefix('keuangan')->name('keuangan.')->group(function () {
    // Dashboard & SOP Alur: Manajer Keuangan, Admin Keuangan, Faktur, Anggaran & Administrator
    Route::middleware('role:keuangan,admin_keuangan,faktur,anggaran,administrator')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'keuangan'])->name('dashboard');
        Route::get('/alur-faktur-inkaso', [DashboardController::class, 'alurFakturInkaso'])->name('alur');
    });

    // A. Modul Faktur & Piutang: Akses Faktur, Manajer Keuangan, Admin Keuangan & Administrator
    Route::middleware('role:faktur,keuangan,admin_keuangan,administrator')->prefix('faktur')->name('faktur.')->group(function () {
        Route::get('/', [FakturController::class, 'index'])->name('index');

        // 1. Modul Faktur Iklan
        Route::prefix('iklan')->name('iklan.')->group(function () {
            Route::get('/', [FakturController::class, 'iklanKeseluruhan'])->name('index');
            Route::get('/susulan', [FakturController::class, 'iklanSusulan'])->name('susulan');
            Route::get('/rekap', [FakturController::class, 'rekapIklan'])->name('rekap');
            Route::get('/rekap/cetak', [FakturController::class, 'cetakRekapIklan'])->name('rekap.cetak');
            Route::get('/tunai', [FakturController::class, 'iklanTunai'])->name('tunai');
            Route::get('/piutang', [FakturController::class, 'iklanPiutang'])->name('piutang');
            Route::get('/piutang/cetak', [FakturController::class, 'cetakRekapIklanPiutang'])->name('piutang.cetak');
            Route::get('/stopper-bonus', [FakturController::class, 'iklanStopperBonus'])->name('stopper-bonus');
            Route::get('/stok-opname', [FakturController::class, 'stokOpname'])->name('stok-opname');
            Route::post('/stok-opname', [FakturController::class, 'storeStokOpname'])->name('stok-opname.store');
            Route::get('/stok-opname/cetak', [FakturController::class, 'cetakStokOpname'])->name('stok-opname.cetak');
        });

        // 2. Modul Faktur Sirkulasi
        Route::prefix('sirkulasi')->name('sirkulasi.')->group(function () {
            Route::get('/', [FakturController::class, 'sirkulasiIndex'])->name('index');
            Route::get('/susulan', [FakturController::class, 'sirkulasiSusulan'])->name('susulan');
            Route::get('/rekap', [FakturController::class, 'sirkulasiRekap'])->name('rekap');
            Route::get('/rekap/cetak', [FakturController::class, 'cetakRekapSirkulasi'])->name('rekap.cetak');
            Route::get('/insidentil', [FakturController::class, 'sirkulasiInsidentil'])->name('insidentil');
        });

        // 3. Modul Fungsi & Utilitas Faktur
        Route::prefix('fungsi')->name('fungsi.')->group(function () {
            Route::get('/', [FakturController::class, 'fungsiIndex'])->name('index');
            Route::post('/sync-transaksi', [FakturController::class, 'syncTransaksi'])->name('sync');
        });

        // CRUD Penerbitan & Cetak
        Route::get('/cari-iklan', [FakturController::class, 'cariFakturIklan'])->name('cari-iklan');
        Route::get('/create', [FakturController::class, 'create'])->name('create');
        Route::post('/', [FakturController::class, 'store'])->name('store');
        Route::get('/{id}', [FakturController::class, 'show'])->name('show');
        Route::get('/{id}/cetak', [FakturController::class, 'cetak'])->name('cetak');
    });

    // B. Modul Penagihan Lapangan (Inkaso): Akses Faktur, Manajer Keuangan, Admin Keuangan, Petugas Inkaso & Administrator
    Route::middleware('role:faktur,keuangan,admin_keuangan,petugas_inkaso,administrator')->prefix('inkaso')->name('inkaso.')->group(function () {
        Route::get('/', [InkasoController::class, 'index'])->name('index');
        Route::post('/assign', [InkasoController::class, 'assign'])->name('assign');
        Route::post('/setoran', [InkasoController::class, 'storeSetoran'])->name('setoran.store');
        Route::get('/surat-tugas/{id}/cetak', [InkasoController::class, 'cetakSuratTugas'])->name('surat-tugas.cetak');
        Route::get('/kwitansi/{id}/cetak', [InkasoController::class, 'kwitansi'])->name('kwitansi.cetak');
        Route::get('/rekap/cetak', [KasController::class, 'cetakRekapInkaso'])->name('rekap.cetak');
    });

    // C. Modul Pengajuan Anggaran (Cash Out)
    Route::prefix('anggaran')->name('anggaran.')->group(function () {
        // Akses Melihat & Mengajukan: Anggaran, Manajer Keuangan, Admin Keuangan, Kasir & Administrator
        Route::middleware('role:anggaran,keuangan,admin_keuangan,kasir,administrator')->group(function () {
            Route::get('/', [AnggaranController::class, 'index'])->name('index');
            Route::get('/create', [AnggaranController::class, 'create'])->name('create');
            Route::post('/', [AnggaranController::class, 'store'])->name('store');
            Route::get('/{id}/voucher', [AnggaranController::class, 'cetakVoucher'])->name('voucher');
        });

        // Akses Persetujuan (Approve/Reject): Khusus Manajer Keuangan, Admin Keuangan & Administrator
        Route::middleware('role:keuangan,admin_keuangan,administrator')->group(function () {
            Route::post('/{id}/approve', [AnggaranController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [AnggaranController::class, 'reject'])->name('reject');
        });

        // Akses Pencairan Kasir: Kasir, Manajer Keuangan & Administrator
        Route::middleware('role:kasir,keuangan,admin_keuangan,administrator')->group(function () {
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
Route::middleware(['auth', 'role:accounting'])->prefix('accounting')->name('accounting.')->group(function () {
    Route::get('/dashboard', [AccountingController::class, 'dashboard'])->name('dashboard');

    // Tarik & Validasi Data
    Route::prefix('tarik')->name('tarik.')->group(function () {
        Route::get('/iklan', [AccountingController::class, 'tarikIklan'])->name('iklan');
        Route::post('/iklan', [AccountingController::class, 'prosesTarikIklan'])->name('iklan.proses');
        Route::get('/sirkulasi', [AccountingController::class, 'tarikSirkulasi'])->name('sirkulasi');
        Route::post('/sirkulasi', [AccountingController::class, 'prosesTarikSirkulasi'])->name('sirkulasi.proses');
        Route::get('/kasir', [AccountingController::class, 'tarikKasir'])->name('kasir');
        Route::post('/kasir', [AccountingController::class, 'prosesTarikKasir'])->name('kasir.proses');
    });

    // Kartu Sub-Ledger (Kartu Piutang)
    Route::prefix('kartu')->name('kartu.')->group(function () {
        Route::get('/iklan', [AccountingController::class, 'kartuIklan'])->name('iklan');
        Route::get('/sirkulasi', [AccountingController::class, 'kartuSirkulasi'])->name('sirkulasi');
        Route::get('/retur', [AccountingController::class, 'returBapr'])->name('retur');
        Route::post('/retur', [AccountingController::class, 'storeReturBapr'])->name('retur.store');
    });

    // Buku Besar & Jurnal
    Route::prefix('gl')->name('gl.')->group(function () {
        Route::get('/', [AccountingController::class, 'generalLedger'])->name('index');
        Route::get('/coa', [AccountingController::class, 'masterCoa'])->name('coa');
    });

    // Laporan Keuangan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/laba-rugi', [AccountingController::class, 'labaRugi'])->name('laba-rugi');
        Route::get('/neraca', [AccountingController::class, 'neraca'])->name('neraca');
        Route::get('/buku-besar', [AccountingController::class, 'bukuBesar'])->name('buku-besar');
    });
});

// ==========================================
// 5. DIVISI SIRKULASI (Sistem DOS Asli SIRK.EXE)
// Akses: Role Sirkulasi & Administrator
// ==========================================
Route::middleware(['auth', 'role:sirkulasi'])->prefix('sirkulasi')->name('sirkulasi.')->group(function () {
    Route::get('/dashboard', [SirkulasiController::class, 'dashboard'])->name('dashboard');

    // 1. MENU TABEL (Master Data)
    Route::prefix('tabel')->name('tabel.')->group(function () {
        Route::get('/wilayah', [SirkulasiController::class, 'tabelWilayah'])->name('wilayah');
        Route::get('/pengepakan', [SirkulasiController::class, 'tabelPengepakan'])->name('pengepakan');
        Route::get('/jenis-agen', [SirkulasiController::class, 'tabelJenisAgen'])->name('jenis-agen');
        Route::get('/agen', [SirkulasiController::class, 'tabelAgen'])->name('agen');
        Route::post('/agen', [SirkulasiController::class, 'storeAgen'])->name('agen.store');
        Route::patch('/agen/{id}/toggle-status', [SirkulasiController::class, 'toggleStatusAgen'])->name('agen.toggle');
    });

    // 2. MENU JATAH (Operasional H+1)
    Route::prefix('jatah')->name('jatah.')->group(function () {
        Route::get('/edit', [SirkulasiController::class, 'jatahEdit'])->name('edit');
        Route::post('/edit', [SirkulasiController::class, 'storeJatahEdit'])->name('edit.store');
        Route::get('/copy', [SirkulasiController::class, 'copyJatah'])->name('copy');
        Route::post('/copy', [SirkulasiController::class, 'processCopyJatah'])->name('copy.process');
        Route::get('/tutup', [SirkulasiController::class, 'jatahTutup'])->name('tutup');
        Route::post('/tutup', [SirkulasiController::class, 'lockJatahTutup'])->name('tutup.lock');
        Route::get('/hitung', [SirkulasiController::class, 'jatahHitung'])->name('hitung');
        Route::post('/hitung', [SirkulasiController::class, 'processJatahHitung'])->name('hitung.process');

        // Aliases untuk backward compatibility jika diperlukan
        Route::get('/harian', fn() => redirect()->route('sirkulasi.jatah.edit'))->name('harian');
        Route::get('/quick-edit', fn() => redirect()->route('sirkulasi.jatah.edit'))->name('quick-edit');
    });

    // 3. MENU CETAK (Laporan & Distribusi)
    Route::prefix('cetak')->name('cetak.')->group(function () {
        Route::get('/pemasaran', [SirkulasiController::class, 'cetakPemasaran'])->name('pemasaran');
        Route::get('/penjualan', [SirkulasiController::class, 'cetakPenjualan'])->name('penjualan');
        Route::get('/nota-distribusi', [SirkulasiController::class, 'cetakNotaDistribusi'])->name('nota-distribusi');
        Route::get('/alamat-agen', [SirkulasiController::class, 'cetakAlamatAgen'])->name('alamat-agen');
    });

    // Backward compatibility aliases untuk master data & laporan lama
    Route::get('/master/agen', fn() => redirect()->route('sirkulasi.tabel.agen'))->name('master.agen');
    Route::get('/master/rute', fn() => redirect()->route('sirkulasi.tabel.agen'))->name('master.rute');
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
// Akses: Role Kasir & Administrator
// ==========================================
Route::middleware(['auth', 'role:kasir'])->group(function () {
    // 1. Dashboard Meja Kasir
    Route::get('/kasir/dashboard', [KasirController::class, 'dashboard'])->name('kasir.dashboard');
    
    // 2. Transaksi Kas Masuk (KI - Cash In)
    Route::get('/kasir/penerimaan/iklan', [KasirController::class, 'penerimaanIklan'])->name('kasir.penerimaan.iklan');
    Route::post('/kasir/penerimaan/iklan', [KasirController::class, 'storePenerimaanIklan'])->name('kasir.penerimaan.iklan.store');

    Route::get('/kasir/penerimaan/sirkulasi', [KasirController::class, 'penerimaanSirkulasi'])->name('kasir.penerimaan.sirkulasi');
    Route::post('/kasir/penerimaan/sirkulasi', [KasirController::class, 'storePenerimaanSirkulasi'])->name('kasir.penerimaan.sirkulasi.store');

    Route::get('/kasir/penerimaan/tunai', [KasirController::class, 'penerimaanTunai'])->name('kasir.penerimaan.tunai');
    Route::post('/kasir/penerimaan/tunai', [KasirController::class, 'storePenerimaanTunai'])->name('kasir.penerimaan.tunai.store');

    Route::get('/kasir/penerimaan/bank', [KasirController::class, 'penerimaanBank'])->name('kasir.penerimaan.bank');
    Route::post('/kasir/penerimaan/bank', [KasirController::class, 'storePenerimaanBank'])->name('kasir.penerimaan.bank.store');

    Route::get('/kasir/penerimaan/langganan', [KasirController::class, 'penerimaanLangganan'])->name('kasir.penerimaan.langganan');
    Route::post('/kasir/penerimaan/langganan', [KasirController::class, 'storePenerimaanLangganan'])->name('kasir.penerimaan.langganan.store');

    // 3. Transaksi Kas Keluar (KO - Cash Out)
    Route::get('/kasir/pengeluaran/realisasi-bp', [KasirController::class, 'realisasiBp'])->name('kasir.pengeluaran.realisasi-bp');
    Route::post('/kasir/pengeluaran/realisasi-bp', [KasirController::class, 'storeRealisasiBp'])->name('kasir.pengeluaran.realisasi-bp.store');

    Route::get('/kasir/pengeluaran/komisi', [KasirController::class, 'pengeluaranKomisi'])->name('kasir.pengeluaran.komisi');
    Route::post('/kasir/pengeluaran/komisi', [KasirController::class, 'storePengeluaranKomisi'])->name('kasir.pengeluaran.komisi.store');

    Route::get('/kasir/setoran-bank', [KasirController::class, 'setoranBank'])->name('kasir.setoran-bank');
    Route::post('/kasir/setoran-bank', [KasirController::class, 'storeSetoranBank'])->name('kasir.setoran-bank.store');

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

    Route::get('/kasir/utilitas/koreksi', [KasirController::class, 'utilitasKoreksi'])->name('kasir.utilitas.koreksi');
    Route::post('/kasir/utilitas/koreksi/{id}/void', [KasirController::class, 'voidTransaksi'])->name('kasir.utilitas.koreksi.void');

    Route::get('/kasir/utilitas/cetak-ulang', [KasirController::class, 'utilitasCetakUlang'])->name('kasir.utilitas.cetak-ulang');
    Route::get('/kasir/riwayat-cetak', [KasirController::class, 'riwayatCetak'])->name('kasir.riwayat-cetak');
});

// Profil Pengguna (Semua Role)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
