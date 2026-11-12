<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TransaksiKoran;
use App\Models\KoreksiPiutang;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingKoreksiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $faktur;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->user = User::where('role', 'accounting')->firstOrFail();
        $this->faktur = TransaksiKoran::where('piutang_transaksikoran', '>', 0)->firstOrFail();
    }

    public function test_koreksi_page_renders_with_table_and_modal(): void
    {
        $response = $this->actingAs($this->user)->get(route('accounting.iklan.koreksi'));

        $response->assertStatus(200);
        $response->assertSee('Koreksi Piutang Iklan');
        $response->assertSee('Terbitkan Koreksi (CN)');
        // Pastikan tabel ada
        $response->assertSee('Riwayat Nota Kredit');
        // Pastikan modal ada
        $response->assertSee('Terbitkan Nota Kredit');
    }

    public function test_store_koreksi_piutang_reduces_outstanding(): void
    {
        $initialPiutang = (float)$this->faktur->piutang_transaksikoran;
        $koreksiNominal = min(50000, $initialPiutang);

        $response = $this->actingAs($this->user)->post(route('accounting.iklan.koreksi.store'), [
            'no_faktur' => $this->faktur->nofakturkoran,
            'tanggal' => now()->toDateString(),
            'jumlah_koreksi' => number_format($koreksiNominal, 0, ',', '.'),
            'keterangan' => 'Diskon khusus pimpinan',
        ]);

        $response->assertRedirect(route('accounting.iklan.koreksi'));
        $response->assertSessionHas('success');

        $this->faktur->refresh();
        $this->assertEquals($initialPiutang - $koreksiNominal, (float)$this->faktur->piutang_transaksikoran);

        $this->assertDatabaseHas('koreksi_piutang', [
            'no_faktur' => $this->faktur->nofakturkoran,
            'jumlah_koreksi' => $koreksiNominal,
        ]);
    }

    public function test_store_koreksi_cannot_exceed_outstanding(): void
    {
        $initialPiutang = (float)$this->faktur->piutang_transaksikoran;

        $response = $this->actingAs($this->user)
            ->from(route('accounting.iklan.koreksi'))
            ->post(route('accounting.iklan.koreksi.store'), [
                'no_faktur' => $this->faktur->nofakturkoran,
                'tanggal' => now()->toDateString(),
                'jumlah_koreksi' => $initialPiutang + 1000000,
                'keterangan' => 'Koreksi kebesaran melebihi sisa piutang',
            ]);

        $response->assertRedirect(route('accounting.iklan.koreksi'));
        $response->assertSessionHas('error');

        $this->faktur->refresh();
        $this->assertEquals($initialPiutang, (float)$this->faktur->piutang_transaksikoran);
    }
}
