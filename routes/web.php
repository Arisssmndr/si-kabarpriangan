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
use App\Http\Controllers\SistemConfigController;
use App\Http\Controllers\SppiController;
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

    // Master Pelanggan (CRM Periklanan)
    Route::resource('pelanggan', PelangganController::class);
    Route::get('/api/pelanggan/search', [PelangganController::class, 'search'])->name('pelanggan.search');

    // Transaksi Iklan Koran
    Route::resource('transaksikoran', TransaksiKoranController::class);
    Route::get('/transaksikoran/{id}/cetak', [TransaksiKoranController::class, 'cetak'])->name('transaksikoran.cetak');
    Route::get('/transaksikoran/{id}/sppi', [SppiController::class, 'cetak'])->name('transaksikoran.sppi');
    Route::post('/sppi/{id}/konfirmasi-ttd', [SppiController::class, 'konfirmasiTtd'])->name('sppi.konfirmasi-ttd');

    // Pencatatan Pembayaran & Kwitansi Kasir Digital
    Route::get('/pembayaran-iklan', [PembayaranIklanController::class, 'index'])->name('pembayaran-iklan.index');
    Route::post('/pembayaran-iklan', [PembayaranIklanController::class, 'store'])->name('pembayaran-iklan.store');
    Route::get('/pembayaran-iklan/{id}/kwitansi', [PembayaranIklanController::class, 'kwitansi'])->name('pembayaran-iklan.kwitansi');

    // Konfigurasi Tarif, PPN, dan Operasional Periklanan
    Route::get('/iklan/pengaturan', [SistemConfigController::class, 'index'])->name('iklan.pengaturan.index');
    Route::post('/iklan/pengaturan', [SistemConfigController::class, 'update'])->name('iklan.pengaturan.update');

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
// Akses: Role Keuangan, Admin Keuangan, Kasir, Petugas Inkaso, Kepala Divisi, & Administrator
// ==========================================
Route::middleware(['auth', 'role:keuangan,admin_keuangan,kasir,petugas_inkaso,kepala_divisi,administrator'])->prefix('keuangan')->name('keuangan.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'keuangan'])->name('dashboard');
    Route::get('/alur-faktur-inkaso', [DashboardController::class, 'alurFakturInkaso'])->name('alur');

    // A. Modul Faktur & Piutang
    Route::prefix('faktur')->name('faktur.')->group(function () {
        Route::get('/', [FakturController::class, 'index'])->name('index');
        Route::get('/create', [FakturController::class, 'create'])->name('create');
        Route::post('/', [FakturController::class, 'store'])->name('store');
        Route::get('/{id}', [FakturController::class, 'show'])->name('show');
        Route::get('/{id}/cetak', [FakturController::class, 'cetak'])->name('cetak');
    });

    // B. Modul Penagihan Lapangan (Inkaso)
    Route::prefix('inkaso')->name('inkaso.')->group(function () {
        Route::get('/', [InkasoController::class, 'index'])->name('index');
        Route::post('/assign', [InkasoController::class, 'assign'])->name('assign');
        Route::post('/setoran', [InkasoController::class, 'storeSetoran'])->name('setoran.store');
        Route::get('/surat-tugas/{id}/cetak', [InkasoController::class, 'cetakSuratTugas'])->name('surat-tugas.cetak');
        Route::get('/kwitansi/{id}/cetak', [InkasoController::class, 'kwitansi'])->name('kwitansi.cetak');
        Route::get('/rekap/cetak', [KasController::class, 'cetakRekapInkaso'])->name('rekap.cetak');
    });

    // C. Modul Pengajuan Anggaran (Cash Out)
    Route::prefix('anggaran')->name('anggaran.')->group(function () {
        Route::get('/', [AnggaranController::class, 'index'])->name('index');
        Route::get('/create', [AnggaranController::class, 'create'])->name('create');
        Route::post('/', [AnggaranController::class, 'store'])->name('store');
        Route::post('/{id}/approve', [AnggaranController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [AnggaranController::class, 'reject'])->name('reject');
        Route::post('/{id}/cairkan', [AnggaranController::class, 'cairkan'])->name('cairkan');
        Route::get('/{id}/voucher', [AnggaranController::class, 'cetakVoucher'])->name('voucher');
    });

    // D. Modul Buku Kas (Treasury)
    Route::prefix('kas')->name('kas.')->group(function () {
        Route::get('/', [KasController::class, 'index'])->name('index');
        Route::get('/harian', [KasController::class, 'harian'])->name('harian');
        Route::get('/bulanan', [KasController::class, 'bulanan'])->name('bulanan');
        Route::get('/cetak', [KasController::class, 'cetak'])->name('cetak');
    });

    // E. Modul Laporan Keuangan Terpadu (3 Tab: Harian, Bulanan, Inkaso)
    Route::get('/laporan', [KasController::class, 'laporan'])->name('laporan');
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
