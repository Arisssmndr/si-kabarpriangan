<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingCetakTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->user = User::where('role', 'accounting')->firstOrFail();
    }

    public function test_all_10_accounting_cetak_routes_render_official_document(): void
    {
        $routes = [
            'accounting.laporan.neraca.cetak',
            'accounting.laporan.laba-rugi.cetak',
            'accounting.laporan.arus-kas.cetak',
            'accounting.laporan.ekuitas.cetak',
            'accounting.laporan.rincian-beban.cetak',
            'accounting.gl.jurnal.cetak',
            'accounting.gl.buku-besar.cetak',
            'accounting.gl.neraca-lajur.cetak',
            'accounting.cetak.iklan.cetak',
            'accounting.cetak.sirkulasi.cetak',
        ];

        foreach ($routes as $routeName) {
            $response = $this->actingAs($this->user)->get(route($routeName));

            $response->assertStatus(200);
            $response->assertSee('PT. BERKAH PIKIRAN RAKYAT');
            $response->assertSee('HARIAN UMUM KABAR PRIANGAN');
            $response->assertSee('window.print()', false);
            $response->assertSee('Disusun Oleh', false);
            $response->assertSee('Diperiksa Oleh', false);
            $response->assertSee('Disetujui Oleh', false);
        }
    }

    public function test_sirkulasi_pages_render_without_error(): void
    {
        $sirkulasiUser = User::where('role', 'sirkulasi')->first() ?? $this->user;

        $routes200 = [
            'sirkulasi.dashboard',
            'sirkulasi.tabel.agen',
            'sirkulasi.tabel.wilayah',
            'sirkulasi.tabel.pengepakan',
            'sirkulasi.tabel.jenis-agen',
            'sirkulasi.jatah.edit',
            'sirkulasi.jatah.copy',
            'sirkulasi.jatah.hitung',
            'sirkulasi.jatah.tutup',
            'sirkulasi.cetak.pemasaran',
            'sirkulasi.cetak.penjualan',
            'sirkulasi.cetak.nota-distribusi',
            'sirkulasi.cetak.alamat-agen',
        ];

        foreach ($routes200 as $routeName) {
            $response = $this->actingAs($sirkulasiUser)->get(route($routeName));
            $response->assertStatus(200);
        }

        $routesAliases = [
            'sirkulasi.jatah.harian',
            'sirkulasi.logistik.retur',
            'sirkulasi.logistik.surat-jalan',
            'sirkulasi.master.agen',
            'sirkulasi.master.pelanggan',
            'sirkulasi.laporan.pemasaran',
            'sirkulasi.laporan.penjualan',
        ];

        foreach ($routesAliases as $routeName) {
            $response = $this->actingAs($sirkulasiUser)->get(route($routeName));
            $response->assertStatus(302);
        }
    }
}
