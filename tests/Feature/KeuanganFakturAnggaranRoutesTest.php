<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeuanganFakturAnggaranRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\AkunPerkiraanSeeder::class);
    }

    public function test_faktur_routes_accessible_by_faktur_role(): void
    {
        $userFaktur = User::factory()->create([
            'role' => 'faktur',
            'status' => 'active',
        ]);

        $routes = [
            route('keuangan.faktur.index'),
            route('keuangan.faktur.create'),
            route('keuangan.faktur.stok-opname'),
            route('keuangan.faktur.sirkulasi.index'),
            route('keuangan.faktur.sirkulasi.susulan'),
            route('keuangan.faktur.sirkulasi.rekap'),
            route('keuangan.faktur.sirkulasi.insidentil'),
            route('keuangan.faktur.cetak-rekap'),
            route('keuangan.inkaso.index'),
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($userFaktur)->get($url);
            $response->assertStatus(200);
        }
    }

    public function test_anggaran_routes_accessible_by_anggaran_role(): void
    {
        $userAnggaran = User::factory()->create([
            'role' => 'anggaran',
            'status' => 'active',
        ]);

        $routes = [
            route('keuangan.anggaran.index'),
            route('keuangan.anggaran.create'),
            route('keuangan.anggaran.tabel.bidang'),
            route('keuangan.anggaran.tabel.perkiraan'),
            route('keuangan.anggaran.tabel.rekening-bank'),
            route('keuangan.anggaran.entry.rab'),
            route('keuangan.anggaran.entry.dp-kasbon'),
            route('keuangan.anggaran.entry.tutup-anggaran'),
            route('keuangan.anggaran.rekap-bp'),
            route('keuangan.anggaran.cetak.index'),
            route('keuangan.anggaran.cetak.seluruh-bidang'),
            route('keuangan.anggaran.cetak.per-bidang'),
            route('keuangan.anggaran.cetak.bp-tanggal'),
            route('keuangan.anggaran.cetak.bp-bulan'),
            route('keuangan.anggaran.cetak.rekap-bidang-coa'),
            route('keuangan.anggaran.cetak.cetak-ulang-bp'),
            route('keuangan.anggaran.cetak.tahunan'),
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($userAnggaran)->get($url);
            $response->assertStatus(200);
        }
    }

    public function test_keuangan_dashboard_accessible_by_admin_keuangan(): void
    {
        $userKeuangan = User::factory()->create([
            'role' => 'admin_keuangan',
            'status' => 'active',
        ]);

        $routes = [
            route('keuangan.dashboard'),
            route('keuangan.alur'),
            route('keuangan.kas.index'),
            route('keuangan.kas.harian'),
            route('keuangan.kas.bulanan'),
            route('keuangan.laporan', ['tab' => 'harian']),
            route('keuangan.laporan', ['tab' => 'bulanan']),
            route('keuangan.laporan', ['tab' => 'inkaso']),
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($userKeuangan)->get($url);
            $response->assertStatus(200);
        }
    }
}
