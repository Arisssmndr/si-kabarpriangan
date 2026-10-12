<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IklanOnline;
use App\Models\IklanPriangan;

class IklanMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $onlineData = [
            ['kode_iklanonline' => 'ONL001', 'jenis_iklanonline' => 'Banner Header (Leaderboard)'],
            ['kode_iklanonline' => 'ONL002', 'jenis_iklanonline' => 'Banner Sidebar (Medium Rectangle)'],
            ['kode_iklanonline' => 'ONL003', 'jenis_iklanonline' => 'Advertorial / Rilis Berita Online'],
            ['kode_iklanonline' => 'ONL004', 'jenis_iklanonline' => 'Social Media Post (Instagram / FB)'],
            ['kode_iklanonline' => 'ONL005', 'jenis_iklanonline' => 'Video Ads / Reels / TikTok'],
        ];

        foreach ($onlineData as $item) {
            IklanOnline::firstOrCreate(
                ['kode_iklanonline' => $item['kode_iklanonline']],
                ['jenis_iklanonline' => $item['jenis_iklanonline']]
            );
        }

        $prianganData = [
            ['kode_iklanpriangan' => 'PTV001', 'jenis_iklanpriangan' => 'Video Ads Commercial (30 Detik)'],
            ['kode_iklanpriangan' => 'PTV002', 'jenis_iklanpriangan' => 'Running Text Berita'],
            ['kode_iklanpriangan' => 'PTV003', 'jenis_iklanpriangan' => 'Liputan Khusus / Talkshow'],
            ['kode_iklanpriangan' => 'PTV004', 'jenis_iklanpriangan' => 'Bumper In / Out Program'],
            ['kode_iklanpriangan' => 'PTV005', 'jenis_iklanpriangan' => 'Sponsoring Program Acara'],
        ];

        foreach ($prianganData as $item) {
            IklanPriangan::firstOrCreate(
                ['kode_iklanpriangan' => $item['kode_iklanpriangan']],
                ['jenis_iklanpriangan' => $item['jenis_iklanpriangan']]
            );
        }
    }
}
