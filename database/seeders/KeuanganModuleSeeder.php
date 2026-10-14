<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Faktur;
use App\Models\KasTransaksi;
use App\Models\PembayaranFaktur;
use App\Models\PengajuanAnggaran;
use App\Models\PenugasanInkaso;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KeuanganModuleSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = Hash::make('password');

        // 1. Pastikan User dengan Role Terkait Keuangan Tersedia
        $users = [
            [
                'email' => 'admin.keuangan@kabarpriangan.co.id',
                'name'  => 'Admin Keuangan Kabar Priangan',
                'role'  => 'admin_keuangan',
            ],
            [
                'email' => 'kasir.keuangan@kabarpriangan.co.id',
                'name'  => 'Kasir Operasional Keuangan',
                'role'  => 'kasir',
            ],
            [
                'email' => 'ahmad.inkaso@kabarpriangan.co.id',
                'name'  => 'Ahmad Fauzi (Inkaso Lapangan)',
                'role'  => 'petugas_inkaso',
            ],
            [
                'email' => 'rudi.inkaso@kabarpriangan.co.id',
                'name'  => 'Rudi Hermawan (Inkaso Lapangan)',
                'role'  => 'petugas_inkaso',
            ],
            [
                'email' => 'dadan.inkaso@kabarpriangan.co.id',
                'name'  => 'Dadan Ramdani (Inkaso Lapangan)',
                'role'  => 'petugas_inkaso',
            ],
            [
                'email' => 'kepala.divisi@kabarpriangan.co.id',
                'name'  => 'H. Asep Ridwan, S.E. (Kepala Divisi)',
                'role'  => 'kepala_divisi',
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name'     => $u['name'],
                    'password' => $defaultPassword,
                    'role'     => $u['role'],
                ]
            );
        }

        $adminUser = User::where('role', 'admin_keuangan')->first() ?? User::first();
        $kasirUser = User::where('role', 'kasir')->first() ?? $adminUser;
        $petugasAhmad = User::where('email', 'ahmad.inkaso@kabarpriangan.co.id')->first() ?? $adminUser;
        $petugasRudi = User::where('email', 'rudi.inkaso@kabarpriangan.co.id')->first() ?? $adminUser;
        $kepalaDivisi = User::where('role', 'kepala_divisi')->first() ?? $adminUser;

        // 2. Data Master Customer
        $customersData = [
            // Iklan
            [
                'nama_customer' => 'PT Bank BJB Kantor Cabang Tasikmalaya',
                'no_telepon'    => '0265-331200',
                'alamat'        => 'Jl. Mayor Utarya No. 1, Kota Tasikmalaya',
                'tipe'          => 'iklan',
            ],
            [
                'nama_customer' => 'Mayasari Plaza Management',
                'no_telepon'    => '0265-342890',
                'alamat'        => 'Jl. Pasar Wetan No. 126, Kota Tasikmalaya',
                'tipe'          => 'iklan',
            ],
            [
                'nama_customer' => 'Universitas Siliwangi (UNSIL)',
                'no_telepon'    => '0265-330634',
                'alamat'        => 'Jl. Siliwangi No. 24, Kahuripan, Kota Tasikmalaya',
                'tipe'          => 'iklan',
            ],
            [
                'nama_customer' => 'RSUD dr. Soekardjo Tasikmalaya',
                'no_telepon'    => '0265-331683',
                'alamat'        => 'Jl. Rumah Sakit No. 33, Kota Tasikmalaya',
                'tipe'          => 'iklan',
            ],
            // Sirkulasi
            [
                'nama_customer' => 'Agen Koran Utama Cihideung',
                'no_telepon'    => '0812-2345-6789',
                'alamat'        => 'Komp. Ruko Pasar Cihideung Blok B-4, Kota Tasikmalaya',
                'tipe'          => 'sirkulasi',
            ],
            [
                'nama_customer' => 'Kios Koran Simpang Lima (Pak Dadang)',
                'no_telepon'    => '0852-9876-5432',
                'alamat'        => 'Jl. Ir. H. Juanda No. 88, Simpang Lima Tasikmalaya',
                'tipe'          => 'sirkulasi',
            ],
            [
                'nama_customer' => 'Sub Agen Sirkulasi Ciawi & Rajapolah',
                'no_telepon'    => '0821-3456-7890',
                'alamat'        => 'Jl. Raya Ciawi No. 15, Kec. Ciawi, Kab. Tasikmalaya',
                'tipe'          => 'sirkulasi',
            ],
            [
                'nama_customer' => 'Agen Koran Harian Banjar Patroman',
                'no_telepon'    => '0813-1122-3344',
                'alamat'        => 'Jl. Kantor Pos No. 7, Kota Banjar',
                'tipe'          => 'sirkulasi',
            ],
        ];

        $customers = [];
        foreach ($customersData as $cd) {
            $customers[] = Customer::firstOrCreate(
                ['nama_customer' => $cd['nama_customer']],
                $cd
            );
        }

        // 3. Set Saldo Awal Buku Kas jika belum ada
        if (KasTransaksi::count() === 0) {
            KasTransaksi::create([
                'no_transaksi'   => 'KM-' . date('Ymd') . '-0001',
                'tanggal'        => Carbon::now()->subDays(10)->toDateString(),
                'jenis'          => 'masuk',
                'kategori_kas'   => 'Saldo Awal Kas',
                'nominal'        => 50000000.00,
                'keterangan'     => 'Saldo awal pembukaan kas operasional Kabar Priangan',
                'saldo_akhir'    => 50000000.00,
                'created_by'     => $adminUser->id,
            ]);
        }

        // 4. Sample Faktur
        // A. Faktur Lunas Langsung (Iklan)
        if (Faktur::where('no_faktur', 'INV-IKL-202610-001')->doesntExist()) {
            $c1 = $customers[0]; // BJB
            $faktur1 = Faktur::create([
                'no_faktur'      => 'INV-IKL-202610-001',
                'customer_id'    => $c1->id,
                'kategori'       => 'iklan',
                'tanggal_faktur' => Carbon::now()->subDays(5)->toDateString(),
                'jatuh_tempo'    => Carbon::now()->addDays(25)->toDateString(),
                'rincian_biaya'  => [
                    ['item' => 'Iklan Display 1 Halaman Berwarna (Pilkada)', 'qty' => 1, 'harga' => 12500000, 'subtotal' => 12500000],
                ],
                'total_tagihan'  => 12500000.00,
                'total_terbayar' => 12500000.00,
                'sisa_piutang'   => 0.00,
                'status'         => 'lunas',
                'created_by'     => $adminUser->id,
                'catatan'        => 'Pembayaran lunas transfer 100% saat pemesanan.',
            ]);

            $bayar1 = PembayaranFaktur::create([
                'no_kwitansi'         => 'KW-202610-0001',
                'faktur_id'           => $faktur1->id,
                'penugasan_inkaso_id' => null,
                'tanggal_bayar'       => Carbon::now()->subDays(5)->toDateString(),
                'nominal_bayar'       => 12500000.00,
                'metode_bayar'        => 'transfer',
                'tipe_bayar'          => 'lunas_langsung',
                'diterima_oleh'       => $kasirUser->id,
                'catatan'             => 'Pelunasan transfer Bank BJB',
            ]);

            KasTransaksi::catatMutasi(
                'masuk',
                'Pelunasan Faktur Iklan',
                12500000.00,
                'Pelunasan langsung Faktur ' . $faktur1->no_faktur . ' (' . $c1->nama_customer . ')',
                $bayar1,
                $kasirUser->id,
                Carbon::now()->subDays(5)->toDateString()
            );
        }

        // B. Faktur Cicilan / Tempo dengan DP (Iklan)
        if (Faktur::where('no_faktur', 'INV-IKL-202610-002')->doesntExist()) {
            $c2 = $customers[1]; // Mayasari
            $faktur2 = Faktur::create([
                'no_faktur'      => 'INV-IKL-202610-002',
                'customer_id'    => $c2->id,
                'kategori'       => 'iklan',
                'tanggal_faktur' => Carbon::now()->subDays(3)->toDateString(),
                'jatuh_tempo'    => Carbon::now()->addDays(14)->toDateString(),
                'rincian_biaya'  => [
                    ['item' => 'Iklan Banner Event Expo Kuliner 7 Hari', 'qty' => 7, 'harga' => 1000000, 'subtotal' => 7000000],
                ],
                'total_tagihan'  => 7000000.00,
                'total_terbayar' => 3000000.00,
                'sisa_piutang'   => 4000000.00,
                'status'         => 'cicilan',
                'created_by'     => $adminUser->id,
                'catatan'        => 'DP Rp 3.000.000, sisa Rp 4.000.000 tempo 14 hari.',
            ]);

            // DP masuk kas
            $bayarDp = PembayaranFaktur::create([
                'no_kwitansi'         => 'KW-202610-0002',
                'faktur_id'           => $faktur2->id,
                'penugasan_inkaso_id' => null,
                'tanggal_bayar'       => Carbon::now()->subDays(3)->toDateString(),
                'nominal_bayar'       => 3000000.00,
                'metode_bayar'        => 'tunai',
                'tipe_bayar'          => 'cicilan_inkaso',
                'diterima_oleh'       => $kasirUser->id,
                'catatan'             => 'Uang Muka (DP) pemesanan iklan',
            ]);

            KasTransaksi::catatMutasi(
                'masuk',
                'Uang Muka (DP) Faktur Iklan',
                3000000.00,
                'Pembayaran DP Faktur ' . $faktur2->no_faktur . ' (' . $c2->nama_customer . ')',
                $bayarDp,
                $kasirUser->id,
                Carbon::now()->subDays(3)->toDateString()
            );

            // Masuk Penugasan Inkaso
            PenugasanInkaso::create([
                'no_tugas'      => 'TGS-202610-001',
                'faktur_id'     => $faktur2->id,
                'petugas_id'    => $petugasAhmad->id,
                'tanggal_tugas' => Carbon::now()->toDateString(),
                'status_tugas'  => 'berjalan',
                'catatan'       => 'Tagihkan sisa pelunasan Rp 4.000.000 ke Bagian Promosi Mayasari Plaza.',
            ]);
        }

        // C. Faktur Sirkulasi Belum Bayar (Belum Ada DP, Antrean Inkaso)
        if (Faktur::where('no_faktur', 'INV-SRK-202610-001')->doesntExist()) {
            $c3 = $customers[4]; // Agen Cihideung
            $faktur3 = Faktur::create([
                'no_faktur'      => 'INV-SRK-202610-001',
                'customer_id'    => $c3->id,
                'kategori'       => 'sirkulasi',
                'tanggal_faktur' => Carbon::now()->subDays(2)->toDateString(),
                'jatuh_tempo'    => Carbon::now()->addDays(7)->toDateString(),
                'rincian_biaya'  => [
                    ['item' => 'Oplah Koran Harian Kabar Priangan (500 eks x 7 hari)', 'qty' => 3500, 'harga' => 2000, 'subtotal' => 7000000],
                ],
                'total_tagihan'  => 7000000.00,
                'total_terbayar' => 0.00,
                'sisa_piutang'   => 7000000.00,
                'status'         => 'belum_bayar',
                'created_by'     => $adminUser->id,
                'catatan'        => 'Tagihan mingguan agen koran Cihideung.',
            ]);

            // Assign ke Rudi
            PenugasanInkaso::create([
                'no_tugas'      => 'TGS-202610-002',
                'faktur_id'     => $faktur3->id,
                'petugas_id'    => $petugasRudi->id,
                'tanggal_tugas' => Carbon::now()->toDateString(),
                'status_tugas'  => 'berjalan',
                'catatan'       => 'Penagihan rutin omset koran mingguan.',
            ]);
        }

        // D. Sample Pengajuan Anggaran
        if (PengajuanAnggaran::count() === 0) {
            // 1. Pengajuan Cair (Redaksi)
            $anggaranCair = PengajuanAnggaran::create([
                'no_pengajuan'      => 'ANG-RDK-202610-001',
                'divisi'            => 'redaksi',
                'tanggal_pengajuan' => Carbon::now()->subDays(4)->toDateString(),
                'nominal_diajukan'  => 3500000.00,
                'nominal_disetujui' => 3500000.00,
                'status'            => 'cair',
                'approved_by'       => $kepalaDivisi->id,
                'cair_by'           => $kasirUser->id,
                'tanggal_cair'      => Carbon::now()->subDays(3)->toDateString(),
                'keterangan'        => 'Biaya akomodasi dan peliputan khusus liputan investigasi Pilkada Tasikmalaya.',
                'created_by'        => $adminUser->id,
            ]);

            // Mutasi Kas Keluar
            KasTransaksi::catatMutasi(
                'keluar',
                'Pencairan Anggaran Divisi Redaksi',
                3500000.00,
                'Pencairan dana anggaran No. ' . $anggaranCair->no_pengajuan . ' (Redaksi)',
                $anggaranCair,
                $kasirUser->id,
                Carbon::now()->subDays(3)->toDateString()
            );

            // 2. Pengajuan Approved (Sirkulasi)
            PengajuanAnggaran::create([
                'no_pengajuan'      => 'ANG-SRK-202610-002',
                'divisi'            => 'sirkulasi',
                'tanggal_pengajuan' => Carbon::now()->subDays(2)->toDateString(),
                'nominal_diajukan'  => 2200000.00,
                'nominal_disetujui' => 2000000.00,
                'status'            => 'approved',
                'approved_by'       => $kepalaDivisi->id,
                'cair_by'           => null,
                'tanggal_cair'      => null,
                'keterangan'        => 'BBM dan biaya servis motor armada distribusi koran wilayah selatan.',
                'created_by'        => $adminUser->id,
            ]);

            // 3. Pengajuan Pending (Sekum)
            PengajuanAnggaran::create([
                'no_pengajuan'      => 'ANG-SKM-202610-003',
                'divisi'            => 'sekum',
                'tanggal_pengajuan' => Carbon::now()->subDay()->toDateString(),
                'nominal_diajukan'  => 1850000.00,
                'nominal_disetujui' => null,
                'status'            => 'pending',
                'approved_by'       => null,
                'cair_by'           => null,
                'tanggal_cair'      => null,
                'keterangan'        => 'Pengadaan ATK, kertas cetak nota faktur, dan tinta printer divisi administrasi.',
                'created_by'        => $adminUser->id,
            ]);
        }
    }
}
