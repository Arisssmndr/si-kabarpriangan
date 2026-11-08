<?php

namespace Tests\Feature;

use App\Models\Agen;
use App\Models\BuktiPengeluaran;
use App\Models\Kasbon;
use App\Models\TransaksiKoran;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KasirRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_all_kasir_get_routes(): void
    {
        $user = User::where('role', 'kasir')->firstOrFail();

        $routes = [
            'kasir.dashboard',
            'kasir.penerimaan.iklan',
            'kasir.penerimaan.sirkulasi',
            'kasir.penerimaan.koran-sisa',
            'kasir.penerimaan.tunai',
            'kasir.penerimaan.bank',
            'kasir.penerimaan.langganan',
            'kasir.pengeluaran.realisasi-bp',
            'kasir.pengeluaran.komisi',
            'kasir.setoran-bank',
            'kasir.pengeluaran.kasbon',
            'kasir.laporan.rekap',
            'kasir.laporan.mutasi-bank',
            'kasir.laporan.perkiraan',
            'kasir.laporan.posisi-kas',
            'kasir.laporan.kasbon',
            'kasir.laporan-harian',
            'kasir.utilitas.closing',
            'kasir.buku-kas',
            'kasir.utilitas.koreksi',
            'kasir.utilitas.cetak-ulang',
            'kasir.riwayat-cetak',
            'kasir.utilitas.buka-kas',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($user)->get(route($route));
            $this->assertEquals(
                200,
                $response->getStatusCode(),
                "Route [{$route}] returned status {$response->getStatusCode()}"
            );
        }
    }

    public function test_input_penerimaan_tunai(): void
    {
        $user = User::where('role', 'kasir')->firstOrFail();

        $response = $this->actingAs($user)->post(route('kasir.penerimaan.tunai.store'), [
            'pembeli'    => 'Budi Santoso',
            'kategori'   => 'Lain-lain',
            'keterangan' => 'Penjualan materi cetak tambahan',
            'jumlah'     => 150000,
            'metode'     => 'tunai',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('kas_masuks', [
            'terima_dari'     => 'Budi Santoso',
            'jumlah_diterima' => 150000,
        ]);
    }

    public function test_input_penerimaan_koran_sisa(): void
    {
        $user = User::where('role', 'kasir')->firstOrFail();

        $response = $this->actingAs($user)->post(route('kasir.penerimaan.koran-sisa.store'), [
            'pembeli'          => 'Pengepul Berkah',
            'jenis_satuan'     => 'kg',
            'jumlah_satuan'    => 20,
            'harga_per_satuan' => 3500,
            'metode_bayar'     => 'tunai',
            'keterangan'       => 'Penjualan sisa koran retur',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('kas_masuks', [
            'terima_dari'     => 'Pengepul Berkah',
            'jumlah_diterima' => 70000,
        ]);
    }

    public function test_input_penerimaan_bank(): void
    {
        $user = User::where('role', 'kasir')->firstOrFail();

        $response = $this->actingAs($user)->post(route('kasir.penerimaan.bank.store'), [
            'bank_sumber' => 'Bank BJB',
            'no_cek_giro' => 'CEK-BJB-998877',
            'nominal'     => 5000000,
            'penarik'     => 'Pak Eki (Kasir)',
            'keterangan'  => 'Tarik tunai modal kas laci',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('kas_masuks', [
            'tipe_transaksi'        => 'bank',
            'no_referensi_transfer' => 'CEK-BJB-998877',
            'jumlah_diterima'       => 5000000,
        ]);
    }

    public function test_input_pengeluaran_kasbon(): void
    {
        $user = User::where('role', 'kasir')->firstOrFail();

        $response = $this->actingAs($user)->post(route('kasir.pengeluaran.kasbon.store'), [
            'nama_penerima' => 'Ahmad Teknisi',
            'keperluan'     => 'Beli kabel LAN dan konektor RJ45',
            'nominal'       => 75000,
            'status_acc'    => 'langsung',
            'divisi'        => 'IT & Sirkulasi',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('kasbons', [
            'nama_penerima' => 'Ahmad Teknisi',
            'nominal'       => 75000,
        ]);
    }

    public function test_input_setoran_bank(): void
    {
        $user = User::where('role', 'kasir')->firstOrFail();

        $response = $this->actingAs($user)->post(route('kasir.setoran-bank.store'), [
            'bank_tujuan'   => 'Bank BJB - Rekening Operasional',
            'nominal_setor' => 1000000,
            'no_slip'       => 'SLIP-BJB-00123',
            'keterangan'    => 'Setor uang fisik ke rekening korporasi',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
    }

    public function test_input_penerimaan_sirkulasi(): void
    {
        $user = User::where('role', 'kasir')->firstOrFail();
        $agen = Agen::first();

        $response = $this->actingAs($user)->post(route('kasir.penerimaan.sirkulasi.store'), [
            'agen_id'           => $agen ? $agen->id : null,
            'kode_agen'         => $agen ? $agen->kode_agen : 'AG-01',
            'nama_agen'         => $agen ? $agen->nama_agen : 'Agen Tasik',
            'nominal_setoran'   => 250000,
            'metode_pembayaran' => 'tunai',
            'retur_eksemplar'   => 5,
            'keterangan'        => 'Setoran harian agen',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('kas_masuks', [
            'tipe_transaksi' => 'sirkulasi',
        ]);
    }

    public function test_input_penerimaan_iklan(): void
    {
        $user = User::where('role', 'kasir')->firstOrFail();
        $koran = TransaksiKoran::where('piutang_transaksikoran', '>', 0)->first();

        if (!$koran) {
            $koran = TransaksiKoran::first();
            if ($koran) {
                $koran->update([
                    'piutang_transaksikoran' => 500000,
                    'status_order' => 'Belum Lunas',
                ]);
            }
        }

        if ($koran) {
            $response = $this->actingAs($user)->post(route('kasir.penerimaan.iklan.store'), [
                'type'              => 'koran',
                'id'                => $koran->id,
                'nominal_bayar'     => 100000,
                'metode_pembayaran' => 'tunai',
                'keterangan'        => 'Pelunasan faktur iklan koran',
            ]);

            $response->assertSessionHasNoErrors();
            $this->assertDatabaseHas('kas_masuks', [
                'tipe_transaksi' => 'iklan',
                'ref_id'         => $koran->id,
            ]);
        }
    }

    public function test_input_realisasi_bp(): void
    {
        $user = User::where('role', 'kasir')->firstOrFail();
        $bp = BuktiPengeluaran::where('status', 'approved')->first() 
            ?? BuktiPengeluaran::create([
                'no_bp'             => 'BP-TEST-001',
                'divisi'            => 'Redaksi',
                'tanggal_pengajuan' => now(),
                'pemohon'           => 'Wartawan 1',
                'keperluan'         => 'Biaya peliputan lapangan',
                'nominal_acc'       => 200000,
                'status'            => 'approved',
            ]);

        $response = $this->actingAs($user)->post(route('kasir.pengeluaran.realisasi-bp.store'), [
            'bp_id'        => $bp->id,
            'nominal_cair' => 200000,
            'penerima'     => 'Wartawan 1',
            'metode_bayar' => 'kas_fisik',
            'keterangan'   => 'Pencairan operasional liputan',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('kas_keluars', [
            'bp_id'         => $bp->id,
            'jumlah_keluar' => 200000,
        ]);
    }

    public function test_input_closing_kasir(): void
    {
        $user = User::where('role', 'kasir')->firstOrFail();

        $response = $this->actingAs($user)->post(route('kasir.utilitas.closing.store'), [
            'pecahan_100k' => 10,
            'pecahan_50k'  => 5,
            'pecahan_20k'  => 2,
            'pecahan_10k'  => 0,
            'pecahan_5k'   => 0,
            'pecahan_2k'   => 0,
            'pecahan_1k'   => 0,
            'pecahan_koin' => 0,
            'catatan'      => 'Tutup shift harian normal seimbang',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('tutup_kas_berhasil', true);
        $response->assertSessionHas('closing_summary');
        $this->assertDatabaseHas('shift_kasirs', [
            'kasir_id' => $user->id,
            'status'   => 'closed',
        ]);
    }

    public function test_security_guest_redirected_to_login(): void
    {
        $this->get('/kasir/dashboard')->assertRedirect('/login');
        $this->get('/kasir/penerimaan/iklan')->assertRedirect('/login');
        $this->get('/kasir/pengeluaran/realisasi-bp')->assertRedirect('/login');
    }

    public function test_security_unauthorized_roles_forbidden_403(): void
    {
        $userIklan = User::where('role', 'iklan')->first() ?? User::factory()->create(['role' => 'iklan']);
        $userSirkulasi = User::where('role', 'sirkulasi')->first() ?? User::factory()->create(['role' => 'sirkulasi']);

        $this->actingAs($userIklan)->get('/kasir/penerimaan/iklan')->assertStatus(403);
        $this->actingAs($userIklan)->get('/kasir/pengeluaran/realisasi-bp')->assertStatus(403);
        $this->actingAs($userSirkulasi)->get('/kasir/penerimaan/iklan')->assertStatus(403);
        $this->actingAs($userSirkulasi)->get('/kasir/pengeluaran/realisasi-bp')->assertStatus(403);
    }

    public function test_security_shift_lock_enforcement_via_url(): void
    {
        $user = User::where('role', 'kasir')->firstOrFail();

        \App\Models\ShiftKasir::create([
            'kasir_id'       => $user->id,
            'tanggal_shift'  => now()->subDay()->toDateString(),
            'tgl_shift'      => now()->subDay()->toDateString(),
            'waktu_buka'     => now()->subDay(),
            'saldo_awal'     => 1000000,
            'total_ki_tunai' => 0,
            'total_ko_tunai' => 0,
            'saldo_sistem'   => 1000000,
            'saldo_fisik'    => 0,
            'selisih'        => 0,
            'status'         => 'open',
        ]);

        // Direct GET & POST bypass attempts must redirect to closing utility
        $this->actingAs($user)
            ->get(route('kasir.penerimaan.iklan'))
            ->assertRedirect(route('kasir.utilitas.closing'));

        $this->actingAs($user)
            ->post(route('kasir.penerimaan.tunai.store'), [
                'pembeli'    => 'Bypass Attempt',
                'kategori'   => 'Lain-lain',
                'keterangan' => 'Test bypass',
                'jumlah'     => 50000,
                'metode'     => 'tunai',
            ])
            ->assertRedirect(route('kasir.utilitas.closing'));
    }

    public function test_security_reject_double_payment_and_overpayment_iklan(): void
    {
        $user = User::where('role', 'kasir')->firstOrFail();
        $koran = TransaksiKoran::firstOrFail();

        // 1. Invoice already Lunas must be rejected
        $koran->update([
            'piutang_transaksikoran' => 0,
            'status_order'           => 'Lunas',
        ]);

        $responseLunas = $this->actingAs($user)->post(route('kasir.penerimaan.iklan.store'), [
            'type'              => 'koran',
            'id'                => $koran->id,
            'nominal_bayar'     => 100000,
            'metode_pembayaran' => 'tunai',
        ]);
        $responseLunas->assertSessionHas('error');

        // 2. Overpayment exceeding sisa_piutang must be rejected
        $koran->update([
            'piutang_transaksikoran' => 200000,
            'status_order'           => 'Menunggu_Bayar',
        ]);

        $responseOverpay = $this->actingAs($user)->post(route('kasir.penerimaan.iklan.store'), [
            'type'              => 'koran',
            'id'                => $koran->id,
            'nominal_bayar'     => 500000,
            'metode_pembayaran' => 'tunai',
        ]);
        $responseOverpay->assertSessionHas('error');
    }

    public function test_security_wapu_instansi_tax_calculation_and_ko_offset(): void
    {
        $user = User::where('role', 'kasir')->firstOrFail();
        $koran = TransaksiKoran::firstOrFail();

        $koran->update([
            'totaltagihan_transaksikoran' => 1110000,
            'jumlahbayar_transaksikoran'  => 0,
            'piutang_transaksikoran'      => 1110000,
            'status_order'                => 'Menunggu_Bayar',
        ]);

        // DPP = 1.110.000 * 100/111 = 1.000.000
        // PPN 11% = 110.000, PPh 23 2% = 20.000 -> Total Pajak = 130.000
        // Net Kas Diterima = 980.000
        $response = $this->actingAs($user)->post(route('kasir.penerimaan.iklan.store'), [
            'type'              => 'koran',
            'id'                => $koran->id,
            'nominal_bayar'     => 1110000,
            'metode_pembayaran' => 'tunai',
            'is_instansi_tax'   => 1,
            'keterangan'        => 'Setoran Iklan Pemkab WAPU',
        ]);

        $response->assertSessionHasNoErrors();

        // KI records gross amount (1.110.000) and net cash (980.000), KO records tax offset (130.000)
        $this->assertDatabaseHas('kas_masuks', [
            'tipe_transaksi'  => 'iklan',
            'ref_id'          => $koran->id,
            'jumlah_dibayar'  => 1110000,
            'jumlah_diterima' => 980000,
            'potongan_ppn'    => 110000,
            'potongan_pph23'  => 20000,
            'potongan_pajak'  => 130000,
        ]);

        $this->assertDatabaseHas('kas_keluars', [
            'kategori_beban' => 'pajak_instansi',
            'is_tax_offset'  => true,
            'jumlah_keluar'  => 130000,
        ]);
    }

    public function test_security_budget_cap_and_unapproved_bp_rejected(): void
    {
        $user = User::where('role', 'kasir')->firstOrFail();

        $bp = BuktiPengeluaran::create([
            'no_bp'             => 'BP-CAP-001',
            'no_reg_anggaran'   => 'PR-2026-999',
            'divisi'            => 'Redaksi',
            'tanggal_pengajuan' => now(),
            'pemohon'           => 'Redaktur',
            'keperluan'         => 'Operasional Liputan Khusus',
            'nominal_acc'       => 300000,
            'status'            => 'approved',
        ]);

        // Attempt to disburse 450.000 (> 300.000 budget cap)
        $responseCap = $this->actingAs($user)->post(route('kasir.pengeluaran.realisasi-bp.store'), [
            'bp_id'        => $bp->id,
            'nominal_cair' => 450000,
            'penerima'     => 'Redaktur',
            'metode_bayar' => 'kas_fisik',
        ]);
        $responseCap->assertSessionHas('error');
    }

    public function test_security_void_closed_transaction_and_idor_cetak_rejected(): void
    {
        $user = User::where('role', 'kasir')->firstOrFail();

        $km = \App\Models\KasMasuk::create([
            'no_kuitansi'     => 'KI-TEST-CLOSED-01',
            'tanggal'         => now()->toDateString(),
            'kode_bagian'     => 'KI-07',
            'terima_dari'     => 'Pelanggan Uji',
            'jumlah_diterima' => 100000,
            'cara_bayar'      => 'tunai',
            'kasir_id'        => $user->id,
            'is_closed'       => true,
        ]);

        // Attempt to void a closed transaction via URL / POST must be rejected
        $responseVoid = $this->actingAs($user)->post(route('kasir.utilitas.koreksi.store', $km->id), [
            'jenis'       => 'masuk',
            'id'          => $km->id,
            'alasan_void' => 'Coba hapus transaksi tutup buku',
        ]);
        $responseVoid->assertSessionHas('error');

        // Open transaction that gets voided cannot be printed via direct URL
        $kmOpen = \App\Models\KasMasuk::create([
            'no_kuitansi'     => 'VOID-KI-TEST-02',
            'tanggal'         => now()->toDateString(),
            'kode_bagian'     => 'KI-07',
            'terima_dari'     => 'Pelanggan Batal',
            'jumlah_diterima' => 50000,
            'cara_bayar'      => 'tunai',
            'kasir_id'        => $user->id,
            'is_closed'       => false,
            'keterangan'      => '[VOID / DIBATALKAN: Salah input]',
        ]);

        $this->actingAs($user)
            ->get(route('kasir.penerimaan.cetak', $kmOpen->id))
            ->assertStatus(403);
    }

    public function test_sop_continuous_ki_numbering_across_divisions_and_yearly_reset(): void
    {
        $user = User::where('role', 'kasir')->firstOrFail();

        $ki1 = \App\Models\KasMasuk::generateNoKuitansi('01', '2026-03-15');
        \App\Models\KasMasuk::create([
            'no_kuitansi'     => $ki1,
            'tanggal'         => '2026-03-15',
            'kode_bagian'     => '01',
            'terima_dari'     => 'Klien Iklan',
            'jumlah_diterima' => 500000,
            'cara_bayar'      => 'tunai',
            'kasir_id'        => $user->id,
        ]);

        // Different division (02 - Sirkulasi) and different month (April 2026) must continue sequence!
        $ki2 = \App\Models\KasMasuk::generateNoKuitansi('02', '2026-04-02');
        $seq1 = (int) substr($ki1, -4);
        $seq2 = (int) substr($ki2, -4);
        $this->assertEquals($seq1 + 1, $seq2);

        // Next year (January 2027) must reset back to 0001
        $kiNextYear = \App\Models\KasMasuk::generateNoKuitansi('01', '2027-01-02');
        $this->assertStringEndsWith('0001', $kiNextYear);
    }

    public function test_sop_transfer_bank_creates_bbm_and_double_entry_ko09(): void
    {
        $user = User::where('role', 'kasir')->firstOrFail();
        $koran = TransaksiKoran::firstOrFail();

        $koran->update([
            'totaltagihan_transaksikoran' => 800000,
            'jumlahbayar_transaksikoran'  => 0,
            'piutang_transaksikoran'      => 800000,
            'status_order'                => 'Menunggu_Bayar',
        ]);

        $response = $this->actingAs($user)->post(route('kasir.penerimaan.iklan.store'), [
            'type'                  => 'koran',
            'id'                    => $koran->id,
            'nominal_bayar'         => 500000, // Cicilan sebagian
            'metode_pembayaran'     => 'transfer',
            'bank_tujuan'           => 'Bank BJB Cabang Tasikmalaya',
            'no_referensi_transfer' => 'TRF-BJB-778899',
            'no_kuitansi_sementara' => 'KS-202611-0099',
            'manager_otorisasi'     => 'Manajer Keuangan',
            'keterangan'            => 'Cicilan Transfer Iklan',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('kas_masuks', [
            'ref_id'                => $koran->id,
            'jenis_dokumen'         => 'BBM',
            'is_cicilan'            => true,
            'no_kuitansi_sementara' => 'KS-202611-0099',
            'jumlah_dibayar'        => 500000,
        ]);

        // Verify paired Double-Entry KO-09 (BBM)
        $this->assertDatabaseHas('kas_keluars', [
            'kode_bagian'    => '09',
            'jenis_dokumen'  => 'BBM',
            'kategori_beban' => 'setor_bank',
            'jumlah_keluar'  => 500000,
        ]);
    }

    public function test_sop_strict_month_validation_and_reject_dp_decision_node(): void
    {
        $user = User::where('role', 'kasir')->firstOrFail();

        $bpLastMonth = BuktiPengeluaran::create([
            'no_bp'             => 'BP-LAST-MONTH-01',
            'no_reg_anggaran'   => 'PR-LAST-001',
            'divisi'            => 'Umum',
            'tanggal_pengajuan' => now()->subMonth(),
            'pemohon'           => 'Staf Umum',
            'keperluan'         => 'Pengadaan ATK Bulan Lalu',
            'nominal_acc'       => 250000,
            'status'            => 'approved',
        ]);

        // Attempting to disburse cross-month BP must be rejected
        $response = $this->actingAs($user)->post(route('kasir.pengeluaran.realisasi-bp.store'), [
            'bp_id'        => $bpLastMonth->id,
            'nominal_cair' => 250000,
            'penerima'     => 'Staf Umum',
            'metode_bayar' => 'kas_fisik',
        ]);
        $response->assertSessionHas('error');

        // Reject/return DP with catatan_penolakan
        $responseReject = $this->actingAs($user)->post(route('kasir.pengeluaran.realisasi-bp.tolak', $bpLastMonth->id), [
            'catatan_penolakan' => 'Dokumen DP berbeda bulan, wajib diajukan DP baru di bulan berjalan.',
        ]);
        $responseReject->assertSessionHas('success');

        $this->assertDatabaseHas('bukti_pengeluarans', [
            'id'                => $bpLastMonth->id,
            'status'            => 'rejected',
            'catatan_penolakan' => 'Dokumen DP berbeda bulan, wajib diajukan DP baru di bulan berjalan.',
        ]);
    }

    public function test_sop_kas_kecil_bs_to_bkkc_and_lkk_ki09_replenishment(): void
    {
        $user = User::where('role', 'kasir')->firstOrFail();

        // 1. Create active Bon Sementara (BS) Rp 500.000
        $bs = Kasbon::create([
            'no_kasbon'     => 'KB-2026110099',
            'no_bs'         => 'KB-2026110099',
            'tanggal'       => now()->toDateString(),
            'nama_penerima' => 'Yana Redaksi',
            'keperluan'     => 'Konsumsi Rapat Redaksi',
            'nominal'       => 500000,
            'nominal_bs'    => 500000,
            'status'        => 'pending',
            'kasir_id'      => $user->id,
            'approved_by'   => 'Manajer Keuangan',
        ]);

        // 2. Settle BS via BKKC with realization Rp 450.000 (Rp 50.000 returned to drawer)
        $resBkkc = $this->actingAs($user)->post(route('kasir.pengeluaran.kasbon.bkkc.store'), [
            'kasbon_id'         => $bs->id,
            'nama_penerima'     => 'Yana Redaksi',
            'keperluan'         => 'Konsumsi Rapat Redaksi Sesuai Nota',
            'nominal_realisasi' => 450000,
            'no_bkkc_ref'       => 'BKKC-2026110001',
            'kode_bagian'       => '06',
        ]);
        $resBkkc->assertSessionHasNoErrors();

        $this->assertDatabaseHas('kasbons', [
            'id'                    => $bs->id,
            'status'                => 'selesai_bkkc',
            'nominal_realisasi'     => 450000,
            'selisih_kembali_tunai' => 50000,
            'no_bkkc_ref'           => 'BKKC-2026110001',
        ]);

        $this->assertDatabaseHas('kas_keluars', [
            'jenis_dokumen' => 'BKKC',
            'jumlah_keluar' => 450000,
        ]);

        // 3. Compile BKKC into LKK and replenish via Check KI-09
        $resLkk = $this->actingAs($user)->post(route('kasir.pengeluaran.kasbon.lkk.store'), [
            'no_lkk_ref'        => 'LKK-2026110001',
            'kasbon_ids'        => [$bs->id],
            'nominal_pengisian' => 450000,
            'cairkan_cek_ki09'  => 1,
            'no_cek_bbk'        => 'CEK-BJB-LKK-777',
            'bank_sumber'       => 'Bank BJB Operasional',
        ]);
        $resLkk->assertSessionHasNoErrors();

        $this->assertDatabaseHas('kasbons', [
            'id'         => $bs->id,
            'no_lkk_ref' => 'LKK-2026110001',
        ]);

        $this->assertDatabaseHas('kas_masuks', [
            'kode_bagian'           => '09',
            'no_referensi_transfer' => 'CEK-BJB-LKK-777',
            'jumlah_diterima'       => 450000,
        ]);
    }

    public function test_same_day_closed_shift_locks_inputs_until_accounting_reopens_and_next_day_auto_opens(): void
    {
        $kasir = User::where('role', 'kasir')->firstOrFail();
        $accounting = User::where('role', 'keuangan')->firstOrFail();

        // Pastikan shift hari ini terbuka
        $this->actingAs($kasir)->get(route('kasir.dashboard'))->assertStatus(200);
        $shiftToday = \App\Models\ShiftKasir::whereDate('tanggal_shift', now()->toDateString())
            ->orWhereDate('tgl_shift', now()->toDateString())
            ->latest('id')
            ->firstOrFail();

        // 1. Tutup kas hari ini (misal jam 16:00)
        $resClose = $this->actingAs($kasir)->post(route('kasir.utilitas.closing.store'), [
            'pecahan_100k' => 50,
            'catatan'      => 'Closing jam 16.00',
        ]);
        $resClose->assertRedirect(route('kasir.dashboard'));

        $shiftToday->refresh();
        $this->assertEquals('closed', $shiftToday->status);

        // 2. Di hari yang sama setelah ditutup, mengakses menu input atau menembak POST wajib ditolak & diarahkan ke Buka Kembali Kas
        $this->actingAs($kasir)
            ->get(route('kasir.penerimaan.iklan'))
            ->assertRedirect(route('kasir.utilitas.buka-kas'))
            ->assertSessionHas('error');

        $this->actingAs($kasir)
            ->post(route('kasir.penerimaan.tunai.store'), [
                'pembeli'    => 'Klien Sore',
                'kategori'   => 'Koran Eceran',
                'keterangan' => 'Coba input setelah kas tutup',
                'jumlah'     => 25000,
                'metode'     => 'Tunai',
            ])
            ->assertRedirect(route('kasir.utilitas.buka-kas'))
            ->assertSessionHas('error');

        // Pastikan tidak ada shift baru yang terbuat otomatis di tanggal yang sama
        $openCountSameDay = \App\Models\ShiftKasir::where(function ($q) {
            $q->whereDate('tanggal_shift', now()->toDateString())
              ->orWhereDate('tgl_shift', now()->toDateString());
        })->whereIn('status', ['open', 'aktif'])->count();
        $this->assertEquals(0, $openCountSameDay);

        // 3. Kasir mengajukan Buka Kembali Kas & Accounting menyetujui
        $this->actingAs($kasir)
            ->post(route('kasir.utilitas.buka-kas.ajukan', $shiftToday->id), [
                'alasan_buka_kembali' => 'Ada koreksi input kuitansi iklan sore hari yang tertinggal.',
            ])
            ->assertSessionHas('success');

        $this->actingAs($accounting)
            ->post(route('kasir.utilitas.buka-kas.setujui', $shiftToday->id), [
                'catatan' => 'Disetujui Accounting untuk koreksi input tanggal hari ini.',
            ])
            ->assertSessionHas('success');

        $shiftToday->refresh();
        $this->assertEquals('open', $shiftToday->status);
        $this->assertEquals('approved', $shiftToday->reopen_status);

        // Sekarang kasir bisa membuka menu dan menginput transaksi di tanggal tersebut
        $this->actingAs($kasir)
            ->get(route('kasir.penerimaan.iklan'))
            ->assertStatus(200);

        $this->actingAs($kasir)
            ->post(route('kasir.penerimaan.tunai.store'), [
                'pembeli'    => 'Klien Sore Susulan',
                'kategori'   => 'Koran Eceran',
                'keterangan' => 'Input susulan setelah ACC Accounting',
                'jumlah'     => 25000,
                'metode'     => 'Tunai',
            ])
            ->assertSessionHas('success');

        // Tutup kembali shift hari ini setelah selesai koreksi
        $this->actingAs($kasir)->post(route('kasir.utilitas.closing.store'), [
            'pecahan_100k' => 50,
            'catatan'      => 'Re-closing setelah koreksi',
        ])->assertRedirect(route('kasir.dashboard'));

        $shiftToday->refresh();
        $this->assertEquals('closed', $shiftToday->status);
        $this->assertEquals('none', $shiftToday->reopen_status);

        // 4. Keesokan harinya (H+1), sistem otomatis membuka shift baru untuk hari esok dan langsung bisa input
        \Illuminate\Support\Carbon::setTestNow(now()->addDay()->setTime(8, 30));

        try {
            $this->actingAs($kasir)
                ->get(route('kasir.penerimaan.iklan'))
                ->assertStatus(200);

            $this->actingAs($kasir)
                ->post(route('kasir.penerimaan.tunai.store'), [
                    'pembeli'    => 'Klien Besok Pagi',
                    'kategori'   => 'Koran Eceran',
                    'keterangan' => 'Input hari berikutnya otomatis terbuka',
                    'jumlah'     => 30000,
                    'metode'     => 'Tunai',
                ])
                ->assertSessionHas('success');

            $shiftNextDay = \App\Models\ShiftKasir::whereDate('tanggal_shift', now()->toDateString())
                ->whereIn('status', ['open', 'aktif'])
                ->first();
            $this->assertNotNull($shiftNextDay);
        } finally {
            \Illuminate\Support\Carbon::setTestNow();
        }
    }

    public function test_edit_inputan_masuk_kasir_and_audit_trail(): void
    {
        $kasir = User::where('role', 'kasir')->firstOrFail();

        // 1. Kasir input transaksi kas masuk tunai
        $resStore = $this->actingAs($kasir)->post(route('kasir.penerimaan.tunai.store'), [
            'pembeli'    => 'Pak Budi',
            'kategori'   => 'Iklan Baris',
            'keterangan' => 'Pemasangan iklan baris 3 baris',
            'jumlah'     => 50000,
            'metode'     => 'Tunai',
        ]);
        $resStore->assertSessionHas('success');

        $trx = \App\Models\KasMasuk::where('terima_dari', 'Pak Budi')->latest('id')->firstOrFail();
        $this->assertEquals(50000, (float)$trx->jumlah_dibayar);

        $shift = \App\Models\ShiftKasir::find($trx->shift_id);
        $initialKi = (float)$shift->total_ki;

        // 2. Admin kasir menyadari salah input dan mengedit transaksi tersebut
        $resEdit = $this->actingAs($kasir)->post(route('kasir.utilitas.koreksi.update', $trx->id), [
            'tipe_dokumen'   => 'KI',
            'nama_pihak'     => 'Pak Budi Hendrawan (Koreksi)',
            'jumlah_nominal' => 75000,
            'metode_bayar'   => 'transfer',
            'bank_tujuan'    => 'Bank BJB Tasikmalaya',
            'keterangan'     => 'Pemasangan iklan baris 5 baris (diperbaiki)',
            'alasan_koreksi' => 'Koreksi typo nama dan penambahan nominal 25k sesuai faktur',
        ]);
        $resEdit->assertSessionHas('success');

        // 3. Verifikasi record KasMasuk berhasil terupdate dan memuat audit trail
        $trx->refresh();
        $this->assertEquals('Pak Budi Hendrawan (Koreksi)', $trx->terima_dari);
        $this->assertEquals(75000, (float)$trx->jumlah_dibayar);
        $this->assertEquals('transfer', $trx->metode_bayar);
        $this->assertEquals('Bank BJB Tasikmalaya', $trx->bank_tujuan);
        $this->assertStringContainsString('Koreksi typo nama', $trx->keterangan);

        // 4. Verifikasi saldo total_ki pada shift otomatis bertambah sesuai selisih (delta +25.000)
        $shift->refresh();
        $this->assertEquals($initialKi + 25000, (float)$shift->total_ki);

        // 5. Transaksi yang sudah ditutup (is_closed = true) wajib ditolak jika diedit
        $trx->update(['is_closed' => true]);

        $resEditClosed = $this->actingAs($kasir)->post(route('kasir.utilitas.koreksi.update', $trx->id), [
            'tipe_dokumen'   => 'KI',
            'nama_pihak'     => 'Coba Edit Transaksi Closed',
            'jumlah_nominal' => 100000,
            'alasan_koreksi' => 'Uji penolakan edit closed',
        ]);
        $resEditClosed->assertSessionHas('error');
    }

    public function test_filter_tanggal_pada_menu_pencarian_kasir(): void
    {
        $kasir = User::where('role', 'kasir')->firstOrFail();
        $sampleDate = '2026-11-06';

        // 1. Utilitas Buka Kembali Kas dengan filter tanggal
        $resBukaKas = $this->actingAs($kasir)->get(route('kasir.utilitas.buka-kas', ['tanggal' => $sampleDate]));
        $resBukaKas->assertStatus(200);
        $resBukaKas->assertViewHas('tanggal', $sampleDate);

        // 2. Utilitas Cetak Ulang Kuitansi dengan filter tanggal
        $resCetakUlang = $this->actingAs($kasir)->get(route('kasir.utilitas.cetak-ulang', ['tanggal' => $sampleDate]));
        $resCetakUlang->assertStatus(200);
        $resCetakUlang->assertViewHas('tanggal', $sampleDate);

        // 3. Riwayat Cetak dengan filter tanggal
        $resRiwayat = $this->actingAs($kasir)->get(route('kasir.riwayat-cetak', ['tanggal' => $sampleDate]));
        $resRiwayat->assertStatus(200);
        $resRiwayat->assertViewHas('tanggal', $sampleDate);

        // 4. Realisasi BP dengan filter tanggal
        $resBp = $this->actingAs($kasir)->get(route('kasir.pengeluaran.realisasi-bp', ['tanggal' => $sampleDate]));
        $resBp->assertStatus(200);
        $resBp->assertViewHas('tanggal', $sampleDate);

        // 5. Pengeluaran Komisi dengan filter tanggal
        $resKomisi = $this->actingAs($kasir)->get(route('kasir.pengeluaran.komisi', ['tanggal' => $sampleDate]));
        $resKomisi->assertStatus(200);
        $resKomisi->assertViewHas('tanggal', $sampleDate);

        // 6. Penerimaan Bank dengan filter tanggal
        $resBank = $this->actingAs($kasir)->get(route('kasir.penerimaan.bank', ['tanggal' => $sampleDate]));
        $resBank->assertStatus(200);
        $resBank->assertViewHas('tanggal', $sampleDate);

        // 7. Penerimaan Koran Sisa dengan filter tanggal
        $resSisa = $this->actingAs($kasir)->get(route('kasir.penerimaan.koran-sisa', ['tanggal' => $sampleDate]));
        $resSisa->assertStatus(200);
        $resSisa->assertViewHas('tanggal', $sampleDate);

        // 8. Laporan Kasbon dengan filter tanggal
        $resKasbon = $this->actingAs($kasir)->get(route('kasir.laporan.kasbon', ['tanggal' => $sampleDate]));
        $resKasbon->assertStatus(200);
        $resKasbon->assertViewHas('tanggal', $sampleDate);
    }

    public function test_filter_periode_waktu_dan_qr_scan(): void
    {
        $kasir = User::where('role', 'kasir')->firstOrFail();

        // 1. Filter periode bulan dan tahun
        $resBulan = $this->actingAs($kasir)->get(route('kasir.utilitas.buka-kas', [
            'periode_tipe' => 'bulan',
            'bulan'        => '11',
            'tahun'        => '2026',
        ]));
        $resBulan->assertStatus(200);
        $resBulan->assertViewHas('periodeTipe', 'bulan');
        $resBulan->assertViewHas('bulan', '11');
        $resBulan->assertViewHas('tahun', '2026');

        // 2. Filter periode tahun
        $resTahun = $this->actingAs($kasir)->get(route('kasir.utilitas.cetak-ulang', [
            'periode_tipe' => 'tahun',
            'tahun'        => '2026',
        ]));
        $resTahun->assertStatus(200);
        $resTahun->assertViewHas('periodeTipe', 'tahun');
        $resTahun->assertViewHas('tahun', '2026');

        // 3. Scan QR Code faktur iklan
        $koran = TransaksiKoran::firstOrFail();
        $resScanQr = $this->actingAs($kasir)->get(route('kasir.api.scan-iklan', [
            'qr' => $koran->nofakturkoran,
        ]));
        $resScanQr->assertStatus(200);
        $resScanQr->assertJson([
            'success' => true,
            'data'    => [
                'no_faktur' => $koran->nofakturkoran,
            ],
        ]);
    }
}
