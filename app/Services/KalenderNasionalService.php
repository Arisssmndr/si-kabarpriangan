<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KalenderNasionalService
{
    /**
     * Daftar Hari Peringatan & Hari Besar Nasional Indonesia.
     * Sangat berguna untuk redaksi & divisi iklan koran dalam membidik edisi khusus / ucapan selamat.
     */
    public const HARI_PERINGATAN_NASIONAL = [
        '01-10' => 'Hari Gerakan Satu Juta Pohon',
        '01-15' => 'Hari Dharma Samudera',
        '01-25' => 'Hari Gizi dan Makanan',
        '02-09' => 'Hari Pers Nasional (HPN)',
        '02-28' => 'Hari Gizi Nasional Indonesia',
        '03-01' => 'Hari Penegakan Kedaulatan Negara',
        '03-09' => 'Hari Musik Nasional',
        '03-30' => 'Hari Film Nasional',
        '04-09' => 'Hari TNI Angkatan Udara',
        '04-21' => 'Hari Kartini',
        '04-22' => 'Hari Bumi',
        '04-24' => 'Hari Angkutan Nasional',
        '04-28' => 'Hari Puisi Nasional',
        '05-02' => 'Hari Pendidikan Nasional (Hardiknas)',
        '05-17' => 'Hari Buku Nasional',
        '05-20' => 'Hari Kebangkitan Nasional (Harkitnas)',
        '05-29' => 'Hari Lanjut Usia Nasional',
        '06-01' => 'Hari Lahir Pancasila',
        '06-21' => 'Hari Krida Pertanian',
        '06-24' => 'Hari Bidan Nasional',
        '06-29' => 'Hari Keluarga Nasional (Harganas)',
        '07-01' => 'Hari Bhayangkara (HUT Polri)',
        '07-05' => 'Hari Bank Indonesia',
        '07-12' => 'Hari Koperasi Indonesia',
        '07-22' => 'Hari Kejaksaan Republik Indonesia (Hari Bhakti Adhyaksa)',
        '07-23' => 'Hari Anak Nasional',
        '08-10' => 'Hari Veteran Nasional / Kebangkitan Teknologi',
        '08-14' => 'Hari Pramuka Nasional',
        '08-17' => 'Hari Kemerdekaan RI (HUT Proklamasi)',
        '09-01' => 'Hari Polisi Wanita (Polwan)',
        '09-09' => 'Hari Olahraga Nasional (Haornas)',
        '09-11' => 'Hari Radio Republik Indonesia (RRI)',
        '09-17' => 'Hari Perhubungan Nasional / PMI',
        '09-24' => 'Hari Tani Nasional',
        '09-27' => 'Hari Pos dan Telekomunikasi',
        '10-01' => 'Hari Kesaktian Pancasila',
        '10-02' => 'Hari Batik Nasional',
        '10-05' => 'Hari TNI (Tentara Nasional Indonesia)',
        '10-14' => 'Hari Penglihatan Sedunia',
        '10-24' => 'Hari Dokter Nasional',
        '10-28' => 'Hari Sumpah Pemuda',
        '10-30' => 'Hari Keuangan Nasional',
        '11-05' => 'Hari Cinta Puspa dan Satwa Nasional',
        '11-10' => 'Hari Pahlawan Nasional',
        '11-12' => 'Hari Kesehatan Nasional (HKN) / Hari Ayah',
        '11-14' => 'Hari Brimob',
        '11-25' => 'Hari Guru Nasional (HGN / PGRI)',
        '11-28' => 'Hari Menanam Pohon Indonesia',
        '11-29' => 'Hari KORPRI',
        '12-03' => 'Hari Disabilitas Internasional',
        '12-09' => 'Hari Anti Korupsi Sedunia (Hakordia)',
        '12-10' => 'Hari Hak Asasi Manusia (HAM)',
        '12-13' => 'Hari Nusantara',
        '12-15' => 'Hari Juang Kartika TNI AD',
        '12-19' => 'Hari Bela Negara',
        '12-22' => 'Hari Ibu',
    ];

    /**
     * Ambil seluruh hari libur resmi nasional & cuti bersama untuk tahun tertentu.
     * Menggunakan data terverifikasi offline 100% cepat, andal, tanpa ketergantungan API pihak ketiga yang rentan timeout.
     */
    public function getHariLiburTahun(int $tahun): array
    {
        $cacheKey = "kalender_libur_nasional_{$tahun}";

        return Cache::remember($cacheKey, 86400 * 30, function () use ($tahun) {
            return $this->getHolidaysByYear($tahun);
        });
    }

    /**
     * Dataset Hari Libur Resmi Nasional & Cuti Bersama SKB 3 Menteri Indonesia.
     */
    public function getHolidaysByYear(int $tahun): array
    {
        $dataset = [
            2024 => [
                ['tanggal' => '2024-01-01', 'nama' => 'Tahun Baru 2024 Masehi', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2024-02-08', 'nama' => 'Isra Mikraj Nabi Muhammad SAW', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2024-02-09', 'nama' => 'Cuti Bersama Tahun Baru Imlek 2575 Kongzili', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2024-02-10', 'nama' => 'Tahun Baru Imlek 2575 Kongzili', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2024-03-11', 'nama' => 'Hari Suci Nyepi (Tahun Baru Saka 1946)', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2024-03-12', 'nama' => 'Cuti Bersama Hari Suci Nyepi', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2024-03-29', 'nama' => 'Wafat Yesus Kristus', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2024-03-31', 'nama' => 'Hari Paskah', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2024-04-08', 'nama' => 'Cuti Bersama Idul Fitri 1445 H', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2024-04-09', 'nama' => 'Cuti Bersama Idul Fitri 1445 H', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2024-04-10', 'nama' => 'Hari Raya Idul Fitri 1445 H', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2024-04-11', 'nama' => 'Hari Raya Idul Fitri 1445 H', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2024-04-12', 'nama' => 'Cuti Bersama Idul Fitri 1445 H', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2024-04-15', 'nama' => 'Cuti Bersama Idul Fitri 1445 H', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2024-05-01', 'nama' => 'Hari Buruh Internasional', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2024-05-09', 'nama' => 'Kenaikan Yesus Kristus', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2024-05-10', 'nama' => 'Cuti Bersama Kenaikan Yesus Kristus', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2024-05-23', 'nama' => 'Hari Raya Waisak 2568 BE', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2024-05-24', 'nama' => 'Cuti Bersama Hari Raya Waisak', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2024-06-01', 'nama' => 'Hari Lahir Pancasila', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2024-06-17', 'nama' => 'Hari Raya Idul Adha 1445 H', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2024-06-18', 'nama' => 'Cuti Bersama Hari Raya Idul Adha', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2024-07-07', 'nama' => '1 Muharram / Tahun Baru Islam 1446 H', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2024-08-17', 'nama' => 'Hari Kemerdekaan RI (HUT ke-79 RI)', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2024-09-16', 'nama' => 'Maulid Nabi Muhammad SAW', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2024-12-25', 'nama' => 'Hari Raya Natal', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2024-12-26', 'nama' => 'Cuti Bersama Hari Raya Natal', 'jenis' => 'cuti_bersama', 'is_libur' => true],
            ],
            2025 => [
                ['tanggal' => '2025-01-01', 'nama' => 'Tahun Baru 2025 Masehi', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2025-01-27', 'nama' => 'Isra Mikraj Nabi Muhammad SAW', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2025-01-28', 'nama' => 'Cuti Bersama Tahun Baru Imlek 2576 Kongzili', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2025-01-29', 'nama' => 'Tahun Baru Imlek 2576 Kongzili', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2025-03-28', 'nama' => 'Cuti Bersama Hari Suci Nyepi', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2025-03-29', 'nama' => 'Hari Suci Nyepi (Tahun Baru Saka 1947)', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2025-03-31', 'nama' => 'Hari Raya Idul Fitri 1446 H', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2025-04-01', 'nama' => 'Hari Raya Idul Fitri 1446 H', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2025-04-02', 'nama' => 'Cuti Bersama Idul Fitri 1446 H', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2025-04-03', 'nama' => 'Cuti Bersama Idul Fitri 1446 H', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2025-04-04', 'nama' => 'Cuti Bersama Idul Fitri 1446 H', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2025-04-07', 'nama' => 'Cuti Bersama Idul Fitri 1446 H', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2025-04-18', 'nama' => 'Wafat Yesus Kristus', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2025-04-20', 'nama' => 'Hari Paskah', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2025-05-01', 'nama' => 'Hari Buruh Internasional', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2025-05-12', 'nama' => 'Hari Raya Waisak 2569 BE', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2025-05-13', 'nama' => 'Cuti Bersama Hari Raya Waisak', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2025-05-29', 'nama' => 'Kenaikan Yesus Kristus', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2025-05-30', 'nama' => 'Cuti Bersama Kenaikan Yesus Kristus', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2025-06-01', 'nama' => 'Hari Lahir Pancasila', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2025-06-06', 'nama' => 'Hari Raya Idul Adha 1446 H', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2025-06-09', 'nama' => 'Cuti Bersama Hari Raya Idul Adha', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2025-06-27', 'nama' => '1 Muharram / Tahun Baru Islam 1447 H', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2025-08-17', 'nama' => 'Hari Kemerdekaan RI (HUT ke-80 RI)', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2025-09-05', 'nama' => 'Maulid Nabi Muhammad SAW', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2025-12-25', 'nama' => 'Hari Raya Natal', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2025-12-26', 'nama' => 'Cuti Bersama Hari Raya Natal', 'jenis' => 'cuti_bersama', 'is_libur' => true],
            ],
            2026 => [
                ['tanggal' => '2026-01-01', 'nama' => 'Tahun Baru 2026 Masehi', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2026-01-16', 'nama' => 'Isra Mikraj Nabi Muhammad SAW', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2026-02-16', 'nama' => 'Cuti Bersama Tahun Baru Imlek 2577 Kongzili', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2026-02-17', 'nama' => 'Tahun Baru Imlek 2577 Kongzili', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2026-03-19', 'nama' => 'Hari Suci Nyepi (Tahun Baru Saka 1948)', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2026-03-20', 'nama' => 'Hari Raya Idul Fitri 1447 H', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2026-03-21', 'nama' => 'Hari Raya Idul Fitri 1447 H', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2026-03-23', 'nama' => 'Cuti Bersama Idul Fitri 1447 H', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2026-03-24', 'nama' => 'Cuti Bersama Idul Fitri 1447 H', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2026-04-03', 'nama' => 'Wafat Yesus Kristus', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2026-04-05', 'nama' => 'Hari Paskah', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2026-05-01', 'nama' => 'Hari Buruh Internasional', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2026-05-14', 'nama' => 'Kenaikan Yesus Kristus', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2026-05-15', 'nama' => 'Cuti Bersama Kenaikan Yesus Kristus', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2026-05-27', 'nama' => 'Hari Raya Idul Adha 1447 H', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2026-05-28', 'nama' => 'Cuti Bersama Hari Raya Idul Adha', 'jenis' => 'cuti_bersama', 'is_libur' => true],
                ['tanggal' => '2026-05-31', 'nama' => 'Hari Raya Waisak 2570 BE', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2026-06-01', 'nama' => 'Hari Lahir Pancasila', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2026-06-16', 'nama' => '1 Muharram / Tahun Baru Islam 1448 H', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2026-08-17', 'nama' => 'Hari Kemerdekaan RI (HUT ke-81 RI)', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2026-08-25', 'nama' => 'Maulid Nabi Muhammad SAW', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2026-12-25', 'nama' => 'Hari Raya Natal', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2026-12-26', 'nama' => 'Cuti Bersama Hari Raya Natal', 'jenis' => 'cuti_bersama', 'is_libur' => true],
            ],
            2027 => [
                ['tanggal' => '2027-01-01', 'nama' => 'Tahun Baru 2027 Masehi', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2027-01-06', 'nama' => 'Isra Mikraj Nabi Muhammad SAW', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2027-02-06', 'nama' => 'Tahun Baru Imlek 2578 Kongzili', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2027-03-08', 'nama' => 'Hari Suci Nyepi (Tahun Baru Saka 1949)', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2027-03-09', 'nama' => 'Hari Raya Idul Fitri 1448 H', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2027-03-10', 'nama' => 'Hari Raya Idul Fitri 1448 H', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2027-03-26', 'nama' => 'Wafat Yesus Kristus', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2027-05-01', 'nama' => 'Hari Buruh Internasional', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2027-05-06', 'nama' => 'Kenaikan Yesus Kristus', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2027-05-16', 'nama' => 'Hari Raya Idul Adha 1448 H', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2027-05-20', 'nama' => 'Hari Raya Waisak 2571 BE', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2027-06-01', 'nama' => 'Hari Lahir Pancasila', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2027-06-06', 'nama' => '1 Muharram / Tahun Baru Islam 1449 H', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2027-08-15', 'nama' => 'Maulid Nabi Muhammad SAW', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2027-08-17', 'nama' => 'Hari Kemerdekaan RI (HUT ke-82 RI)', 'jenis' => 'nasional', 'is_libur' => true],
                ['tanggal' => '2027-12-25', 'nama' => 'Hari Raya Natal', 'jenis' => 'nasional', 'is_libur' => true],
            ],
        ];

        return $dataset[$tahun] ?? $this->getFallbackHolidays($tahun);
    }

    /**
     * Ambil data lengkap kalender bulanan (Hari Libur Resmi + Hari Peringatan Nasional).
     */
    public function getKalenderBulan(int $tahun, int $bulan): array
    {
        $allHolidays = $this->getHariLiburTahun($tahun);
        $monthStr = sprintf('%02d', $bulan);

        $liburBulanIni = [];
        foreach ($allHolidays as $h) {
            if (str_starts_with($h['tanggal'], "{$tahun}-{$monthStr}")) {
                $liburBulanIni[$h['tanggal']] = $h;
            }
        }

        $peringatanBulanIni = [];
        foreach (self::HARI_PERINGATAN_NASIONAL as $mmdd => $namaPeringatan) {
            if (str_starts_with($mmdd, "{$monthStr}-")) {
                $dateStr = "{$tahun}-{$mmdd}";
                $peringatanBulanIni[$dateStr] = [
                    'tanggal'  => $dateStr,
                    'nama'     => $namaPeringatan,
                    'jenis'    => 'peringatan_nasional',
                    'is_libur' => isset($liburBulanIni[$dateStr]),
                ];
            }
        }

        return [
            'libur'      => $liburBulanIni,
            'peringatan' => $peringatanBulanIni,
        ];
    }

    /**
     * Fallback hari libur nasional Indonesia (jika API offline).
     */
    private function getFallbackHolidays(int $tahun): array
    {
        return [
            ['tanggal' => "{$tahun}-01-01", 'nama' => 'Tahun Baru Masehi', 'jenis' => 'nasional', 'is_libur' => true],
            ['tanggal' => "{$tahun}-05-01", 'nama' => 'Hari Buruh Internasional', 'jenis' => 'nasional', 'is_libur' => true],
            ['tanggal' => "{$tahun}-06-01", 'nama' => 'Hari Lahir Pancasila', 'jenis' => 'nasional', 'is_libur' => true],
            ['tanggal' => "{$tahun}-08-17", 'nama' => 'Hari Kemerdekaan Republik Indonesia', 'jenis' => 'nasional', 'is_libur' => true],
            ['tanggal' => "{$tahun}-12-25", 'nama' => 'Hari Raya Natal', 'jenis' => 'nasional', 'is_libur' => true],
        ];
    }
}
