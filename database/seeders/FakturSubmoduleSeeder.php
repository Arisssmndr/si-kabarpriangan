<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Faktur;
use App\Models\StokOpnameFaktur;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FakturSubmoduleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'administrator')->orWhere('role', 'admin')->first() ?? User::first();
        $fakturUser = User::where('role', 'faktur')->first() ?? $admin;

        // Pastikan customer iklan & sirkulasi tersedia
        $custIklan1 = Customer::firstOrCreate(
            ['nama_customer' => 'PT Bank Pembangunan Daerah Jawa Barat dan Banten (BJB) Tasikmalaya'],
            ['no_telepon' => '0265-331234', 'alamat' => 'Jl. Mayor Utarya No. 12, Tasikmalaya', 'tipe' => 'iklan']
        );

        $custIklan2 = Customer::firstOrCreate(
            ['nama_customer' => 'Sekretariat DPRD Kota Tasikmalaya'],
            ['no_telepon' => '0265-334567', 'alamat' => 'Jl. RE Martadinata No. 345, Tasikmalaya', 'tipe' => 'iklan']
        );

        $custIklan3 = Customer::firstOrCreate(
            ['nama_customer' => 'Asia Plaza Tasikmalaya (PT Asia Sanprima Jaya)'],
            ['no_telepon' => '0265-235000', 'alamat' => 'Jl. KHZ Mustofa No. 326, Tasikmalaya', 'tipe' => 'iklan']
        );

        $custSirkulasi = Customer::firstOrCreate(
            ['nama_customer' => 'Agen Koran Utama Cihideung'],
            ['no_telepon' => '0812-2345-6789', 'alamat' => 'Komp. Ruko Pasar Cihideung Blok B-4, Kota Tasikmalaya', 'tipe' => 'sirkulasi']
        );

        // Update faktur iklan yang belum ada sales_nama
        $aeNames = ['Ahmad Fauzi', 'Rina Marlina', 'Dedi Gunawan'];
        $fakturs = Faktur::where('kategori', 'iklan')->whereNull('sales_nama')->get();
        foreach ($fakturs as $idx => $f) {
            $f->update(['sales_nama' => $aeNames[$idx % count($aeNames)]]);
        }

        // 1. Faktur Iklan Reguler (Edisi Khusus)
        Faktur::updateOrCreate(
            ['no_faktur' => 'INV-IKL-202610-008'],
            [
                'customer_id'    => $custIklan3->id,
                'sales_nama'     => 'Ahmad Fauzi',
                'kategori'       => 'iklan',
                'tipe_faktur'    => 'reguler',
                'tanggal_faktur' => Carbon::now()->subDays(2)->toDateString(),
                'jatuh_tempo'    => Carbon::now()->addDays(14)->toDateString(),
                'rincian_biaya'  => [
                    ['item' => 'Iklan Halaman 1 Berwarna - Ucapan Selamat Pembukaan Gerai Baru', 'qty' => 1, 'harga' => 4500000, 'subtotal' => 4500000],
                ],
                'total_tagihan'  => 4500000,
                'total_terbayar' => 0,
                'sisa_piutang'   => 4500000,
                'status'         => 'belum_bayar',
                'created_by'     => $fakturUser->id,
                'catatan'        => 'Materi iklan terbit edisi khusus akhir pekan',
            ]
        );

        // 2. Faktur Iklan Stopper
        Faktur::updateOrCreate(
            ['no_faktur' => 'INV-IKL-STP-202610-001'],
            [
                'customer_id'    => $custIklan2->id,
                'sales_nama'     => 'Dedi Gunawan',
                'kategori'       => 'iklan',
                'tipe_faktur'    => 'stopper',
                'tanggal_faktur' => Carbon::now()->subDays(3)->toDateString(),
                'jatuh_tempo'    => Carbon::now()->toDateString(),
                'rincian_biaya'  => [
                    ['item' => 'Iklan Stopper Halaman 5 (Pengisi Ruang Redaksional)', 'qty' => 1, 'harga' => 0, 'subtotal' => 0],
                ],
                'total_tagihan'  => 0,
                'total_terbayar' => 0,
                'sisa_piutang'   => 0,
                'status'         => 'lunas',
                'created_by'     => $fakturUser->id,
                'catatan'        => 'Iklan stopper pengisi ruang edisi cetak harian Kabar Priangan',
            ]
        );

        // 3. Faktur Iklan Bonus / Barter Promo
        Faktur::updateOrCreate(
            ['no_faktur' => 'INV-IKL-BNS-202610-001'],
            [
                'customer_id'    => $custIklan1->id,
                'sales_nama'     => 'Rina Marlina',
                'kategori'       => 'iklan',
                'tipe_faktur'    => 'bonus',
                'tanggal_faktur' => Carbon::now()->subDays(5)->toDateString(),
                'jatuh_tempo'    => Carbon::now()->toDateString(),
                'rincian_biaya'  => [
                    ['item' => 'Iklan Bonus Tayang Kerjasama MOU Bank BJB (1/4 Halaman)', 'qty' => 1, 'harga' => 0, 'subtotal' => 0],
                ],
                'total_tagihan'  => 0,
                'total_terbayar' => 0,
                'sisa_piutang'   => 0,
                'status'         => 'lunas',
                'created_by'     => $fakturUser->id,
                'catatan'        => 'Bonus pemasangan paket kontrak tahunan 2026',
            ]
        );

        // 4. Faktur Sirkulasi Sample (Reguler)
        Faktur::updateOrCreate(
            ['no_faktur' => 'INV-SRK-202610-001'],
            [
                'customer_id'    => $custSirkulasi->id,
                'sales_nama'     => null,
                'kategori'       => 'sirkulasi',
                'tipe_faktur'    => 'reguler',
                'tanggal_faktur' => Carbon::now()->subDays(4)->toDateString(),
                'jatuh_tempo'    => Carbon::now()->addDays(10)->toDateString(),
                'rincian_biaya'  => [
                    ['item' => 'Distribusi Koran Edisi 01 - 10 Oktober 2026 (700 Eks @ Rp 4.000)', 'qty' => 700, 'harga' => 4000, 'subtotal' => 2800000],
                ],
                'total_tagihan'  => 2800000,
                'total_terbayar' => 1000000,
                'sisa_piutang'   => 1800000,
                'status'         => 'cicilan',
                'created_by'     => $fakturUser->id,
                'catatan'        => 'Titipan setoran pertama Rp 1.000.000',
            ]
        );

        // 4.A. Faktur Sirkulasi Susulan
        Faktur::updateOrCreate(
            ['no_faktur' => 'INV-SRK-SUS-202610-001'],
            [
                'customer_id'    => $custSirkulasi->id,
                'sales_nama'     => null,
                'kategori'       => 'sirkulasi',
                'tipe_faktur'    => 'susulan',
                'tanggal_faktur' => Carbon::now()->subDays(2)->toDateString(),
                'jatuh_tempo'    => Carbon::now()->addDays(7)->toDateString(),
                'rincian_biaya'  => [
                    ['item' => 'Oplah Tambahan Susulan Edisi Khusus Akhir Pekan (250 Eks @ Rp 4.000)', 'qty' => 250, 'harga' => 4000, 'subtotal' => 1000000],
                ],
                'total_tagihan'  => 1000000,
                'total_terbayar' => 0,
                'sisa_piutang'   => 1000000,
                'status'         => 'belum_bayar',
                'created_by'     => $fakturUser->id,
                'catatan'        => 'Pengiriman oplah susulan permintaan agen Pasar Cihideung',
            ]
        );

        // 4.B. Faktur Sirkulasi Insidentil
        $custInsidentil = Customer::firstOrCreate(
            ['nama_customer' => 'Bagian Humas & Protokol Setda Kota Tasikmalaya'],
            ['no_telepon' => '0265-331555', 'alamat' => 'Bale Kota Tasikmalaya, Jl. Letnan Harun No. 1', 'tipe' => 'sirkulasi']
        );

        Faktur::updateOrCreate(
            ['no_faktur' => 'INV-SRK-INS-202610-001'],
            [
                'customer_id'    => $custInsidentil->id,
                'sales_nama'     => null,
                'kategori'       => 'sirkulasi',
                'tipe_faktur'    => 'insidentil',
                'tanggal_faktur' => Carbon::now()->subDay()->toDateString(),
                'jatuh_tempo'    => Carbon::now()->toDateString(),
                'rincian_biaya'  => [
                    ['item' => 'Pembelian Koran Edisi Liputan Khusus Hari Jadi (500 Eks @ Rp 4.500)', 'qty' => 500, 'harga' => 4500, 'subtotal' => 2250000],
                ],
                'total_tagihan'  => 2250000,
                'total_terbayar' => 2250000,
                'sisa_piutang'   => 0,
                'status'         => 'lunas',
                'created_by'     => $fakturUser->id,
                'catatan'        => 'Penjualan koran insidentil borongan acara perayaan Pemkot',
            ]
        );

        // 5. Stok Opname Faktur Iklan Sample
        $currentMonth = Carbon::now()->format('Y-m');
        StokOpnameFaktur::updateOrCreate(
            ['periode_bulan' => $currentMonth],
            [
                'no_dokumen'         => 'SO-FAK-' . Carbon::now()->format('Ym') . '-001',
                'kategori'           => 'iklan',
                'tanggal_opname'     => Carbon::now()->toDateString(),
                'nomor_seri_awal'    => 'INV-IKL-' . Carbon::now()->format('Ym') . '-001',
                'nomor_seri_akhir'   => 'INV-IKL-' . Carbon::now()->format('Ym') . '-100',
                'stok_awal'          => 100,
                'jumlah_terpakai'    => Faktur::where('kategori', 'iklan')->count(),
                'jumlah_rusak_batal' => 1,
                'sisa_blangko_fisik' => max(0, 100 - (Faktur::where('kategori', 'iklan')->count() + 1)),
                'selisih'            => 0,
                'status_opname'      => 'sesuai',
                'keterangan'         => 'Rekonsiliasi fisik blangko faktur iklan edisi berjalan (Nomor Seri INV-IKL ' . $currentMonth . ')',
                'petugas_id'         => $fakturUser->id,
            ]
        );
    }
}
