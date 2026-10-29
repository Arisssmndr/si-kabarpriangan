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
use App\Http\Controllers\KasirController;
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
// Akses: Role Keuangan (Manajer Keuangan), Faktur, Anggaran, Admin Keuangan, Kasir, Kepala Divisi, & Administrator
// ==========================================
Route::middleware(['auth', 'role:keuangan,admin_keuangan,faktur,anggaran,kasir,kepala_divisi,administrator'])->prefix('keuangan')->name('keuangan.')->group(function () {
    // Dashboard & SOP Alur: Manajer Keuangan, Admin Keuangan, Faktur, Anggaran & Administrator
    Route::middleware('role:keuangan,admin_keuangan,faktur,anggaran,administrator')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'keuangan'])->name('dashboard');
        Route::get('/alur-faktur-inkaso', [DashboardController::class, 'alurFakturInkaso'])->name('alur');
    });

    // A. Modul Faktur & Piutang: Akses Faktur, Manajer Keuangan, Admin Keuangan & Administrator
    Route::middleware('role:faktur,keuangan,admin_keuangan,administrator')->prefix('faktur')->name('faktur.')->group(function () {
        Route::get('/', [FakturController::class, 'index'])->name('index');
        Route::get('/cetak-rekap', [FakturController::class, 'cetakRekap'])->name('cetak-rekap');
        Route::get('/cari-iklan', [FakturController::class, 'cariFakturIklan'])->name('cari-iklan');
        Route::get('/create', [FakturController::class, 'create'])->name('create');
        Route::post('/', [FakturController::class, 'store'])->name('store');
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

    // B. Modul Pengajuan Anggaran (Cash Out)
    Route::prefix('anggaran')->name('anggaran.')->group(function () {
        // Akses Melihat & Mengajukan: Anggaran, Manajer Keuangan, Admin Keuangan, Kasir & Administrator
        Route::middleware('role:anggaran,keuangan,admin_keuangan,kasir,administrator')->group(function () {
            // Tabel Master DOS ANGGARAN.EXE
            Route::get('/tabel/bidang', [AnggaranController::class, 'tabelBidang'])->name('tabel.bidang');
            Route::get('/tabel/perkiraan', [AnggaranController::class, 'tabelPerkiraan'])->name('tabel.perkiraan');
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

