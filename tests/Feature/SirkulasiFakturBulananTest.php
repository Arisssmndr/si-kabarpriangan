<?php

namespace Tests\Feature;

use App\Models\Agen;
use App\Models\Faktur;
use App\Models\SirkulasiFakturBulanan;
use App\Models\SirkulasiRetur;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SirkulasiFakturBulananTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $agen;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->user = User::where('role', 'accounting')->first() ?? User::first();
        $this->agen = Agen::where('status', 'aktif')->first() ?? Agen::first();
    }

    public function test_faktur_bulanan_page_renders_cleanly(): void
    {
        $response = $this->actingAs($this->user)->get(route('accounting.sirkulasi.faktur-bulanan'));

        $response->assertStatus(200);
        $response->assertSee('Faktur Sirkulasi Bulanan');
        $response->assertSee('Oplah Kirim Gross');
        $response->assertSee('Retur Fisik Disetujui');
        $response->assertSee('Total Tagihan Bersih');
        $response->assertSee('Input Retur (BAPR)');
        $response->assertSee('Generate Faktur Bulanan');
    }

    public function test_generate_faktur_bulanan_creates_consolidated_invoices(): void
    {
        $periode = now()->format('Y-m');

        $response = $this->actingAs($this->user)->post(route('accounting.sirkulasi.faktur-bulanan.generate'), [
            'bulan' => $periode,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $fakturBulanan = SirkulasiFakturBulanan::where('periode_bulan', $periode)->first();
        $this->assertNotNull($fakturBulanan);
        $this->assertStringStartsWith('FAK-SRK-', $fakturBulanan->no_faktur);
        $this->assertGreaterThan(0, $fakturBulanan->total_oplah_kirim);
        $this->assertGreaterThan(0, $fakturBulanan->total_tagihan_bersih);

        // Pastikan tersinkron ke master Faktur
        $fMaster = Faktur::where('no_faktur', $fakturBulanan->no_faktur)->first();
        $this->assertNotNull($fMaster);
        $this->assertEquals('sirkulasi', $fMaster->kategori);
        $this->assertEquals('bulanan', $fMaster->tipe_faktur);
    }

    public function test_verifikasi_retur_bapr_deducts_net_billing(): void
    {
        $periode = now()->format('Y-m');

        // Generate faktur dulu
        $this->actingAs($this->user)->post(route('accounting.sirkulasi.faktur-bulanan.generate'), [
            'bulan' => $periode,
        ]);

        $faktur = SirkulasiFakturBulanan::where('periode_bulan', $periode)
            ->where('agen_id', $this->agen->id)
            ->first();

        $this->assertNotNull($faktur);
        $tagihanKotorAwal = (float) $faktur->total_tagihan_kotor;

        // Input verifikasi retur fisik 10 eks
        $response = $this->actingAs($this->user)->post(route('accounting.sirkulasi.retur.store'), [
            'periode_bulan' => $periode,
            'agen_id' => $this->agen->id,
            'tanggal_retur' => now()->toDateString(),
            'jumlah_retur_disetujui' => 10,
            'catatan_kondisi' => 'Koran sisa edisi reguler, kondisi utuh.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Cek tabel retur
        $retur = SirkulasiRetur::where('periode_bulan', $periode)
            ->where('agen_id', $this->agen->id)
            ->first();

        $this->assertNotNull($retur);
        $this->assertStringStartsWith('BAPR', $retur->no_bapr);
        $this->assertEquals(10, $retur->jumlah_retur_disetujui);

        // Cek update pada Faktur Bulanan
        $faktur->refresh();
        $this->assertEquals(10, $faktur->total_retur_eks);
        $this->assertEquals($faktur->total_oplah_kirim - 10, $faktur->oplah_bersih);
        $this->assertEquals(10 * $faktur->tarif_per_eks, (float) $faktur->total_potongan_retur);
        $this->assertEquals($tagihanKotorAwal - (10 * $faktur->tarif_per_eks), (float) $faktur->total_tagihan_bersih);
    }

    public function test_cetak_faktur_bulanan_renders_successfully(): void
    {
        $periode = now()->format('Y-m');

        // Generate faktur
        $this->actingAs($this->user)->post(route('accounting.sirkulasi.faktur-bulanan.generate'), [
            'bulan' => $periode,
        ]);

        $faktur = SirkulasiFakturBulanan::where('periode_bulan', $periode)->first();
        $this->assertNotNull($faktur);

        $response = $this->actingAs($this->user)->get(route('accounting.sirkulasi.faktur-bulanan.cetak', $faktur->id));

        $response->assertStatus(200);
        $response->assertSee('FAKTUR SIRKULASI BULANAN');
        $response->assertSee($faktur->no_faktur);
        $response->assertSee('Statement of Daily Deliveries');
        $response->assertSee('PT BERKAH PIKIRAN RAKYAT');
    }

    public function test_kasir_can_settle_consolidated_monthly_invoice(): void
    {
        $periode = now()->format('Y-m');

        // 1. Generate faktur bulanan
        $this->actingAs($this->user)->post(route('accounting.sirkulasi.faktur-bulanan.generate'), [
            'bulan' => $periode,
        ]);

        $faktur = SirkulasiFakturBulanan::where('periode_bulan', $periode)
            ->where('agen_id', $this->agen->id)
            ->first();
        $this->assertNotNull($faktur);

        // 2. Login as kasir
        $kasirUser = User::where('role', 'kasir')->first() ?? $this->user;

        // Ensure active shift exists
        $shift = \App\Models\ShiftKasir::where('kasir_id', $kasirUser->id)->where('status', 'open')->first();
        if (!$shift) {
            $shift = \App\Models\ShiftKasir::create([
                'kasir_id' => $kasirUser->id,
                'saldo_awal' => 500000,
                'status' => 'open',
                'waktu_buka' => now(),
            ]);
        }

        $nominalBayar = (float) $faktur->total_tagihan_bersih;

        // 3. Setor kasir dengan no_faktur
        $response = $this->actingAs($kasirUser)->post(route('kasir.penerimaan.sirkulasi.store'), [
            'agen_id' => $this->agen->id,
            'no_faktur' => $faktur->no_faktur,
            'nominal_setoran' => $nominalBayar,
            'metode_pembayaran' => 'tunai',
            'periode' => $periode,
        ]);

        $response->assertRedirect();

        // 4. Pastikan Faktur Bulanan terupdate menjadi lunas
        $faktur->refresh();
        $this->assertEquals($nominalBayar, (float) $faktur->total_terbayar);
        $this->assertEquals(0, (float) $faktur->sisa_piutang);
        $this->assertEquals('lunas', $faktur->status_pembayaran);

        // 5. Pastikan master faktur juga lunas
        $fMaster = Faktur::where('no_faktur', $faktur->no_faktur)->first();
        $this->assertNotNull($fMaster);
        $this->assertEquals(0, (float) $fMaster->sisa_piutang);
        $this->assertEquals('lunas', $fMaster->status);
    }
}
