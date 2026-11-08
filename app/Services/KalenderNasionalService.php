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
     * Ambil seluruh hari libur resmi dari API untuk tahun tertentu.
     */
    public function getHariLiburTahun(int $tahun): array
    {
        $cacheKey = "kalender_libur_nasional_{$tahun}";

        return Cache::remember($cacheKey, 86400 * 30, function () use ($tahun) {
            $apiUrl = "https://api-hari-libur.vercel.app/api?year={$tahun}";

            try {
                $client = Http::timeout(5);
                if (app()->environment('local') || config('app.debug')) {
                    $client = $client->withoutVerifying();
                }
                $response = $client->get($apiUrl);

                if ($response->successful()) {
                    $json = $response->json();
                    if (!empty($json['data']) && is_array($json['data'])) {
                        $holidays = [];
                        foreach ($json['data'] as $item) {
                            $isCuti = str_contains(strtolower($item['description'] ?? ''), 'cuti bersama');
                            $holidays[] = [
                                'tanggal'     => $item['date'],
                                'nama'        => $item['description'],
                                'jenis'       => $isCuti ? 'cuti_bersama' : 'nasional',
                                'is_libur'    => true,
                            ];
                        }
                        return $holidays;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Gagal fetch API Kalender Nasional ({$tahun}): " . $e->getMessage());
            }

            return $this->getFallbackHolidays($tahun);
        });
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
