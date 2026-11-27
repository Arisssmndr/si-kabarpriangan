<?php

namespace Tests\Feature;

use App\Models\AccountingAccountMapping;
use App\Models\Agen;
use App\Models\AkunPerkiraan;
use App\Models\JurnalUmum;
use App\Models\JurnalUmumDetail;
use App\Models\KasMasuk;
use App\Models\PeriodeAkuntansi;
use App\Models\RekonsiliasiBank;
use App\Models\ShiftKasir;
use App\Models\SistemConfig;
use App\Models\User;
use App\Models\VendorTagihan;
use Database\Seeders\AkunPerkiraanSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingEnterpriseFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->seed(AkunPerkiraanSeeder::class);
        $this->user = User::where('role', 'accounting')->firstOrFail();
    }

    public function test_account_mapping_page_renders_and_updates_dynamic_coa_routing(): void
    {
        $res = $this->actingAs($this->user)->get(route('accounting.pengaturan.mapping'));
        $res->assertStatus(200);
        $res->assertSee('Mapping Akun COA Otomatis');

        $mapping = AccountingAccountMapping::where('mapping_key', 'bank_bjb')->firstOrFail();
        $targetCoa = '1.00.01.01.01.00.00001';

        $updateRes = $this->actingAs($this->user)->post(route('accounting.pengaturan.mapping.update'), [
            'mappings' => [
                $mapping->id => $targetCoa,
            ],
        ]);
        $updateRes->assertSessionHas('success');
        $this->assertEquals($targetCoa, $mapping->fresh()->kode_perkiraan);
    }

    public function test_sirkulasi_payment_splits_tunai_bca_and_bjb_accounts_in_gl(): void
    {
        $tgl = date('Y-m-d');

        KasMasuk::create([
            'no_kuitansi'          => 'BKM-TEST-TUNAI-01',
            'jenis_dokumen'        => 'BKM_KASIR',
            'tanggal_terima'       => $tgl,
            'tgl_transaksi'        => $tgl,
            'kode_bagian'          => '02',
            'tipe_transaksi'       => 'sirkulasi',
            'terima_dari'          => 'Agen Tunai',
            'nama_pembayar'        => 'Agen Tunai',
            'metode_bayar'         => 'tunai',
            'bank_tujuan'          => null,
            'jumlah_diterima'      => 200000,
            'jumlah_dibayar'       => 200000,
            'is_pulled_accounting' => false,
            'status_void'          => false,
            'user_id'              => $this->user->id,
        ]);

        KasMasuk::create([
            'no_kuitansi'          => 'BKM-TEST-BJB-02',
            'jenis_dokumen'        => 'BKM_KASIR',
            'tanggal_terima'       => $tgl,
            'tgl_transaksi'        => $tgl,
            'kode_bagian'          => '02',
            'tipe_transaksi'       => 'sirkulasi',
            'terima_dari'          => 'Agen BJB',
            'nama_pembayar'        => 'Agen BJB',
            'metode_bayar'         => 'transfer',
            'bank_tujuan'          => 'Bank BJB Giro',
            'jumlah_diterima'      => 300000,
            'jumlah_dibayar'       => 300000,
            'is_pulled_accounting' => false,
            'status_void'          => false,
            'user_id'              => $this->user->id,
        ]);

        // Tutup shift kasir aktif sesuai aturan SOP Closing Shift sebelum tarik pembayaran
        ShiftKasir::query()->update(['status' => 'closed']);

        $res = $this->actingAs($this->user)->post(route('accounting.sirkulasi.hitung-pembayaran.proses'), [
            'tanggal' => $tgl,
        ]);

        $res->assertSessionHas('success');

        $jurnal = JurnalUmum::where('referensi', "SETOR-SIRK-{$tgl}")->firstOrFail();
        $this->assertEquals(500000.0, (float) $jurnal->total_debet);
        $this->assertEquals(500000.0, (float) $jurnal->total_kredit);

        // Kas Operasional (tunai) = 200.000, Bank BJB = 300.000, Piutang Sirkulasi kredit = 500.000
        $this->assertDatabaseHas('jurnal_umum_detail', [
            'jurnal_umum_id' => $jurnal->id,
            'kode_perkiraan' => '1.00.00.01.01.00.00001',
            'debet'          => 200000,
        ]);
        $this->assertDatabaseHas('jurnal_umum_detail', [
            'jurnal_umum_id' => $jurnal->id,
            'kode_perkiraan' => '1.00.01.01.01.00.06270',
            'debet'          => 300000,
        ]);
        $this->assertDatabaseHas('jurnal_umum_detail', [
            'jurnal_umum_id' => $jurnal->id,
            'kode_perkiraan' => '1.00.04.01.01.00.00002',
            'kredit'         => 500000,
        ]);
    }

    public function test_bapr_retur_verification_automatically_posts_gl_journal(): void
    {
        SistemConfig::set('sirkulasi_harga_agen_besar', 3000, 'test');

        $agen = Agen::create([
            'kode_agen'     => 'AG-BAPR-01',
            'nama_agen'     => 'Agen BAPR Test',
            'kategori'      => '11',
            'status_aktif'  => true,
        ]);

        $periode = date('Y-m');
        $tgl = date('Y-m-d');

        $res = $this->actingAs($this->user)->post(route('accounting.sirkulasi.retur.store'), [
            'periode_bulan'          => $periode,
            'agen_id'                => $agen->id,
            'tanggal_retur'          => $tgl,
            'jumlah_retur_fisik'     => 10,
            'jumlah_retur_disetujui' => 10,
            'catatan_kondisi'        => 'Basah kena hujan',
        ]);

        $res->assertSessionHas('success');

        $jurnalRetur = JurnalUmum::where('sumber', 'sirkulasi')
            ->where('referensi', 'like', 'BAPR-%')
            ->firstOrFail();

        $this->assertEquals(30000.0, (float) $jurnalRetur->total_debet);
        $this->assertDatabaseHas('jurnal_umum_detail', [
            'jurnal_umum_id' => $jurnalRetur->id,
            'kode_perkiraan' => '4.00.05.01.02.00.00001',
            'debet'          => 30000,
        ]);
        $this->assertDatabaseHas('jurnal_umum_detail', [
            'jurnal_umum_id' => $jurnalRetur->id,
            'kode_perkiraan' => '1.00.04.01.01.00.00002',
            'kredit'         => 30000,
        ]);
    }

    public function test_gl_draft_approval_and_immutable_reversal_workflow(): void
    {
        $tgl = date('Y-m-d');

        // 1. Simpan sebagai Draft dulu (tidak boleh mengubah neraca sebelum di-approve)
        $draftRes = $this->actingAs($this->user)->post(route('accounting.gl.memorial.store'), [
            'tanggal'     => $tgl,
            'keterangan'  => 'Penyesuaian Biaya Listrik',
            'akun_debet'  => '6.00.01.01.01.00.00001',
            'akun_kredit' => '1.00.00.01.01.00.00001',
            'nominal'     => 450000,
            'action_mode' => 'draft',
        ]);
        $draftRes->assertRedirect(route('accounting.gl.jurnal'));

        $draftJurnal = JurnalUmum::where('keterangan', 'Penyesuaian Biaya Listrik')->firstOrFail();
        $this->assertEquals('draft', $draftJurnal->status);
        $this->assertNull($draftJurnal->approved_by);

        // 2. Approve & Post
        $appRes = $this->actingAs($this->user)->post(route('accounting.gl.jurnal.approve', $draftJurnal->id));
        $appRes->assertSessionHas('success');

        $draftJurnal->refresh();
        $this->assertEquals('posted', $draftJurnal->status);
        $this->assertEquals($this->user->id, $draftJurnal->approved_by);

        // 3. Immutable Ledger: Terbitkan Jurnal Pembalik (jurnal lama tetap posted + reversed_by terisi, net GL = 0)
        $revRes = $this->actingAs($this->user)->post(route('accounting.gl.jurnal.reverse', $draftJurnal->id), [
            'alasan' => 'Koreksi salah nominal bulan berjalan',
        ]);
        $revRes->assertSessionHas('success');

        $draftJurnal->refresh();
        $this->assertEquals('posted', $draftJurnal->status);
        $this->assertNotNull($draftJurnal->reversed_by_jurnal_id);

        $revJurnal = JurnalUmum::findOrFail($draftJurnal->reversed_by_jurnal_id);
        $this->assertEquals('pembalik', $revJurnal->tipe);
        $this->assertEquals($draftJurnal->id, $revJurnal->reversal_of_jurnal_id);

        $netBeban = (float) JurnalUmumDetail::whereHas('jurnal', fn ($q) => $q->where('status', 'posted'))
            ->where('kode_perkiraan', '6.00.01.01.01.00.00001')
            ->selectRaw('COALESCE(SUM(debet - kredit), 0) as net')
            ->value('net');

        $this->assertEquals(0.0, $netBeban);
    }

    public function test_coa_saldo_awal_and_pure_gl_balance_sheet_is_mathematically_balanced(): void
    {
        $akunKas = AkunPerkiraan::where('kode_perkiraan', '1.00.00.01.01.00.00001')->firstOrFail();
        $akunModal = AkunPerkiraan::where('kode_perkiraan', '3.00.01.01.01.00.00001')->firstOrFail();

        $this->actingAs($this->user)->patch(route('accounting.pengaturan.coa.saldo-awal', $akunKas->id), [
            'saldo_awal' => 10000000,
        ])->assertSessionHas('success');

        $this->actingAs($this->user)->patch(route('accounting.pengaturan.coa.saldo-awal', $akunModal->id), [
            'saldo_awal' => 10000000,
        ])->assertSessionHas('success');

        // Posting 1 transaksi pendapatan tunai Rp 2.500.000 bulan ini
        $tgl = date('Y-m-d');
        $this->actingAs($this->user)->post(route('accounting.gl.memorial.store'), [
            'tanggal'     => $tgl,
            'keterangan'  => 'Pendapatan Jasa Iklan Langsung',
            'akun_debet'  => '1.00.00.01.01.00.00001',
            'akun_kredit' => '4.00.01.01.01.00.00001',
            'nominal'     => 2500000,
            'action_mode' => 'posted',
        ]);

        // Buku Besar Kas harus menampilkan Saldo Awal 10.000.000 & Saldo Akhir 12.500.000
        $bukuRes = $this->actingAs($this->user)->get(route('accounting.gl.buku-besar', [
            'kode_perkiraan' => '1.00.00.01.01.00.00001',
            'dari'           => date('Y-m-01'),
            'sampai'         => date('Y-m-t'),
        ]));
        $bukuRes->assertStatus(200);
        $bukuRes->assertViewHas('saldoAwal', 10000000.0);
        $bukuRes->assertViewHas('saldoAkhir', 12500000.0);

        // Neraca Lajur & Laporan Neraca Pure GL harus balanced (Total Aset 12.5jt == Total Ekuitas + Kewajiban 12.5jt)
        $periode = date('Y-m');
        $neracaRes = $this->actingAs($this->user)->get(route('accounting.laporan.neraca', ['periode' => $periode]));
        $neracaRes->assertStatus(200);
        $neracaRes->assertViewHas('totalAset', 12500000.0);
        $neracaRes->assertViewHas('totalKewajiban', 0.0);
        $neracaRes->assertViewHas('totalEkuitas', 12500000.0);

        $lrRes = $this->actingAs($this->user)->get(route('accounting.laporan.laba-rugi', ['periode' => $periode]));
        $lrRes->assertStatus(200);
        $lrRes->assertViewHas('labaBersih', 2500000.0);
    }

    public function test_bank_reconciliation_store_automatch_and_gl_adjustment(): void
    {
        $periode = date('Y-m');
        $tgl = date('Y-m-d');

        KasMasuk::create([
            'no_kuitansi'          => 'BKM-REKON-BCA-01',
            'jenis_dokumen'        => 'BKM_KASIR',
            'tanggal_terima'       => $tgl,
            'tgl_transaksi'        => $tgl,
            'kode_bagian'          => '01',
            'tipe_transaksi'       => 'iklan',
            'terima_dari'          => 'PT Klien BCA',
            'nama_pembayar'        => 'PT Klien BCA',
            'metode_bayar'         => 'transfer',
            'bank_tujuan'          => 'BCA Giro',
            'jumlah_diterima'      => 1500000,
            'jumlah_dibayar'       => 1500000,
            'status_void'          => false,
            'user_id'              => $this->user->id,
        ]);

        // Baris 1: setoran klien Rp 1.500.000 (akan cocok saat auto-match)
        $this->actingAs($this->user)->post(route('accounting.beban.rekon.store'), [
            'periode'           => $periode,
            'rekening_bank'     => 'BCA',
            'tanggal_transaksi' => $tgl,
            'uraian_bank'       => 'TRSF CR PT Klien BCA',
            'tipe_mutasi'       => 'kredit_masuk',
            'nominal'           => 1500000,
        ])->assertSessionHas('success');

        // Baris 2: biaya admin bank Rp 25.000 (tidak ada di KasKeluar -> disesuaikan ke GL)
        $this->actingAs($this->user)->post(route('accounting.beban.rekon.store'), [
            'periode'           => $periode,
            'rekening_bank'     => 'BCA',
            'tanggal_transaksi' => $tgl,
            'uraian_bank'       => 'BIAYA ADM BULANAN',
            'tipe_mutasi'       => 'debet_keluar',
            'nominal'           => 25000,
        ])->assertSessionHas('success');

        $this->actingAs($this->user)->post(route('accounting.beban.rekon.auto-match'), [
            'periode' => $periode,
        ])->assertSessionHas('success');

        $matched = RekonsiliasiBank::where('uraian_bank', 'TRSF CR PT Klien BCA')->firstOrFail();
        $this->assertEquals('matched', $matched->status_match);

        $unmatched = RekonsiliasiBank::where('uraian_bank', 'BIAYA ADM BULANAN')->firstOrFail();
        $this->assertEquals('unmatched', $unmatched->status_match);

        $this->actingAs($this->user)->post(route('accounting.beban.rekon.adjust', $unmatched->id))
            ->assertSessionHas('success');

        $unmatched->refresh();
        $this->assertEquals('adjusted', $unmatched->status_match);
        $this->assertNotNull($unmatched->jurnal_umum_id);
    }

    public function test_hutang_vendor_ap_recording_and_payment_posts_to_gl_and_tutup_buku_closing_entry(): void
    {
        $tgl = date('Y-m-d');
        $periode = date('Y-m');
        $akunBeban = AkunPerkiraan::whereIn('kepala', [5, 6])->firstOrFail();

        // 1. Catat Tagihan Vendor: DPP 10.000.000, PPN 0, PPh23 200.000 => Utang bersih 9.800.000
        $storeRes = $this->actingAs($this->user)->post(route('accounting.beban.hutang-vendor.store'), [
            'nama_vendor'          => 'PT Kertas Priangan',
            'no_invoice_vendor'    => 'INV-PAPER-099',
            'kategori_beban'       => 'Kertas Koran',
            'tanggal_tagihan'      => $tgl,
            'jatuh_tempo'          => date('Y-m-d', strtotime('+30 days')),
            'kode_perkiraan_beban' => $akunBeban->kode_perkiraan,
            'dpp'                  => 10000000,
            'ppn'                  => 0,
            'pph23'                => 200000,
            'post_gl_now'          => 1,
        ]);
        $storeRes->assertSessionHas('success');

        $tagihan = VendorTagihan::where('nama_vendor', 'PT Kertas Priangan')->firstOrFail();
        $this->assertEquals(9800000.0, (float) $tagihan->total_tagihan);
        $this->assertEquals('tercatat', $tagihan->status);
        $this->assertNotNull($tagihan->jurnal_akui_id);

        // 2. Bayar Sebagian Rp 4.800.000 dari Bank BCA -> sisa utang Rp 5.000.000
        $bayarRes = $this->actingAs($this->user)->post(route('accounting.beban.hutang-vendor.bayar', $tagihan->id), [
            'tanggal_bayar'           => $tgl,
            'nominal_bayar'           => 4800000,
            'kode_perkiraan_kas_bank' => '1.00.01.01.01.00.00001',
            'metode_bayar'            => 'transfer',
            'keterangan'              => 'Termin 1 AP',
        ]);
        $bayarRes->assertSessionHas('success');

        $tagihan->refresh();
        $this->assertEquals(4800000.0, (float) $tagihan->total_terbayar);
        $this->assertEquals(5000000.0, (float) $tagihan->sisa_utang);
        $this->assertEquals('dibayar_sebagian', $tagihan->status);

        // 3. Tutup Buku dengan opsi Post Closing Entry
        $tutupRes = $this->actingAs($this->user)->post(route('accounting.pengaturan.tutup-buku.proses'), [
            'periode_key'        => $periode,
            'post_closing_entry' => 1,
        ]);
        $tutupRes->assertSessionHas('success');
        $this->assertTrue(PeriodeAkuntansi::isPeriodeLocked($periode));
        $this->assertDatabaseHas('jurnal_umum', [
            'referensi' => "CLOSE-{$periode}",
            'tipe'      => 'auto',
            'status'    => 'posted',
        ]);
    }
}
