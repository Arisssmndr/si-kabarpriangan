<?php

namespace Tests\Feature;

use App\Models\MemoUnlock;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemoHistoryPagesTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $sirkulasiUser;
    protected $iklanUser;
    protected $accountingUser;
    protected $kasirUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->adminUser = User::where('role', 'admin')->first() ?: User::factory()->create(['role' => 'admin']);
        $this->sirkulasiUser = User::where('role', 'sirkulasi')->first() ?: User::factory()->create(['role' => 'sirkulasi']);
        $this->iklanUser = User::where('role', 'iklan')->first() ?: User::factory()->create(['role' => 'iklan']);
        $this->accountingUser = User::where('role', 'accounting')->first() ?: User::factory()->create(['role' => 'accounting']);
        $this->kasirUser = User::where('role', 'kasir')->first() ?: User::factory()->create(['role' => 'kasir']);
    }

    public function test_sirkulasi_memo_history_renders_cleanly_and_filters(): void
    {
        $memo = MemoUnlock::create([
            'nomor_memo'          => 'MEMO/SRK/2026/11/0001',
            'tanggal_target'      => now()->toDateString(),
            'alasan'              => 'Perubahan oplah agen Indihiang',
            'tujuan_divisi'       => 'sirkulasi',
            'status_akuntansi'    => 'PENDING',
            'status'              => 'OPEN',
            'dibuka_oleh_user_id' => $this->sirkulasiUser->id,
        ]);

        $response = $this->actingAs($this->sirkulasiUser)->get(route('sirkulasi.memo.history'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Memo Sirkulasi');
        $response->assertSee('MEMO/SRK/2026/11/0001');
        $response->assertSee('Perubahan oplah agen Indihiang');
        $response->assertSee(route('sirkulasi.jatah.memo.cetak', $memo->id));
    }

    public function test_iklan_memo_history_renders_cleanly_and_filters(): void
    {
        $memo = MemoUnlock::create([
            'nomor_memo'          => 'MEMO/IKL/2026/11/0001',
            'tanggal_target'      => now()->toDateString(),
            'nama_pemasang'       => 'Bank BJB Tasikmalaya',
            'alasan'              => 'Iklan display duka cita susulan',
            'tujuan_divisi'       => 'iklan',
            'status_akuntansi'    => 'APPROVED',
            'status'              => 'OPEN',
            'dibuka_oleh_user_id' => $this->iklanUser->id,
        ]);

        $response = $this->actingAs($this->iklanUser)->get(route('iklan.memo.history'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Memo Iklan');
        $response->assertSee('MEMO/IKL/2026/11/0001');
        $response->assertSee('Bank BJB Tasikmalaya');
        $response->assertSee(route('iklan.cutoff.memo-unlock.cetak', $memo->id));
    }

    public function test_kasir_memo_history_renders_cleanly_and_filters(): void
    {
        $memo = MemoUnlock::create([
            'nomor_memo'          => 'MEMO/KAS/2026/11/0001',
            'tanggal_target'      => now()->toDateString(),
            'nama_pemasang'       => 'Shift Kasir #1 (Kasir Utama)',
            'alasan'              => 'Setoran susulan agen koran',
            'tujuan_divisi'       => 'kasir',
            'status_akuntansi'    => 'PENDING',
            'status'              => 'OPEN',
            'dibuka_oleh_user_id' => $this->kasirUser->id,
        ]);

        $response = $this->actingAs($this->kasirUser)->get(route('kasir.memo.history'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Memo Kasir');
        $response->assertSee('MEMO/KAS/2026/11/0001');
        $response->assertSee('Setoran susulan agen koran');
        $response->assertSee(route('kasir.memo.cetak', $memo->id));
    }

    public function test_accounting_memo_history_shows_both_divisions_and_filters(): void
    {
        MemoUnlock::create([
            'nomor_memo'          => 'MEMO/SRK/2026/11/0002',
            'tanggal_target'      => now()->toDateString(),
            'alasan'              => 'Oplah Singaparna',
            'tujuan_divisi'       => 'sirkulasi',
            'status_akuntansi'    => 'PENDING',
            'status'              => 'OPEN',
            'dibuka_oleh_user_id' => $this->sirkulasiUser->id,
        ]);

        MemoUnlock::create([
            'nomor_memo'          => 'MEMO/IKL/2026/11/0002',
            'tanggal_target'      => now()->toDateString(),
            'nama_pemasang'       => 'Diskominfo Kota',
            'alasan'              => 'Advertorial Pemda',
            'tujuan_divisi'       => 'iklan',
            'status_akuntansi'    => 'APPROVED',
            'status'              => 'OPEN',
            'dibuka_oleh_user_id' => $this->iklanUser->id,
        ]);

        MemoUnlock::create([
            'nomor_memo'          => 'MEMO/KAS/2026/11/0002',
            'tanggal_target'      => now()->toDateString(),
            'nama_pemasang'       => 'Shift Kasir #2',
            'alasan'              => 'Koreksi voucher kasir',
            'tujuan_divisi'       => 'kasir',
            'status_akuntansi'    => 'PENDING',
            'status'              => 'OPEN',
            'dibuka_oleh_user_id' => $this->kasirUser->id,
        ]);

        // 1. Semua Divisi
        $responseAll = $this->actingAs($this->accountingUser)->get(route('accounting.memo.history'));
        $responseAll->assertStatus(200);
        $responseAll->assertSee('Riwayat Seluruh Memo Buka Kunci');
        $responseAll->assertSee('MEMO/SRK/2026/11/0002');
        $responseAll->assertSee('MEMO/IKL/2026/11/0002');
        $responseAll->assertSee('MEMO/KAS/2026/11/0002');

        // 2. Filter Sirkulasi
        $responseSrk = $this->actingAs($this->accountingUser)->get(route('accounting.memo.history', ['divisi' => 'sirkulasi']));
        $responseSrk->assertStatus(200);
        $responseSrk->assertSee('MEMO/SRK/2026/11/0002');
        $responseSrk->assertDontSee('MEMO/IKL/2026/11/0002');
        $responseSrk->assertDontSee('MEMO/KAS/2026/11/0002');

        // 3. Filter Iklan
        $responseIkl = $this->actingAs($this->accountingUser)->get(route('accounting.memo.history', ['divisi' => 'iklan']));
        $responseIkl->assertStatus(200);
        $responseIkl->assertSee('MEMO/IKL/2026/11/0002');
        $responseIkl->assertDontSee('MEMO/SRK/2026/11/0002');
        $responseIkl->assertDontSee('MEMO/KAS/2026/11/0002');

        // 4. Filter Kasir
        $responseKas = $this->actingAs($this->accountingUser)->get(route('accounting.memo.history', ['divisi' => 'kasir']));
        $responseKas->assertStatus(200);
        $responseKas->assertSee('MEMO/KAS/2026/11/0002');
        $responseKas->assertDontSee('MEMO/SRK/2026/11/0002');
        $responseKas->assertDontSee('MEMO/IKL/2026/11/0002');
    }

    public function test_memo_print_pages_render_with_kop(): void
    {
        $memoSrk = MemoUnlock::create([
            'nomor_memo'          => 'MEMO/SRK/2026/11/0003',
            'tanggal_target'      => now()->toDateString(),
            'alasan'              => 'Revisi oplah',
            'tujuan_divisi'       => 'sirkulasi',
            'status_akuntansi'    => 'PENDING',
            'status'              => 'OPEN',
            'dibuka_oleh_user_id' => $this->sirkulasiUser->id,
        ]);

        $responseSrk = $this->actingAs($this->sirkulasiUser)->get(route('sirkulasi.jatah.memo.cetak', $memoSrk->id));
        $responseSrk->assertStatus(200);
        $responseSrk->assertSee('Harian Umum Kabar Priangan');
        $responseSrk->assertSee('MEMO/SRK/2026/11/0003');

        $memoAcc = MemoUnlock::create([
            'nomor_memo'          => 'MEMO/IKL/2026/11/0003',
            'tanggal_target'      => now()->toDateString(),
            'alasan'              => 'Iklan duka cita',
            'tujuan_divisi'       => 'iklan',
            'status_akuntansi'    => 'APPROVED',
            'status'              => 'OPEN',
            'dibuka_oleh_user_id' => $this->iklanUser->id,
        ]);

        $responseAcc = $this->actingAs($this->accountingUser)->get(route('accounting.memo.cetak', $memoAcc->id));
        $responseAcc->assertStatus(200);
        $responseAcc->assertSee('Harian Umum Kabar Priangan');
        $responseAcc->assertSee('MEMO/IKL/2026/11/0003');

        $memoKas = MemoUnlock::create([
            'nomor_memo'          => 'MEMO/KAS/2026/11/0003',
            'tanggal_target'      => now()->toDateString(),
            'alasan'              => 'Re-open shift kasir sore',
            'tujuan_divisi'       => 'kasir',
            'status_akuntansi'    => 'PENDING',
            'status'              => 'OPEN',
            'dibuka_oleh_user_id' => $this->kasirUser->id,
        ]);

        $responseKas = $this->actingAs($this->kasirUser)->get(route('kasir.memo.cetak', $memoKas->id));
        $responseKas->assertStatus(200);
        $responseKas->assertSee('Harian Umum Kabar Priangan');
        $responseKas->assertSee('MEMO/KAS/2026/11/0003');
    }

    public function test_kasir_shift_reopen_request_and_approval_syncs_with_memo_unlock(): void
    {
        $shift = \App\Models\ShiftKasir::create([
            'kasir_id'            => $this->kasirUser->id,
            'tanggal_shift'       => now()->toDateString(),
            'tgl_shift'           => now()->toDateString(),
            'saldo_awal_fisik'    => 1000000,
            'saldo_akhir_teoritis'=> 1000000,
            'saldo_akhir_fisik'   => 1000000,
            'status'              => 'closed',
            'closed_at'           => now(),
        ]);

        // 1. Kasir mengajukan buka kembali
        $response = $this->actingAs($this->kasirUser)->post(route('kasir.utilitas.buka-kas.ajukan', $shift->id), [
            'alasan' => 'Perlu input setoran susulan agen Ciamis',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('memo_unlocks', [
            'tujuan_divisi'    => 'kasir',
            'alasan'           => 'Perlu input setoran susulan agen Ciamis',
            'status_akuntansi' => 'PENDING',
        ]);

        // 2. Akuntansi / Admin menyetujui
        $responseAcc = $this->actingAs($this->accountingUser)->post(route('kasir.utilitas.buka-kas.setujui', $shift->id), [
            'catatan' => 'Disetujui untuk input susulan',
        ]);
        $responseAcc->assertRedirect();
        $responseAcc->assertSessionHas('success');

        $this->assertDatabaseHas('memo_unlocks', [
            'tujuan_divisi'    => 'kasir',
            'status_akuntansi' => 'APPROVED',
        ]);
    }
}
