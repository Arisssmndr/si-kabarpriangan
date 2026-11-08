<?php

namespace Database\Seeders;

use App\Models\JenisIklan;
use App\Models\KategoriMedia;
use Illuminate\Database\Seeder;

class UpdateOnlineTvJenisIklanSeeder extends Seeder
{
    public function run(): void
    {
        $onlMedia = KategoriMedia::firstOrCreate(
            ['kode' => 'ONL'],
            ['nama' => 'Media Online & Digital', 'deskripsi' => 'Grup Portal Kabar Priangan dan Media Sosial', 'is_active' => true]
        );

        $ptvMedia = KategoriMedia::firstOrCreate(
            ['kode' => 'PTV'],
            ['nama' => 'Priangan TV', 'deskripsi' => 'Saluran Kabar Priangan TV & Multimedia Broadcasting', 'is_active' => true]
        );

        // 1. DATA JENIS IKLAN ONLINE (5 KATEGORI / PLATFORM)
        $onlineProducts = [
            // Kategori: PORTAL_BERITA (Grup Portal Kabar Priangan - 9 Website)
            [
                'kode_jenis' => 'ONL001',
                'nama_jenis' => 'Banner Header (Leaderboard 728x90)',
                'tipe_dasar' => 'PORTAL_BERITA',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 1500000,
                'keterangan' => 'Penempatan header utama di website portal pilihan',
            ],
            [
                'kode_jenis' => 'ONL002',
                'nama_jenis' => 'Banner Sidebar (Medium Rectangle 300x250)',
                'tipe_dasar' => 'PORTAL_BERITA',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 1000000,
                'keterangan' => 'Penempatan sidebar kanan artikel berita',
            ],
            [
                'kode_jenis' => 'ONL003',
                'nama_jenis' => 'Advertorial / Rilis Berita Online (1 Portal)',
                'tipe_dasar' => 'PORTAL_BERITA',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 750000,
                'keterangan' => 'Penayangan rilis berita + foto di 1 portal terpilih',
            ],
            [
                'kode_jenis' => 'ONL004',
                'nama_jenis' => 'Paket Rilis Berita All 9 Portal Network',
                'tipe_dasar' => 'PORTAL_BERITA',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 3500000,
                'keterangan' => 'Rilis berita tayang serentak di seluruh 9 portal Kabar Priangan',
            ],
            [
                'kode_jenis' => 'ONL005',
                'nama_jenis' => 'Paket Banner Header All 9 Portal Network',
                'tipe_dasar' => 'PORTAL_BERITA',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 5000000,
                'keterangan' => 'Banner leaderboard tayang di seluruh 9 portal jaringan',
            ],

            // Kategori: INSTAGRAM (@kabarpriangan)
            [
                'kode_jenis' => 'ONL006',
                'nama_jenis' => 'Feed Post / Carousel Instagram',
                'tipe_dasar' => 'INSTAGRAM',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 500000,
                'keterangan' => 'Posting feed / carousel 1-10 slide di IG @kabarpriangan',
            ],
            [
                'kode_jenis' => 'ONL007',
                'nama_jenis' => 'Reels Video Promotion Instagram',
                'tipe_dasar' => 'INSTAGRAM',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 1000000,
                'keterangan' => 'Video reels promosi vertikal 30-60 detik',
            ],
            [
                'kode_jenis' => 'ONL008',
                'nama_jenis' => 'Instagram Story (24 Jam + Swipe Up / Link)',
                'tipe_dasar' => 'INSTAGRAM',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 300000,
                'keterangan' => 'Posting IG Story 24 jam dengan link website/WA',
            ],
            [
                'kode_jenis' => 'ONL009',
                'nama_jenis' => 'Paket Feed + Story Instagram Bundle',
                'tipe_dasar' => 'INSTAGRAM',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 700000,
                'keterangan' => 'Kombinasi hemat 1 post feed + 2 kali IG story',
            ],

            // Kategori: TIKTOK (@kabarpriangan)
            [
                'kode_jenis' => 'ONL010',
                'nama_jenis' => 'Video TikTok Ads / Sponsored Content',
                'tipe_dasar' => 'TIKTOK',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 1250000,
                'keterangan' => 'Produksi & tayang video TikTok di akun resmi',
            ],
            [
                'kode_jenis' => 'ONL011',
                'nama_jenis' => 'TikTok Live Mention & Product Pinning',
                'tipe_dasar' => 'TIKTOK',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 750000,
                'keterangan' => 'Penyebutan nama / promosi produk saat siaran live',
            ],

            // Kategori: YOUTUBE (Kabar Priangan Official)
            [
                'kode_jenis' => 'ONL012',
                'nama_jenis' => 'Video Liputan Khusus / Review YouTube',
                'tipe_dasar' => 'YOUTUBE',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 3000000,
                'keterangan' => 'Video liputan/review penuh tayang di Channel YouTube resmi',
            ],
            [
                'kode_jenis' => 'ONL013',
                'nama_jenis' => 'Adlips / Sponsor Mention YouTube',
                'tipe_dasar' => 'YOUTUBE',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 1000000,
                'keterangan' => 'Penyebutan sponsor pembuka / bumper in video program YouTube',
            ],
            [
                'kode_jenis' => 'ONL014',
                'nama_jenis' => 'YouTube Shorts Video Promotion',
                'tipe_dasar' => 'YOUTUBE',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 750000,
                'keterangan' => 'Video pendek vertikal di tab YouTube Shorts',
            ],

            // Kategori: PODCAST (Studio Priangan)
            [
                'kode_jenis' => 'ONL015',
                'nama_jenis' => 'Taping Episode Podcast Khusus (30-45 Menit)',
                'tipe_dasar' => 'PODCAST',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 3500000,
                'keterangan' => 'Bincang podcast eksklusif bersama host di studio profesional',
            ],
            [
                'kode_jenis' => 'ONL016',
                'nama_jenis' => 'Sponsorship Adlips / Product Placement Podcast',
                'tipe_dasar' => 'PODCAST',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 1500000,
                'keterangan' => 'Penempatan produk dan penyebutan sponsor selama episode podcast',
            ],
        ];

        foreach ($onlineProducts as $p) {
            JenisIklan::updateOrCreate(
                ['kode_jenis' => $p['kode_jenis']],
                [
                    'kategori_media_id' => $onlMedia->id,
                    'nama_jenis'        => $p['nama_jenis'],
                    'tipe_dasar'        => $p['tipe_dasar'],
                    'cara_hitung'       => $p['cara_hitung'],
                    'tarif_dasar'       => $p['tarif_dasar'],
                    'keterangan'        => $p['keterangan'],
                    'is_active'         => true,
                ]
            );
        }

        // 2. DATA JENIS IKLAN PRIANGAN TV (4 KATEGORI KHUSUS PRIANGAN TV SESUAI BRIEF)
        $tvProducts = [
            // Kategori 1: PODCAST (Podcast Priangan TV)
            [
                'kode_jenis' => 'PTV001',
                'nama_jenis' => 'Podcast Taping Talkshow Studio (30-45 Menit)',
                'tipe_dasar' => 'PODCAST',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 3500000,
                'keterangan' => 'Talkshow podcast taping di studio Priangan TV',
            ],
            [
                'kode_jenis' => 'PTV002',
                'nama_jenis' => 'Podcast Adlips / Product Placement TV',
                'tipe_dasar' => 'PODCAST',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 1500000,
                'keterangan' => 'Penyebutan & display produk saat podcast berlangsung',
            ],

            // Kategori 2: DISPLAY_MEDSOS (Display/Poster: Placement di Medsos IG & TikTok Priangan TV)
            [
                'kode_jenis' => 'PTV003',
                'nama_jenis' => 'Display / Poster Instagram Priangan TV',
                'tipe_dasar' => 'DISPLAY_MEDSOS',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 500000,
                'keterangan' => 'Posting poster / flyer promosi di Instagram Priangan TV',
            ],
            [
                'kode_jenis' => 'PTV004',
                'nama_jenis' => 'Display / Poster TikTok Priangan TV',
                'tipe_dasar' => 'DISPLAY_MEDSOS',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 600000,
                'keterangan' => 'Posting display visual / slide di TikTok Priangan TV',
            ],
            [
                'kode_jenis' => 'PTV005',
                'nama_jenis' => 'Paket Display Medsos (Instagram + TikTok Priangan TV)',
                'tipe_dasar' => 'DISPLAY_MEDSOS',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 950000,
                'keterangan' => 'Paket tayang poster di Instagram dan TikTok Priangan TV',
            ],

            // Kategori 3: BANNER_WEB (Banner Website Priangan TV: Parallax, Sidebar Kiri/Kanan, Native)
            [
                'kode_jenis' => 'PTV006',
                'nama_jenis' => 'Banner Parallax (Full Width Interactive Website)',
                'tipe_dasar' => 'BANNER_WEB',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 2000000,
                'keterangan' => 'Banner efek gulir parallax layar penuh di web Priangan TV',
            ],
            [
                'kode_jenis' => 'PTV007',
                'nama_jenis' => 'Banner Sidebar (Kiri / Kanan Website Priangan TV)',
                'tipe_dasar' => 'BANNER_WEB',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 1250000,
                'keterangan' => 'Banner posisi sidebar kiri/kanan halaman video Priangan TV',
            ],
            [
                'kode_jenis' => 'PTV008',
                'nama_jenis' => 'Banner Native (In-Article & In-Feed Website Priangan TV)',
                'tipe_dasar' => 'BANNER_WEB',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 1000000,
                'keterangan' => 'Banner visual menyatu dengan daftar siaran video Priangan TV',
            ],

            // Kategori 4: VIDEO_LIPSUS (Video Lipsus / Liputan Khusus Priangan TV)
            [
                'kode_jenis' => 'PTV009',
                'nama_jenis' => 'Video Lipsus (Liputan Khusus Standar 3-5 Menit)',
                'tipe_dasar' => 'VIDEO_LIPSUS',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 2500000,
                'keterangan' => 'Liputan jurnalis profesional TV lengkap narasi & penayangan',
            ],
            [
                'kode_jenis' => 'PTV010',
                'nama_jenis' => 'Video Lipsus Dokumenter / Profil Lembaga (5-10 Menit)',
                'tipe_dasar' => 'VIDEO_LIPSUS',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 4500000,
                'keterangan' => 'Produksi video profil mendalam instansi/perusahaan',
            ],
            [
                'kode_jenis' => 'PTV011',
                'nama_jenis' => 'Running Text Siaran Berita Priangan TV',
                'tipe_dasar' => 'VIDEO_LIPSUS',
                'cara_hitung' => 'PER_EDISI',
                'tarif_dasar' => 350000,
                'keterangan' => 'Teks berjalan di bawah layar siaran berita Priangan TV',
            ],
        ];

        foreach ($tvProducts as $p) {
            JenisIklan::updateOrCreate(
                ['kode_jenis' => $p['kode_jenis']],
                [
                    'kategori_media_id' => $ptvMedia->id,
                    'nama_jenis'        => $p['nama_jenis'],
                    'tipe_dasar'        => $p['tipe_dasar'],
                    'cara_hitung'       => $p['cara_hitung'],
                    'tarif_dasar'       => $p['tarif_dasar'],
                    'keterangan'        => $p['keterangan'],
                    'is_active'         => true,
                ]
            );
        }
    }
}
