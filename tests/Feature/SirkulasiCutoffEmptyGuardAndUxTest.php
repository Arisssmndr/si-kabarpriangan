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
        $response->assertSee('Alokasi Oplah Edisi Belum Tersedia');
        $response->assertSee('Salin Jatah Edisi Sebelumnya');
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
        $response->assertSee('Pola Alokasi Oplah Belum Diterapkan');
        $response->assertSee('Salin Pola Distribusi Sekarang');
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
