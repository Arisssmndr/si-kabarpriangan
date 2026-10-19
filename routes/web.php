<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenisIklanController;
use App\Http\Controllers\KategoriMediaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransaksiIklanPrianganController;
use App\Http\Controllers\TransaksiKoranController;
use App\Http\Controllers\TransaksiOnlineController;
use App\Http\Controllers\Keuangan\FakturController;
use App\Http\Controllers\Keuangan\InkasoController;
use App\Http\Controllers\Keuangan\AnggaranController;
use App\Http\Controllers\Keuangan\KasController;
use Illuminate\Support\Facades\Route;

// Halaman Depan
Route::get('/', function () {
    return view('welcome');
});

// Central Dispatcher Dashboard: Mengarahkan otomatis ke dashboard divisi pengguna yang login
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ==========================================
// 1. DASHBOARD ADMINISTRATOR (Superadmin)
// ==========================================
Route::middleware(['auth', 'role:administrator'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
});

// ==========================================
// 2. DIVISI IKLAN (Koran, Online, Priangan TV)
// Akses: Role Iklan & Administrator
// ==========================================
Route::middleware(['auth', 'role:iklan'])->group(function () {
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

    // Transaksi Iklan Koran
    Route::resource('transaksikoran', TransaksiKoranController::class);
    Route::get('/transaksikoran/{id}/cetak', [TransaksiKoranController::class, 'cetak'])->name('transaksikoran.cetak');

    // Transaksi Iklan Online
    Route::resource('transaksionline', TransaksiOnlineController::class);
    Route::get('/transaksionline/{id}/cetak', [TransaksiOnlineController::class, 'cetak'])->name('transaksionline.cetak');

    // Transaksi Iklan Priangan TV
    Route::resource('transaksipriangan', TransaksiIklanPrianganController::class);
});

// ==========================================
// RUTE BERSAMA: DIVISI IKLAN & KEUANGAN
// Akses: Role Iklan, Keuangan (Manajer Keuangan), Faktur & Administrator
// ==========================================
Route::middleware(['auth', 'role:iklan,keuangan,admin_keuangan,faktur'])->group(function () {
    // Cetak Dokumen Faktur
    Route::get('/transaksikoran/{id}/cetak', [TransaksiKoranController::class, 'cetak'])->name('transaksikoran.cetak');
    Route::get('/transaksionline/{id}/cetak', [TransaksiOnlineController::class, 'cetak'])->name('transaksionline.cetak');
    Route::get('/transaksipriangan/{id}/cetak', [TransaksiIklanPrianganController::class, 'cetak'])->name('transaksipriangan.cetak');

    // Laporan Keuangan Iklan Terpadu
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');

    // Pengalihan Rute Laporan Lama
    Route::match(['get', 'post'], '/laporankoran', fn() => redirect()->route('laporan.index', ['media' => 'koran']))->name('laporankoran.index');
    Route::match(['get', 'post'], '/laporanonline', fn() => redirect()->route('laporan.index', ['media' => 'online']))->name('laporanonline.index');
    Route::match(['get', 'post'], '/laporanpriangan', fn() => redirect()->route('laporan.index', ['media' => 'tv']))->name('laporanpriangan.index');
});

// ==========================================
// 3. DIVISI KEUANGAN
// Akses: Role Keuangan (Manajer Keuangan), Faktur, Anggaran, Admin Keuangan, Kasir, Petugas Inkaso, Kepala Divisi, & Administrator
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

    // D. Modul Buku Kas (Treasury): Akses Manajer Keuangan, Admin Keuangan, Kasir & Administrator
    Route::middleware('role:keuangan,admin_keuangan,kasir,administrator')->prefix('kas')->name('kas.')->group(function () {
        Route::get('/', [KasController::class, 'index'])->name('index');
        Route::get('/harian', [KasController::class, 'harian'])->name('harian');
        Route::get('/bulanan', [KasController::class, 'bulanan'])->name('bulanan');
        Route::get('/cetak', [KasController::class, 'cetak'])->name('cetak');
    });

    // E. Modul Laporan Keuangan Terpadu: Akses Manajer Keuangan, Admin Keuangan, Kasir & Administrator
    Route::middleware('role:keuangan,admin_keuangan,kasir,administrator')->group(function () {
        Route::get('/laporan', [KasController::class, 'laporan'])->name('laporan');
    });
});

// ==========================================
// 4. DIVISI ACCOUNTING
// Akses: Role Accounting & Administrator
// ==========================================
Route::middleware(['auth', 'role:accounting'])->group(function () {
    Route::get('/accounting/dashboard', [DashboardController::class, 'accounting'])->name('accounting.dashboard');
});

// ==========================================
// 5. DIVISI SIRKULASI
// Akses: Role Sirkulasi & Administrator
// ==========================================
Route::middleware(['auth', 'role:sirkulasi'])->group(function () {
    Route::get('/sirkulasi/dashboard', [DashboardController::class, 'sirkulasi'])->name('sirkulasi.dashboard');
});

// ==========================================
// 6. DIVISI KASIR
// Akses: Role Kasir & Administrator
// ==========================================
Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/kasir/dashboard', [DashboardController::class, 'kasir'])->name('kasir.dashboard');
});

// Profil Pengguna (Semua Role)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
