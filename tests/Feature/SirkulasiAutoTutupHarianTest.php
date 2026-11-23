<?php

namespace Tests\Feature;

use App\Models\DailyCutoff;
use App\Models\MemoUnlock;
use App\Models\SistemConfig;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SirkulasiAutoTutupHarianTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        SistemConfig::set('sirkulasi_jam_cutoff', '17:00');
    }

    public function test_sistem_config_sirkulasi_cutoff_helpers(): void
    {
        $this->assertEquals('17:00', SistemConfig::getJamCutoffSirkulasi());
    }

    public function test_auto_tutup_command_executes_with_force_or_date(): void
    {
        $testDate = '2026-11-20';

        // Pastikan belum terkunci
        Cache::forget('locked_harian_' . $testDate);
        DailyCutoff::whereDate('tanggal_edisi', $testDate)->delete();

        // Jalankan artisan command
        $this->artisan('sirkulasi:auto-tutup-harian', [
            '--date' => $testDate,
            '--force' => true,
        ])->assertExitCode(0);

        // Verifikasi terkunci di Cache
        $this->assertTrue(Cache::get('locked_harian_' . $testDate, false));

        // Verifikasi tersimpan di tabel daily_cutoffs
        $cutoff = DailyCutoff::whereDate('tanggal_edisi', $testDate)->first();
        $this->assertNotNull($cutoff);
        $this->assertTrue($cutoff->is_closed);
        $this->assertStringContainsString('Otomatis', $cutoff->catatan);

        // Cleanup
        Cache::forget('locked_harian_' . $testDate);
        DailyCutoff::whereDate('tanggal_edisi', $testDate)->delete();
    }

    public function test_auto_tutup_skips_when_memo_unlock_is_approved(): void
    {
        $testDate = '2026-11-21';

        Cache::forget('locked_harian_' . $testDate);
        DailyCutoff::whereDate('tanggal_edisi', $testDate)->delete();

        // Buat memo unlock OPEN & APPROVED
        MemoUnlock::create([
            'tanggal_target'      => $testDate,
            'nomor_memo'          => 'REQ-ULK-TEST-001',
            'kategori_urgensi'    => 'Penjualan Susulan',
            'nama_pemasang'       => 'Sirkulasi',
            'alasan'              => 'Koreksi penjualan koran susulan agen',
            'tujuan_divisi'       => 'sirkulasi',
            'dibuka_oleh_user_id' => 1,
            'status'              => 'OPEN',
            'status_akuntansi'    => 'APPROVED',
        ]);

        $this->artisan('sirkulasi:auto-tutup-harian', [
            '--date' => $testDate,
            '--force' => true,
        ])->assertExitCode(0);

        // Harus tetap tidak terkunci karena ada memo unlock APPROVED
        $this->assertFalse(Cache::get('locked_harian_' . $testDate, false));

        // Cleanup
        MemoUnlock::where('nomor_memo', 'REQ-ULK-TEST-001')->delete();
    }

    public function test_admin_can_update_cutoff_time_dynamically(): void
    {
        $user = User::first() ?? User::factory()->create();

        // 0. Test render halaman pengaturan
        $this->actingAs($user)->get(route('sirkulasi.pengaturan.index'))->assertOk();

        // 1. Test update cepat lewat endpoint update-cutoff
        $response = $this->actingAs($user)->post(route('sirkulasi.pengaturan.update-cutoff'), [
            'sirkulasi_jam_cutoff' => '18:30',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('18:30', SistemConfig::getJamCutoffSirkulasi());

        // 2. Test update via menu utama Pengaturan Tarif
        $responsePengaturan = $this->actingAs($user)->post(route('sirkulasi.pengaturan.update'), [
            'sirkulasi_harga_eceran'       => '3.000',
            'sirkulasi_harga_agen_besar'   => '2.400',
            'sirkulasi_harga_langganan'    => '2.700',
            'sirkulasi_harga_cover_het'    => '4.000',
            'sirkulasi_isi_bendel'         => '50',
            'sirkulasi_jam_cutoff'         => '19:00',
            'sirkulasi_batas_retur_persen' => '5.0',
            'sirkulasi_rasio_langganan'    => '60',
            'sirkulasi_rasio_konsinyasi'   => '35',
            'sirkulasi_rasio_gratis'       => '5',
        ]);

        $responsePengaturan->assertRedirect(route('sirkulasi.pengaturan.index'));
        $responsePengaturan->assertSessionHas('success');
        $this->assertEquals('19:00', SistemConfig::getJamCutoffSirkulasi());

        // Kembalikan ke 17:00
        SistemConfig::set('sirkulasi_jam_cutoff', '17:00');
    }

    public function test_oplah_edit_rejected_when_target_date_is_past_cutoff_or_locked(): void
    {
        $user = User::first() ?? User::factory()->create();
        $pastDate = Carbon::yesterday()->toDateString();

        $response = $this->actingAs($user)->post(route('sirkulasi.jatah.edit.store'), [
            'tanggal_target' => $pastDate,
            'jatah' => [
                1 => ['langganan' => 100, 'konsinyasi' => 50, 'gratis' => 5, 'pengenalan' => 0, 'insidentil' => 0],
            ],
        ]);

        $response->assertRedirect(route('sirkulasi.jatah.edit', ['tanggal' => $pastDate]));
        $response->assertSessionHas('error');
    }

    public function test_oplah_edit_allowed_when_target_date_is_not_cutoff(): void
    {
        $user = User::first() ?? User::factory()->create();
        $futureDate = Carbon::tomorrow()->toDateString();

        // Pastikan belum terkunci
        Cache::forget('locked_harian_' . $futureDate);
        DailyCutoff::whereDate('tanggal_edisi', $futureDate)->delete();

        $response = $this->actingAs($user)->post(route('sirkulasi.jatah.edit.store'), [
            'tanggal_target' => $futureDate,
            'jatah' => [
                1 => ['langganan' => 120, 'konsinyasi' => 60, 'gratis' => 10, 'pengenalan' => 0, 'insidentil' => 0],
            ],
        ]);

        $response->assertRedirect(route('sirkulasi.jatah.edit', ['tanggal' => $futureDate]));
        $response->assertSessionHas('success');

        $saved = Cache::get('jatah_harian_' . $futureDate, []);
        $this->assertEquals(120, $saved[1]['langganan']);
        $this->assertEquals(60, $saved[1]['konsinyasi']);

        // Cleanup
        Cache::forget('jatah_harian_' . $futureDate);
    }

    public function test_memo_lifecycle_submission_rejection_and_approval_flow(): void
    {
        $user = User::first() ?? User::factory()->create();
        $targetDate = Carbon::yesterday()->toDateString();

        // 1. Sirkulasi ajukan memo buka data
        $responseSubmit = $this->actingAs($user)->post(route('sirkulasi.jatah.tutup.request-unlock'), [
            'mode'               => 'harian',
            'tanggal'            => $targetDate,
            'kategori_alasan'    => 'Penjualan Baru / Susulan Masuk',
            'estimasi_eksemplar' => 75,
            'estimasi_nominal'   => 225000,
            'alasan'             => 'Permohonan susulan oplah 75 eks untuk agen Ciamis',
        ]);
        $responseSubmit->assertSessionHas('success');

        $memo = MemoUnlock::whereDate('tanggal_target', $targetDate)
            ->where('tujuan_divisi', 'sirkulasi')
            ->latest('id')
            ->first();
        $this->assertNotNull($memo);
        $this->assertEquals('PENDING', $memo->status_akuntansi);
        $this->assertEquals('CLOSED', $memo->status);

        // Oplah masih terkunci karena status masih PENDING
        $this->actingAs($user)->post(route('sirkulasi.jatah.edit.store'), [
            'tanggal_target' => $targetDate,
            'jatah' => [1 => ['langganan' => 10, 'konsinyasi' => 5, 'gratis' => 0, 'pengenalan' => 0, 'insidentil' => 0]],
        ])->assertSessionHas('error');

        // 2. Akuntansi menolak memo pertama
        $responseReject = $this->actingAs($user)->post(route('accounting.sirkulasi.buka-data.proses'), [
            'memo_id'          => $memo->id,
            'action'           => 'reject',
            'alasan_penolakan' => 'Data retur belum disetor fisik ke gudang.',
        ]);
        $responseReject->assertSessionHas('success');

        $memo->refresh();
        $this->assertEquals('REJECTED', $memo->status_akuntansi);
        $this->assertEquals('Data retur belum disetor fisik ke gudang.', $memo->catatan_akuntansi);
        $this->assertNotNull($memo->ditolak_pada);

        // 3. Akuntansi menyetujui memo
        $responseApprove = $this->actingAs($user)->post(route('accounting.sirkulasi.buka-data.proses'), [
            'memo_id' => $memo->id,
            'alasan'  => 'Disetujui setelah diverifikasi Kabag Akuntansi.',
        ]);
        $responseApprove->assertSessionHas('success');

        $memo->refresh();
        $this->assertEquals('APPROVED', $memo->status_akuntansi);
        $this->assertEquals('OPEN', $memo->status);
        $this->assertNotNull($memo->diterima_akuntansi_pada);

        // 4. Test Cetak Memo Resmi
        $this->actingAs($user)->get(route('sirkulasi.jatah.memo.cetak', $memo->id))->assertOk();
        $this->actingAs($user)->get(route('accounting.memo.cetak', $memo->id))->assertOk();

        // Cleanup
        $memo->delete();
    }

    public function test_early_cutoff_in_iklan_locks_target_edition(): void
    {
        $user = User::first() ?? User::factory()->create();
        $targetEdisi = Carbon::tomorrow()->addDays(2)->toDateString();

        // 1. Eksekusi Cut-off Sekarang di Iklan
        $response = $this->actingAs($user)->post(route('iklan.cutoff.tutup'), [
            'tanggal_edisi' => $targetEdisi,
            'catatan'       => 'Cut-off lebih awal selesai dummy cetak.',
        ]);
        $response->assertSessionHas('success');

        $this->assertTrue(DailyCutoff::isDateLocked($targetEdisi));

        // Cleanup
        DailyCutoff::whereDate('tanggal_edisi', $targetEdisi)->delete();
    }
}
