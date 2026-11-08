<?php

namespace Database\Seeders;

use App\Models\AkunPerkiraan;
use App\Models\PeriodeAkuntansi;
use Illuminate\Database\Seeder;

class AkunPerkiraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            // KEPALA 1: ASET
            ['kode_perkiraan' => '1.00.00.01.01.00.00001', 'nama_perkiraan' => 'Kas Operasional / Kasir', 'kepala' => 1, 'kategori' => 'Aset Lancar', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '1.00.01.01.01.00.06270', 'nama_perkiraan' => 'Bank BJB Giro Tasik (Rek: 6270)', 'kepala' => 1, 'kategori' => 'Aset Lancar', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '1.00.01.01.01.00.00001', 'nama_perkiraan' => 'Bank BCA Giro Tasikmalaya', 'kepala' => 1, 'kategori' => 'Aset Lancar', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '1.00.01.01.01.00.00002', 'nama_perkiraan' => 'Bank BRI Cabang Tasikmalaya', 'kepala' => 1, 'kategori' => 'Aset Lancar', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '1.00.01.01.01.00.00003', 'nama_perkiraan' => 'Bank BTN Cabang Tasikmalaya', 'kepala' => 1, 'kategori' => 'Aset Lancar', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '1.00.04.01.01.01.00001', 'nama_perkiraan' => 'Piutang Usaha Iklan Umum', 'kepala' => 1, 'kategori' => 'Aset Lancar', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '1.00.04.01.01.01.04593', 'nama_perkiraan' => 'Piutang Iklan - Klien Khusus', 'kepala' => 1, 'kategori' => 'Aset Lancar', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '1.00.04.01.01.00.00002', 'nama_perkiraan' => 'Piutang Sirkulasi - Sariksa Agency', 'kepala' => 1, 'kategori' => 'Aset Lancar', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '1.00.04.01.01.00.00029', 'nama_perkiraan' => 'Piutang Sirkulasi - Hasan Gunadi', 'kepala' => 1, 'kategori' => 'Aset Lancar', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '1.00.04.01.01.00.00030', 'nama_perkiraan' => 'Piutang Sirkulasi - Agen Berkah Ciamis', 'kepala' => 1, 'kategori' => 'Aset Lancar', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '1.00.05.01.01.00.00001', 'nama_perkiraan' => 'Cadangan Kerugian Piutang (Kontra-Aset)', 'kepala' => 1, 'kategori' => 'Aset Lancar', 'saldo_normal' => 'kredit'],
            ['kode_perkiraan' => '1.00.06.01.01.00.00001', 'nama_perkiraan' => 'Piutang Pajak PPh Pasal 23', 'kepala' => 1, 'kategori' => 'Aset Lancar', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '1.00.07.01.01.00.00001', 'nama_perkiraan' => 'Persediaan Kertas Koran & Tinta', 'kepala' => 1, 'kategori' => 'Aset Lancar', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '1.00.08.01.01.00.00001', 'nama_perkiraan' => 'Biaya Dibayar di Muka', 'kepala' => 1, 'kategori' => 'Aset Lancar', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '1.00.10.01.01.00.00001', 'nama_perkiraan' => 'Aset Tetap Mesin Cetak & Komputer', 'kepala' => 1, 'kategori' => 'Aset Tetap', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '1.00.11.01.01.00.00001', 'nama_perkiraan' => 'Akumulasi Penyusutan Aset Tetap', 'kepala' => 1, 'kategori' => 'Aset Tetap', 'saldo_normal' => 'kredit'],

            // KEPALA 2: KEWAJIBAN / UTANG
            ['kode_perkiraan' => '2.00.01.01.01.01.00001', 'nama_perkiraan' => 'Pendapatan Diterima Di Muka / Uang Muka Iklan', 'kepala' => 2, 'kategori' => 'Kewajiban Lancar', 'saldo_normal' => 'kredit'],
            ['kode_perkiraan' => '2.00.03.01.01.00.00001', 'nama_perkiraan' => 'Utang Pajak PPN Keluaran 11%', 'kepala' => 2, 'kategori' => 'Kewajiban Lancar', 'saldo_normal' => 'kredit'],
            ['kode_perkiraan' => '2.00.03.01.02.00.00001', 'nama_perkiraan' => 'Utang Pajak PPh Pasal 21', 'kepala' => 2, 'kategori' => 'Kewajiban Lancar', 'saldo_normal' => 'kredit'],
            ['kode_perkiraan' => '2.00.03.01.03.00.00001', 'nama_perkiraan' => 'Utang Pajak PPh Pasal 23', 'kepala' => 2, 'kategori' => 'Kewajiban Lancar', 'saldo_normal' => 'kredit'],
            ['kode_perkiraan' => '2.00.04.01.01.00.00001', 'nama_perkiraan' => 'Utang Insentif & Komisi AE', 'kepala' => 2, 'kategori' => 'Kewajiban Lancar', 'saldo_normal' => 'kredit'],
            ['kode_perkiraan' => '2.00.05.01.01.00.00001', 'nama_perkiraan' => 'Beban Akrual (Belum Dibayar)', 'kepala' => 2, 'kategori' => 'Kewajiban Lancar', 'saldo_normal' => 'kredit'],

            // KEPALA 3: EKUITAS
            ['kode_perkiraan' => '3.00.01.01.01.00.00001', 'nama_perkiraan' => 'Modal Disetor PT Berkah Pikiran Rakyat', 'kepala' => 3, 'kategori' => 'Ekuitas', 'saldo_normal' => 'kredit'],
            ['kode_perkiraan' => '3.00.02.01.01.00.00001', 'nama_perkiraan' => 'Laba Ditahan', 'kepala' => 3, 'kategori' => 'Ekuitas', 'saldo_normal' => 'kredit'],
            ['kode_perkiraan' => '3.00.03.01.01.00.00001', 'nama_perkiraan' => 'Laba Tahun Berjalan', 'kepala' => 3, 'kategori' => 'Ekuitas', 'saldo_normal' => 'kredit'],

            // KEPALA 4: PENDAPATAN
            ['kode_perkiraan' => '4.00.01.01.01.00.00001', 'nama_perkiraan' => 'Pendapatan Iklan Kolom Koran', 'kepala' => 4, 'kategori' => 'Pendapatan Usaha', 'saldo_normal' => 'kredit'],
            ['kode_perkiraan' => '4.00.01.01.02.00.00001', 'nama_perkiraan' => 'Pendapatan Iklan Display Koran (BW/FC)', 'kepala' => 4, 'kategori' => 'Pendapatan Usaha', 'saldo_normal' => 'kredit'],
            ['kode_perkiraan' => '4.00.01.01.03.00.00001', 'nama_perkiraan' => 'Pendapatan Iklan Baris Koran', 'kepala' => 4, 'kategori' => 'Pendapatan Usaha', 'saldo_normal' => 'kredit'],
            ['kode_perkiraan' => '4.00.01.01.04.00.00001', 'nama_perkiraan' => 'Pendapatan Iklan Advertorial Koran', 'kepala' => 4, 'kategori' => 'Pendapatan Usaha', 'saldo_normal' => 'kredit'],
            ['kode_perkiraan' => '4.00.02.01.01.00.00001', 'nama_perkiraan' => 'Pendapatan Iklan Media Online', 'kepala' => 4, 'kategori' => 'Pendapatan Usaha', 'saldo_normal' => 'kredit'],
            ['kode_perkiraan' => '4.00.03.01.01.00.00001', 'nama_perkiraan' => 'Pendapatan Iklan Priangan TV', 'kepala' => 4, 'kategori' => 'Pendapatan Usaha', 'saldo_normal' => 'kredit'],
            ['kode_perkiraan' => '4.00.04.01.01.00.00001', 'nama_perkiraan' => 'Pendapatan Sirkulasi Koran Cetak', 'kepala' => 4, 'kategori' => 'Pendapatan Usaha', 'saldo_normal' => 'kredit'],
            ['kode_perkiraan' => '4.00.05.01.01.00.00001', 'nama_perkiraan' => 'Koreksi & Diskon Penjualan Iklan (CN-IKL)', 'kepala' => 4, 'kategori' => 'Pendapatan Usaha', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '4.00.05.01.02.00.00001', 'nama_perkiraan' => 'Retur & Koreksi Sirkulasi Koran (CN-SIR)', 'kepala' => 4, 'kategori' => 'Pendapatan Usaha', 'saldo_normal' => 'debet'],

            // KEPALA 5: HPP / BEBAN POKOK PRODUKSI
            ['kode_perkiraan' => '5.00.01.01.01.00.00001', 'nama_perkiraan' => 'HPP Kertas Koran (Roll)', 'kepala' => 5, 'kategori' => 'Beban Pokok Produksi', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '5.00.01.01.02.00.00001', 'nama_perkiraan' => 'HPP Tinta & Plat Cetak', 'kepala' => 5, 'kategori' => 'Beban Pokok Produksi', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '5.00.01.01.03.00.00001', 'nama_perkiraan' => 'HPP Ongkos Cetak Percetakan', 'kepala' => 5, 'kategori' => 'Beban Pokok Produksi', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '5.00.02.01.01.00.00001', 'nama_perkiraan' => 'Beban Ekspedisi & Armada Distribusi Subuh', 'kepala' => 5, 'kategori' => 'Beban Pokok Produksi', 'saldo_normal' => 'debet'],

            // KEPALA 6: BEBAN OPERASIONAL PER BIDANG
            ['kode_perkiraan' => '6.00.01.01.01.00.00001', 'nama_perkiraan' => 'Beban Operasional Bidang 01 (Iklan)', 'kepala' => 6, 'kategori' => 'Beban Operasional', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '6.00.02.01.01.00.00001', 'nama_perkiraan' => 'Beban Operasional Bidang 02 (Sirkulasi)', 'kepala' => 6, 'kategori' => 'Beban Operasional', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '6.00.03.01.01.00.00001', 'nama_perkiraan' => 'Beban Operasional Bidang 03 (Umum & SDM)', 'kepala' => 6, 'kategori' => 'Beban Operasional', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '6.00.04.01.01.00.00001', 'nama_perkiraan' => 'Beban Operasional Bidang 04 (Akuntansi)', 'kepala' => 6, 'kategori' => 'Beban Operasional', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '6.00.05.01.01.00.00001', 'nama_perkiraan' => 'Beban Operasional Bidang 05 (Keuangan)', 'kepala' => 6, 'kategori' => 'Beban Operasional', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '6.00.06.01.01.00.00001', 'nama_perkiraan' => 'Beban Operasional Bidang 06 (Redaksi & Wartawan)', 'kepala' => 6, 'kategori' => 'Beban Operasional', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '6.00.07.01.01.00.00001', 'nama_perkiraan' => 'Beban Operasional Bidang 07 (Pimpinan)', 'kepala' => 6, 'kategori' => 'Beban Operasional', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '6.00.08.01.01.00.00001', 'nama_perkiraan' => 'Beban Operasional Bidang 08 (Tehnik Redaksi)', 'kepala' => 6, 'kategori' => 'Beban Operasional', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '6.00.10.01.01.00.00001', 'nama_perkiraan' => 'Beban Operasional Bidang 10 (Pemasaran)', 'kepala' => 6, 'kategori' => 'Beban Operasional', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '6.00.20.01.01.00.00001', 'nama_perkiraan' => 'Beban Operasional Bidang 20 (Sekretariat)', 'kepala' => 6, 'kategori' => 'Beban Operasional', 'saldo_normal' => 'debet'],
            ['kode_perkiraan' => '6.00.21.01.01.00.00001', 'nama_perkiraan' => 'Biaya Manajemen PT Berkah Pikiran Rakyat (Bidang 21)', 'kepala' => 6, 'kategori' => 'Beban Operasional', 'saldo_normal' => 'debet'],

            // KEPALA 7 & 8: PENDAPATAN & BEBAN LAIN-LAIN
            ['kode_perkiraan' => '7.00.01.01.01.00.00001', 'nama_perkiraan' => 'Pendapatan Bunga Bank & Jasa Giro', 'kepala' => 7, 'kategori' => 'Pendapatan Lain-lain', 'saldo_normal' => 'kredit'],
            ['kode_perkiraan' => '8.00.01.01.01.00.00001', 'nama_perkiraan' => 'Beban Administrasi Bank & Buku Giro', 'kepala' => 8, 'kategori' => 'Beban Lain-lain', 'saldo_normal' => 'debet'],
        ];

        foreach ($accounts as $acc) {
            AkunPerkiraan::updateOrCreate(
                ['kode_perkiraan' => $acc['kode_perkiraan']],
                $acc
            );
        }

        // Buat periode aktif saat ini
        $now = now();
        PeriodeAkuntansi::firstOrCreate(
            ['periode_key' => $now->format('Y-m')],
            [
                'tahun'  => $now->year,
                'bulan'  => $now->month,
                'status' => 'terbuka',
            ]
        );
    }
}

