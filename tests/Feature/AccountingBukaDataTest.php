<?php

namespace Tests\Feature;

use App\Models\MemoUnlock;
use App\Models\TransaksiKoran;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingBukaDataTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->user = User::where('role', 'accounting')->firstOrFail();
    }

    public function test_buka_data_page_renders_cleanly_with_modal(): void
    {
        $response = $this->actingAs($this->user)->get(route('accounting.iklan.buka-data'));

        $response->assertStatus(200);
        $response->assertSee('Buka Kunci Data Iklan');
        $response->assertSee('Otorisasi Buka Data');
        $response->assertSee('modalBukaData');
    }

    public function test_otorisasi_buka_data_creates_approved_memo(): void
    {
        $faktur = TransaksiKoran::first();

        $response = $this->actingAs($this->user)->post(route('accounting.iklan.proses-buka-data'), [
            'no_faktur'        => $faktur ? $faktur->nofakturkoran : 'FK-TEST-2026',
            'tanggal_target'   => now()->toDateString(),
            'alasan'           => 'Perubahan tarif atas persetujuan pimpinan',
            'kategori_urgensi' => 'MENDESAK',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('memo_unlocks', [
            'status_akuntansi' => 'APPROVED',
            'status'           => 'OPEN',
            'alasan'           => 'Perubahan tarif atas persetujuan pimpinan',
        ]);
    }

    public function test_approve_existing_memo(): void
    {
        $memo = MemoUnlock::create([
            'nomor_memo'          => 'MEMO/IKL/TEST/001',
            'tanggal_target'      => now()->toDateString(),
            'alasan'              => 'Pengajuan perubahan tanggal',
            'status'              => 'CLOSED',
            'status_akuntansi'    => 'PENDING',
            'dibuka_oleh_user_id' => $this->user->id,
            'tujuan_divisi'       => 'Divisi Akuntansi',
        ]);

        $response = $this->actingAs($this->user)->post(route('accounting.iklan.proses-buka-data'), [
            'memo_id' => $memo->id,
            'alasan'  => 'Disetujui untuk cetak ulang',
        ]);

        $response->assertSessionHas('success');
        $memo->refresh();
        $this->assertEquals('APPROVED', $memo->status_akuntansi);
        $this->assertEquals('OPEN', $memo->status);
    }
}

