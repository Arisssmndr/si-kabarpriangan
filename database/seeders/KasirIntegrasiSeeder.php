<?php

namespace Database\Seeders;

use App\Models\Agen;
use App\Models\BuktiPengeluaran;
use App\Models\KasKeluar;
use App\Models\KasMasuk;
use App\Models\PengajuanAnggaran;
use App\Models\ShiftKasir;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class KasirIntegrasiSeeder extends Seeder
{
    public function run(): void
    {
        $kasirUser = User::where('role', 'kasir')->first() ?? User::first();
        $adminUser = User::whereIn('role', ['admin', 'administrator', 'admin_keuangan'])->first() ?? User::first();

        // 1. Seed Master Agen Sirkulasi jika kosong
        if (Agen::count() === 0) {
            $agenData = [
                [
                    'kode_agen' => 'AG-11-001',
                    'nama_agen' => 'Kios Koran H. Dadang (Pasar Wetan)',
                    'kategori' => '11',
                    'induk_agen' => null,
                    'rute_kode' => 'R-01',
                    'wilayah' => 'Kota Tasikmalaya',
                    'telepon' => '0812-2345-6789',
                    'jatah_default' => 450,
                    'plafon_piutang' => 15000000,
                    'saldo_piutang' => 3850000,
                    'status' => 'aktif',
                ],
                [
                    'kode_agen' => 'AG-11-9-011',
                    'nama_agen' => 'Loper Mamat Cihideung',
                    'kategori' => '11-9',
                    'induk_agen' => 'Kios Koran H. Dadang (AG-11-001)',
                    'rute_kode' => 'R-01',
                    'wilayah' => 'Kota Tasikmalaya',
                    'telepon' => '0852-9876-1234',
                    'jatah_default' => 80,
                    'plafon_piutang' => 2000000,
                    'saldo_piutang' => 640000,
                    'status' => 'aktif',
                ],
                [
                    'kode_agen' => 'AG-11-002',
                    'nama_agen' => 'Agen Berkah Ciamis Kota',
                    'kategori' => '11',
                    'induk_agen' => null,
                    'rute_kode' => 'R-03',
                    'wilayah' => 'Kab. Ciamis',
                    'telepon' => '0813-8899-7711',
                    'jatah_default' => 600,
                    'plafon_piutang' => 20000000,
                    'saldo_piutang' => 5200000,
                    'status' => 'aktif',
                ],
                [
                    'kode_agen' => 'AG-11-9-021',
                    'nama_agen' => 'Kios Alun-alun Kawali',
                    'kategori' => '11-9',
                    'induk_agen' => 'Agen Berkah Ciamis Kota (AG-11-002)',
                    'rute_kode' => 'R-03',
                    'wilayah' => 'Kab. Ciamis',
                    'telepon' => '0877-1122-3344',
                    'jatah_default' => 120,
                    'plafon_piutang' => 3000000,
                    'saldo_piutang' => 890000,
                    'status' => 'aktif',
                ],
                [
                    'kode_agen' => 'AG-11-003',
                    'nama_agen' => 'Agen Tarogong Prima Garut',
                    'kategori' => '11',
                    'induk_agen' => null,
                    'rute_kode' => 'R-06',
                    'wilayah' => 'Kab. Garut',
                    'telepon' => '0812-4455-6677',
                    'jatah_default' => 550,
                    'plafon_piutang' => 18000000,
                    'saldo_piutang' => 4750000,
                    'status' => 'aktif',
                ],
            ];

            foreach ($agenData as $a) {
                Agen::create($a);
            }
        }

        // 2. Seed Bukti Pengeluaran (BP) jika kosong
        if (BuktiPengeluaran::count() === 0) {
            $bpData = [
                [
                    'no_bp' => 'BP-2026-10-001',
                    'divisi' => 'iklan',
                    'tanggal_pengajuan' => Carbon::now()->subDays(2)->toDateString(),
                    'pemohon' => 'Ahmad Fauzi (AE Iklan)',
                    'keperluan' => 'Pencairan Komisi KH & Insentif AE Klien Pemkab Tasik',
                    'nominal_acc' => 1250000,
                    'status' => 'approved',
                    'approved_by' => $adminUser ? $adminUser->id : null,
                ],
                [
                    'no_bp' => 'BP-2026-10-002',
                    'divisi' => 'sirkulasi',
                    'tanggal_pengajuan' => Carbon::now()->subDay()->toDateString(),
                    'pemohon' => 'Randi Kurnia (Operasional Sirkulasi)',
                    'keperluan' => 'BBM & Tol Operasional Pengiriman Koran Luar Kota (Ciamis-Banjar)',
                    'nominal_acc' => 450000,
                    'status' => 'approved',
                    'approved_by' => $adminUser ? $adminUser->id : null,
                ],
                [
                    'no_bp' => 'BP-2026-10-003',
                    'divisi' => 'iklan',
                    'tanggal_pengajuan' => Carbon::now()->subDays(4)->toDateString(),
                    'pemohon' => 'Dedi Gunawan (Sales Executive)',
                    'keperluan' => 'BBM & Jamuan Klien Prospek PT Bank BJB',
                    'nominal_acc' => 300000,
                    'status' => 'approved',
                    'approved_by' => $adminUser ? $adminUser->id : null,
                ],
                [
                    'no_bp' => 'BP-2026-10-004',
                    'divisi' => 'redaksi',
                    'tanggal_pengajuan' => Carbon::now()->toDateString(),
                    'pemohon' => 'Rina Marlina (Redaksi / Kontributor)',
                    'keperluan' => 'Honorium Kontributor Tulisan Liputan Khusus Daerah',
                    'nominal_acc' => 750000,
                    'status' => 'pending',
                    'approved_by' => null,
                ],
            ];

            foreach ($bpData as $bp) {
                // Link dengan pengajuan anggaran jika nomornya cocok
                $pengajuan = PengajuanAnggaran::where('no_pengajuan', $bp['no_bp'])->first();
                if ($pengajuan) {
                    $bp['pengajuan_anggaran_id'] = $pengajuan->id;
                }
                BuktiPengeluaran::create($bp);
            }
        }

        // 3. Pastikan Shift Kasir Aktif Hari Ini Ada
        if ($kasirUser) {
            $shiftAktif = ShiftKasir::where('kasir_id', $kasirUser->id)
                ->where('status', 'aktif')
                ->whereDate('tgl_shift', Carbon::today())
                ->first();

            if (!$shiftAktif) {
                ShiftKasir::create([
                    'kasir_id' => $kasirUser->id,
                    'tgl_shift' => Carbon::today(),
                    'saldo_awal_fisik' => 1000000.00,
                    'total_masuk' => 0.00,
                    'total_keluar' => 0.00,
                    'saldo_akhir_fisik' => 1000000.00,
                    'selisih_kas' => 0.00,
                    'status' => 'aktif',
                ]);
            }
        }

        // 4. Seed Bukti Kas Masuk Sirkulasi Historis (September & Agustus) untuk verifikasi arsip & filter
        $historicalSirkulasi = [
            ['kode_agen' => 'AG-11-001', 'nama' => 'Kios Koran H. Dadang (Pasar Wetan)', 'no_kuitansi' => 'KI-02-2026090001', 'tgl' => '2026-09-28', 'tagihan' => 4200000, 'bayar' => 4200000, 'sisa' => 0, 'status' => 'lunas'],
            ['kode_agen' => 'AG-11-002', 'nama' => 'Agen Berkah Ciamis Kota', 'no_kuitansi' => 'KI-02-2026090002', 'tgl' => '2026-09-29', 'tagihan' => 5500000, 'bayar' => 5500000, 'sisa' => 0, 'status' => 'lunas'],
            ['kode_agen' => 'AG-11-9-011', 'nama' => 'Loper Mamat Cihideung', 'no_kuitansi' => 'KI-02-2026090003', 'tgl' => '2026-09-27', 'tagihan' => 750000, 'bayar' => 750000, 'sisa' => 0, 'status' => 'lunas'],
            ['kode_agen' => 'AG-11-003', 'nama' => 'Agen Tarogong Prima Garut', 'no_kuitansi' => 'KI-02-2026090004', 'tgl' => '2026-09-25', 'tagihan' => 4750000, 'bayar' => 2500000, 'sisa' => 2250000, 'status' => 'uang_muka'],
            ['kode_agen' => 'AG-11-9-021', 'nama' => 'Kios Alun-alun Kawali', 'no_kuitansi' => 'KI-02-2026090005', 'tgl' => '2026-09-26', 'tagihan' => 890000, 'bayar' => 500000, 'sisa' => 390000, 'status' => 'uang_muka'],
        ];

        foreach ($historicalSirkulasi as $hs) {
            $agen = Agen::where('kode_agen', $hs['kode_agen'])->first();
            if ($agen && !KasMasuk::where('no_kuitansi', $hs['no_kuitansi'])->exists()) {
                KasMasuk::create([
                    'no_kuitansi'           => $hs['no_kuitansi'],
                    'no_bkm'                => $hs['no_kuitansi'],
                    'shift_id'              => 1,
                    'tanggal_terima'        => $hs['tgl'],
                    'tgl_transaksi'         => $hs['tgl'],
                    'kode_bagian'           => '02',
                    'kode_perkiraan'        => $hs['sisa'] <= 0 ? '1.00.04.01.01.00' : '2.00.01.01.01.00',
                    'nama_perkiraan'        => $hs['sisa'] <= 0 ? 'Piutang Sirkulasi Koran Agen' : 'Uang Muka Sirkulasi',
                    'terima_dari'           => $hs['nama'],
                    'nama_pembayar'         => $hs['nama'],
                    'penagih_inkaso'        => 'Randi Kurnia (Inkaso Sirkulasi)',
                    'kode_agen_ref'         => $hs['kode_agen'],
                    'kode_agen_input'       => $hs['kode_agen'],
                    'tipe_transaksi'        => 'sirkulasi',
                    'ref_id'                => $agen->id,
                    'ref_type'              => 'agen_sirkulasi',
                    'metode_bayar'          => 'tunai',
                    'bank_tujuan'           => 'Bank BJB Sirkulasi',
                    'jumlah_tagihan'        => $hs['tagihan'],
                    'total_tagihan'         => $hs['tagihan'],
                    'potongan_pajak'        => 0,
                    'potongan_retur'        => 0,
                    'jumlah_retur_eks'      => 0,
                    'jumlah_diterima'       => $hs['bayar'],
                    'jumlah_dibayar'        => $hs['bayar'],
                    'sisa_piutang'          => $hs['sisa'],
                    'status_bayar'          => $hs['status'],
                    'status_pembayaran'     => $hs['sisa'] <= 0 ? 'lunas' : 'sebagian',
                    'keterangan'            => "Setoran Koran Agen {$hs['nama']} [{$hs['kode_agen']}] Periode September 2026",
                    'kasir_id'              => 1,
                    'user_id'               => 1,
                    'is_closed'             => true,
                    'is_pulled_accounting'  => true,
                    'status_tarik_akunting' => true,
                ]);
            }
        }
    }
}
