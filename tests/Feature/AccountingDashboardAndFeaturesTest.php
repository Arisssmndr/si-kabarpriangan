<?php

namespace Tests\Feature;

use App\Models\Agen;
use App\Models\AkunPerkiraan;
use App\Models\AuditLogAkuntansi;
use App\Models\JurnalUmum;
use App\Models\KasKeluar;
use App\Models\MemoUnlock;
use App\Models\PeriodeAkuntansi;
use App\Models\SistemConfig;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AccountingDashboardAndFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->user = User::where('role', 'accounting')->firstOrFail();
    }

    public function test_dashboard_accounting_renders_with_charts_and_real_data(): void
    {
        $response = $this->actingAs($this->user)->get(route('accounting.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Akuntansi');
        $response->assertSee('accountingFlowChart');
        $response->assertSee('Komposisi Pendapatan Perusahaan');
        $response->assertSee('Laba Operasional Riil');
        $response->assertSee('revenueShareChart');
        $response->assertSee('Kalender Pembukuan');
        $response->assertSee('Jurnal Umum Terbaru');
        $response->assertDontSee('Performa Sales / AE');
    }

    public function test_sirkulasi_hitung_penjualan_uses_dynamic_sistem_config_and_7_segment_coa(): void
    {
        SistemConfig::set('sirkulasi_harga_agen_besar', 2800, 'test');
        SistemConfig::set('sirkulasi_harga_eceran', 3000, 'test');
        SistemConfig::set('sirkulasi_harga_langganan', 3000, 'test');

        $bulan = now()->format('Y-m');
        $resGet = $this->actingAs($this->user)->get(route('accounting.sirkulasi.hitung-penjualan', ['bulan' => $bulan]));
        $resGet->assertStatus(200);

        $resPost = $this->actingAs($this->user)->post(route('accounting.sirkulasi.hitung-penjualan.proses'), [
            'bulan' => $bulan,
        ]);
        $resPost->assertSessionHas('success');

        $jurnal = JurnalUmum::where('referensi', 'SIRK-' . $bulan)->first();
        $this->assertNotNull($jurnal);
        $this->assertEquals((float) $jurnal->total_debet, (float) $jurnal->total_kredit);

        $this->assertDatabaseHas('jurnal_umum_detail', [
            'jurnal_umum_id' => $jurnal->id,
            'kode_perkiraan' => '1.00.04.01.01.00.00002',
        ]);
        $this->assertDatabaseHas('jurnal_umum_detail', [
            'jurnal_umum_id' => $jurnal->id,
            'kode_perkiraan' => '4.00.04.01.01.00.00001',
        ]);
    }

    public function test_aset_tetap_crud_and_monthly_depreciation_posting(): void
    {
        $bulan = now()->format('Y-m');

        // Store new asset
        $resStore = $this->actingAs($this->user)->post(route('accounting.beban.aset-tetap.store'), [
            'nama'          => 'Unit Kamera Liputan 4K',
            'kategori'      => 'Peralatan Kantor',
            'tgl_perolehan' => now()->toDateString(),
            'harga_beli'    => 24000000,
            'tarif_susut'   => 25,
        ]);
        $resStore->assertSessionHas('success');

        $page = $this->actingAs($this->user)->get(route('accounting.beban.aset-tetap', ['bulan' => $bulan]));
        $page->assertStatus(200);
        $page->assertSee('Unit Kamera Liputan 4K');

        // Post monthly depreciation to GL
        $resDep = $this->actingAs($this->user)->post(route('accounting.beban.aset-tetap.penyusutan'), [
            'bulan' => $bulan,
        ]);
        $resDep->assertSessionHas('success');

        $jurnal = JurnalUmum::where('referensi', 'SUSUT-' . $bulan)->first();
        $this->assertNotNull($jurnal);
        $this->assertDatabaseHas('jurnal_umum_detail', [
            'jurnal_umum_id' => $jurnal->id,
            'kode_perkiraan' => '6.00.22.01.01.00.00001',
        ]);
        $this->assertDatabaseHas('jurnal_umum_detail', [
            'jurnal_umum_id' => $jurnal->id,
            'kode_perkiraan' => '1.00.11.01.01.00.00001',
        ]);

        // Duplicate depreciation in the same month must be rejected
        $resDup = $this->actingAs($this->user)->post(route('accounting.beban.aset-tetap.penyusutan'), [
            'bulan' => $bulan,
        ]);
        $resDup->assertSessionHas('error');

        // Delete newly added asset AST-005
        $resDel = $this->actingAs($this->user)->delete(route('accounting.beban.aset-tetap.destroy', 'AST-005'));
        $resDel->assertSessionHas('success');
    }

    public function test_coa_csv_import_and_filter(): void
    {
        $csv = "kode_perkiraan,nama_perkiraan,kepala,kategori,saldo_normal\n"
             . "6.00.99.01.01.00.00001,Beban Listrik Redaksi,6,Beban Operasional,debet\n"
             . "4.00.99.01.01.00.00001,Pendapatan Event Off-Print,4,Pendapatan Usaha,kredit\n";

        $file = UploadedFile::fake()->createWithContent('coa.csv', $csv);

        $response = $this->actingAs($this->user)->post(route('accounting.pengaturan.coa.import'), [
            'file' => $file,
        ]);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('akun_perkiraan', [
            'kode_perkiraan' => '6.00.99.01.01.00.00001',
            'nama_perkiraan' => 'Beban Listrik Redaksi',
        ]);
        $this->assertDatabaseHas('audit_log_akuntansi', [
            'aksi'  => 'IMPORT_COA',
            'modul' => 'MASTER',
        ]);

        $resFilter = $this->actingAs($this->user)->get(route('accounting.pengaturan.coa', ['q' => 'Listrik', 'kepala' => '6']));
        $resFilter->assertStatus(200);
        $resFilter->assertSee('Beban Listrik Redaksi');
    }

    public function test_tutup_buku_lock_and_controlled_unlock(): void
    {
        $periodeKey = '2026-10';

        $resClose = $this->actingAs($this->user)->post(route('accounting.pengaturan.tutup-buku.proses'), [
            'periode_key' => $periodeKey,
        ]);
        $resClose->assertSessionHas('success');
        $this->assertTrue(PeriodeAkuntansi::isPeriodeLocked($periodeKey));

        $resOpen = $this->actingAs($this->user)->post(route('accounting.pengaturan.tutup-buku.buka'), [
            'periode_key' => $periodeKey,
            'alasan'      => 'Revisi bukti kas keluar operasional akhir bulan',
        ]);
        $resOpen->assertSessionHas('success');
        $this->assertFalse(PeriodeAkuntansi::isPeriodeLocked($periodeKey));

        $this->assertDatabaseHas('audit_log_akuntansi', [
            'aksi'  => 'BUKA_PERIODE',
            'modul' => 'PERIODE',
        ]);
    }

    public function test_sirkulasi_daily_memo_unlock_approval_and_beban_double_post_guard(): void
    {
        $memo = MemoUnlock::create([
            'nomor_memo'          => 'MEMO/SIRK/DAILY/001',
            'tanggal_target'      => now()->toDateString(),
            'periode'             => now()->toDateString(),
            'alasan'              => 'Koreksi jatah agen Cipatujah',
            'status'              => 'CLOSED',
            'status_akuntansi'    => 'PENDING',
            'dibuka_oleh_user_id' => $this->user->id,
            'tujuan_divisi'       => 'sirkulasi',
        ]);

        $resApprove = $this->actingAs($this->user)->post(route('accounting.sirkulasi.buka-data.proses'), [
            'memo_id' => $memo->id,
            'alasan'  => 'Disetujui koreksi harian',
        ]);
        $resApprove->assertSessionHas('success');
        $memo->refresh();
        $this->assertEquals('APPROVED', $memo->status_akuntansi);

        $bkk = KasKeluar::create([
            'no_bkk'               => 'BKK-TEST-001',
            'tanggal_keluar'       => now()->toDateString(),
            'diberikan_kepada'     => 'PLN Tasikmalaya',
            'keterangan'           => 'Token Listrik Redaksi',
            'nominal_keluar'       => 500000,
            'is_pulled_accounting' => false,
            'status_void'          => false,
            'user_id'              => $this->user->id,
        ]);

        $resPost1 = $this->actingAs($this->user)->post(route('accounting.beban.verifikasi.post', $bkk->id), [
            'kode_perkiraan' => '6.00.22.01.01.00.00001',
        ]);
        $resPost1->assertSessionHas('success');

        $resPost2 = $this->actingAs($this->user)->post(route('accounting.beban.verifikasi.post', $bkk->id), [
            'kode_perkiraan' => '6.00.22.01.01.00.00001',
        ]);
        $resPost2->assertSessionHas('error');
    }

    public function test_all_accounting_pages_render_status_200(): void
    {
        $routes = [
            'accounting.dashboard',
            'accounting.iklan.hitung-penjualan',
            'accounting.iklan.hitung-pembayaran',
            'accounting.iklan.koreksi',
            'accounting.iklan.buka-data',
            'accounting.iklan.belanja',
            'accounting.sirkulasi.saldo-awal',
            'accounting.sirkulasi.hitung-penjualan',
            'accounting.sirkulasi.hitung-pembayaran',
            'accounting.sirkulasi.koreksi',
            'accounting.sirkulasi.buka-data',
            'accounting.cetak.iklan',
            'accounting.cetak.sirkulasi',
            'accounting.beban.verifikasi',
            'accounting.beban.mutasi-bank',
            'accounting.beban.akrual',
            'accounting.beban.aset-tetap',
            'accounting.beban.anggaran',
            'accounting.gl.jurnal',
            'accounting.gl.memorial',
            'accounting.gl.buku-besar',
            'accounting.gl.neraca-lajur',
            'accounting.laporan.laba-rugi',
            'accounting.laporan.neraca',
            'accounting.laporan.arus-kas',
            'accounting.laporan.ekuitas',
            'accounting.laporan.rincian-beban',
            'accounting.pengaturan.coa',
            'accounting.pengaturan.tutup-buku',
            'accounting.pengaturan.audit-log',
        ];

        foreach ($routes as $name) {
            $res = $this->actingAs($this->user)->get(route($name));
            $res->assertStatus(200);
        }
    }

    public function test_master_data_agen_permission_accounting_can_crud_and_sirkulasi_is_readonly(): void
    {
        // 1. Staf Akuntansi: Full CRUD
        $accRes = $this->actingAs($this->user)->get(route('sirkulasi.tabel.agen'));
        $accRes->assertStatus(200);
        $accRes->assertSee('Tambah Agen');

        // Test create agen by accounting
        $uniqueCode = 'AG-11-UJI' . rand(100, 999);
        $storeRes = $this->actingAs($this->user)->post(route('sirkulasi.tabel.agen.store'), [
            'kode_agen'      => $uniqueCode,
            'nama_agen'      => 'Agen Uji Coba Akuntansi',
            'kategori'       => '11',
            'wilayah_kode'   => '01',
            'jurusan_kode'   => '01',
            'alamat'         => 'Jl. Uji Akuntansi No. 1',
            'telepon'        => '081234567890',
            'jatah_default'  => 50,
            'plafon_piutang' => 1000000,
        ]);
        $storeRes->assertRedirect(route('sirkulasi.tabel.agen'));
        $storeRes->assertSessionHas('success');

        // 2. Staf Sirkulasi: Read-Only
        $sirkulasiUser = User::where('role', 'sirkulasi')->first() ?? User::factory()->create([
            'role' => 'sirkulasi',
            'password' => bcrypt('password'),
        ]);

        $sirkRes = $this->actingAs($sirkulasiUser)->get(route('sirkulasi.tabel.agen'));
        $sirkRes->assertStatus(200);
        $sirkRes->assertDontSee('Tambah Agen');
        $sirkRes->assertSee('Mode Baca (Sirkulasi)');

        // Test block create agen by sirkulasi
        $blockStore = $this->actingAs($sirkulasiUser)->post(route('sirkulasi.tabel.agen.store'), [
            'kode_agen'      => 'AG-11-ILEGAL',
            'nama_agen'      => 'Agen Ilegal Sirkulasi',
            'kategori'       => '11',
            'wilayah_kode'   => '01',
            'jurusan_kode'   => '01',
            'alamat'         => 'Jl. Ilegal',
            'jatah_default'  => 50,
        ]);
        $blockStore->assertRedirect(route('sirkulasi.tabel.agen'));
        $blockStore->assertSessionHas('error');
    }
}

