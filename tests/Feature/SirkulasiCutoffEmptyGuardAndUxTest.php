<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SirkulasiCutoffEmptyGuardAndUxTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_cutoff_harian_is_rejected_when_jatah_data_is_empty(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $targetDate = '2026-11-28';

        // Pastikan tidak ada data jatah di Cache
        Cache::forget('jatah_harian_' . $targetDate);

        $response = $this->actingAs($user)->post(route('sirkulasi.jatah.tutup.lock'), [
            'mode' => 'harian',
            'tanggal' => $targetDate,
            'action' => 'lock',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('Belum ada data jatah oplah', session('error'));

        // Pastikan tanggal TIDAK terkunci
        $this->assertFalse(Cache::get('locked_harian_' . $targetDate, false));
    }

    public function test_edit_jatah_renders_disabled_cutoff_button_when_data_is_empty(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $targetDate = Carbon::tomorrow()->addDays(5)->toDateString();

        // Pastikan data jatah kosong
        Cache::forget('jatah_harian_' . $targetDate);

        $response = $this->actingAs($user)->get(route('sirkulasi.jatah.edit', ['tanggal' => $targetDate]));
        $response->assertStatus(200);

        // Harus ada tombol disabled Cut-Off (Data Belum Tersedia)
        $response->assertSee('Cut-Off (Data Belum Tersedia)');
        $response->assertSee('Belum Ada Data Jatah');
        $response->assertSee('Salin Jatah Sebelumnya');
        $response->assertSee('is-sirkulasi-view', false);
    }

    public function test_copy_jatah_renders_luxurious_empty_state_without_double_icons(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $targetDate = Carbon::tomorrow()->addDays(6)->toDateString();

        Cache::forget('jatah_harian_' . $targetDate);

        $response = $this->actingAs($user)->get(route('sirkulasi.jatah.copy', [
            'target_tanggal' => $targetDate,
            'sumber_tanggal' => Carbon::today()->toDateString(),
        ]));

        $response->assertStatus(200);
        $response->assertSee('Data Jatah Belum Disalin');
        $response->assertSee('Salin Jatah Sekarang');
        $response->assertSee('is-sirkulasi-view', false);
    }

    public function test_semua_halaman_sirkulasi_menggunakan_kelas_header_tabel_putih(): void
    {
        $user = User::factory()->create(['role' => 'sirkulasi']);
        $routes = [
            'sirkulasi.dashboard',
            'sirkulasi.jatah.edit',
            'sirkulasi.jatah.copy',
            'sirkulasi.jatah.tutup',
            'sirkulasi.jatah.hitung',
            'sirkulasi.cetak.pemasaran',
            'sirkulasi.cetak.penjualan',
            'sirkulasi.cetak.nota-distribusi',
            'sirkulasi.cetak.alamat-agen',
            'sirkulasi.memo.history',
            'sirkulasi.tabel.agen',
            'sirkulasi.tabel.jenis-agen',
            'sirkulasi.tabel.wilayah',
            'sirkulasi.tabel.rute',
            'sirkulasi.tabel.pengepakan',
            'sirkulasi.pengaturan.index',
        ];

        foreach ($routes as $routeName) {
            $resp = $this->actingAs($user)->get(route($routeName));
            $resp->assertStatus(200);
            $resp->assertSee('is-sirkulasi-view', false);
            $resp->assertSee('body.is-sirkulasi-view main.is-sirkulasi-view', false);
            $resp->assertSee('color: #FFFFFF !important;', false);
        }
    }

    public function test_nota_distribusi_renders_luxurious_empty_state(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $targetDate = Carbon::tomorrow()->addDays(7)->toDateString();

        Cache::forget('jatah_harian_' . $targetDate);

        $response = $this->actingAs($user)->get(route('sirkulasi.cetak.nota-distribusi', [
            'tanggal' => $targetDate,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Nota Distribusi Belum Siap Dicetak');
        $response->assertSee('Salin Data Jatah');
        $response->assertSee('Input Oplah Harian');
    }

    public function test_cutoff_harian_succeeds_when_jatah_data_exists(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $targetDate = '2026-11-28';

        // Simulasikan data jatah hasil copy / edit
        Cache::put('jatah_harian_' . $targetDate, [
            1 => [
                'langganan' => 50,
                'konsinyasi' => 20,
                'gratis' => 5,
                'pengenalan' => 0,
                'insidentil' => 0,
            ],
        ], now()->addDays(7));

        $response = $this->actingAs($user)->post(route('sirkulasi.jatah.tutup.lock'), [
            'mode' => 'harian',
            'tanggal' => $targetDate,
            'action' => 'lock',
        ]);

        $response->assertRedirect(route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $targetDate]));
        $response->assertSessionHas('success');
        $this->assertTrue(Cache::get('locked_harian_' . $targetDate, false));
    }
}
