<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenisIklanController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\KategoriMediaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SirkulasiController;
use App\Http\Controllers\TransaksiIklanPrianganController;
use App\Http\Controllers\TransaksiKoranController;
use App\Http\Controllers\TransaksiOnlineController;
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

    // Transaksi Iklan Koran
    Route::resource('transaksikoran', TransaksiKoranController::class);
    Route::get('/transaksikoran/{id}/cetak', [TransaksiKoranController::class, 'cetak'])->name('transaksikoran.cetak');

    // Transaksi Iklan Online
    Route::resource('transaksionline', TransaksiOnlineController::class);
    Route::get('/transaksionline/{id}/cetak', [TransaksiOnlineController::class, 'cetak'])->name('transaksionline.cetak');

    // Transaksi Iklan Priangan TV
    Route::resource('transaksipriangan', TransaksiIklanPrianganController::class);
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
// Akses: Role Keuangan & Administrator
// ==========================================
Route::middleware(['auth', 'role:keuangan'])->group(function () {
    Route::get('/keuangan/dashboard', [DashboardController::class, 'keuangan'])->name('keuangan.dashboard');
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
// 5. DIVISI SIRKULASI
// Akses: Role Sirkulasi & Administrator
// ==========================================
Route::middleware(['auth', 'role:sirkulasi'])->prefix('sirkulasi')->name('sirkulasi.')->group(function () {
    Route::get('/dashboard', [SirkulasiController::class, 'dashboard'])->name('dashboard');

    // Master Data Sirkulasi
    Route::prefix('master')->name('master.')->group(function () {
        Route::get('/agen', [SirkulasiController::class, 'masterAgen'])->name('agen');
        Route::post('/agen', [SirkulasiController::class, 'storeAgen'])->name('agen.store');
        Route::patch('/agen/{id}/toggle-status', [SirkulasiController::class, 'toggleStatusAgen'])->name('agen.toggle');
        Route::get('/rute', [SirkulasiController::class, 'masterRute'])->name('rute');
        Route::get('/kalender', [SirkulasiController::class, 'masterKalender'])->name('kalender');
        Route::get('/tarif', [SirkulasiController::class, 'masterTarif'])->name('tarif');
        Route::get('/pelanggan', [SirkulasiController::class, 'pelanggan'])->name('pelanggan');
    });

    // Operasional Jatah Koran
    Route::prefix('jatah')->name('jatah.')->group(function () {
        Route::get('/harian', [SirkulasiController::class, 'jatahHarian'])->name('harian');
        Route::post('/harian', [SirkulasiController::class, 'storeJatahHarian'])->name('harian.store');
        Route::get('/copy', [SirkulasiController::class, 'copyJatah'])->name('copy');
        Route::post('/copy', [SirkulasiController::class, 'processCopyJatah'])->name('copy.process');
        Route::get('/quick-edit', [SirkulasiController::class, 'quickEditJatah'])->name('quick-edit');
        Route::post('/quick-edit', [SirkulasiController::class, 'updateQuickEditJatah'])->name('quick-edit.update');
    });

    // Logistik & Pengiriman
    Route::prefix('logistik')->name('logistik.')->group(function () {
        Route::get('/surat-jalan', [SirkulasiController::class, 'suratJalan'])->name('surat-jalan');
        Route::get('/retur', [SirkulasiController::class, 'returBapr'])->name('retur');
        Route::post('/retur', [SirkulasiController::class, 'storeReturBapr'])->name('retur.store');
        Route::get('/order-cetak', [SirkulasiController::class, 'orderCetak'])->name('order-cetak');
    });

    // Tutup Data & Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/closing', [SirkulasiController::class, 'closingBulanan'])->name('closing');
        Route::post('/closing/lock', [SirkulasiController::class, 'lockDataBulanan'])->name('closing.lock');
        Route::get('/pemasaran', [SirkulasiController::class, 'rekapPemasaran'])->name('pemasaran');
        Route::get('/penjualan', [SirkulasiController::class, 'rekapPenjualan'])->name('penjualan');
    });
});

// ==========================================
// 6. DIVISI KASIR
// Akses: Role Kasir & Administrator
// ==========================================
Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/kasir/dashboard', [KasirController::class, 'dashboard'])->name('kasir.dashboard');
    
    // Setoran Iklan [Badge: Faktur]
    Route::get('/kasir/penerimaan/iklan', [KasirController::class, 'penerimaanIklan'])->name('kasir.penerimaan.iklan');
    Route::post('/kasir/penerimaan/iklan', [KasirController::class, 'storePenerimaanIklan'])->name('kasir.penerimaan.iklan.store');

    // Setoran Sirkulasi [Badge: Koran]
    Route::get('/kasir/penerimaan/sirkulasi', [KasirController::class, 'penerimaanSirkulasi'])->name('kasir.penerimaan.sirkulasi');
    Route::post('/kasir/penerimaan/sirkulasi', [KasirController::class, 'storePenerimaanSirkulasi'])->name('kasir.penerimaan.sirkulasi.store');

    // Penjualan Langsung
    Route::get('/kasir/penerimaan/tunai', [KasirController::class, 'penerimaanTunai'])->name('kasir.penerimaan.tunai');
    Route::post('/kasir/penerimaan/tunai', [KasirController::class, 'storePenerimaanTunai'])->name('kasir.penerimaan.tunai.store');

    // Realisasi BP [Badge: BKK]
    Route::get('/kasir/pengeluaran/realisasi-bp', [KasirController::class, 'realisasiBp'])->name('kasir.pengeluaran.realisasi-bp');
    Route::post('/kasir/pengeluaran/realisasi-bp', [KasirController::class, 'storeRealisasiBp'])->name('kasir.pengeluaran.realisasi-bp.store');

    // Buku Kas & Tutup [Badge: Shift]
    Route::get('/kasir/buku-kas', [KasirController::class, 'bukuKas'])->name('kasir.buku-kas');
    Route::post('/kasir/buku-kas/tutup', [KasirController::class, 'tutupKasir'])->name('kasir.buku-kas.tutup');

    // Koran Langganan Instansi / Dinas [Badge: Dinas]
    Route::get('/kasir/penerimaan/langganan', [KasirController::class, 'penerimaanLangganan'])->name('kasir.penerimaan.langganan');
    Route::post('/kasir/penerimaan/langganan', [KasirController::class, 'storePenerimaanLangganan'])->name('kasir.penerimaan.langganan.store');

    // Setoran Kas ke Bank [Badge: Bank]
    Route::get('/kasir/setoran-bank', [KasirController::class, 'setoranBank'])->name('kasir.setoran-bank');
    Route::post('/kasir/setoran-bank', [KasirController::class, 'storeSetoranBank'])->name('kasir.setoran-bank.store');

    // Riwayat & Cetak Bukti Kasir [Badge: Kwitansi]
    Route::get('/kasir/riwayat-cetak', [KasirController::class, 'riwayatCetak'])->name('kasir.riwayat-cetak');

    // Laporan Harian Kasir (LHK) [Badge: Rekap]
    Route::get('/kasir/laporan-harian', [KasirController::class, 'laporanHarian'])->name('kasir.laporan-harian');
});

// Profil Pengguna (Semua Role)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
