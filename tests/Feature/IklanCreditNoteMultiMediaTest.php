<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Faktur;
use App\Models\JurnalUmum;
use App\Models\KoreksiPiutang;
use App\Models\TransaksiKoran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IklanCreditNoteMultiMediaTest extends TestCase
{
    use RefreshDatabase;

    protected User $accountingUser;
    protected User $iklanUser;
    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accountingUser = User::factory()->create([
            'role'     => 'accounting',
            'password' => bcrypt('password'),
        ]);

        $this->iklanUser = User::factory()->create([
            'role'     => 'iklan',
            'password' => bcrypt('password'),
        ]);

        $this->customer = Customer::create([
            'nama_customer' => 'PT Maju Bersama',
            'tipe'          => 'iklan',
            'no_telepon'    => '081234567890',
            'alamat'        => 'Jl. Perintis Kemerdekaan No. 10',
        ]);
    }

    public function test_koreksi_index_page_renders_cleanly(): void
    {
        $response = $this->actingAs($this->accountingUser)
            ->get(route('accounting.iklan.koreksi'));

        $response->assertStatus(200);
        $response->assertSee('Koreksi Piutang Iklan');
        $response->assertSee('Credit Note');
    }

    public function test_api_cari_faktur_returns_matching_debt_invoices(): void
    {
        $faktur = Faktur::create([
            'no_faktur'      => 'FK/CTK/2026/11/0099',
            'customer_id'    => $this->customer->id,
            'kategori'       => 'iklan',
            'tanggal_faktur' => Carbon::today(),
            'jatuh_tempo'    => Carbon::today()->addDays(30),
            'total_tagihan'  => 1000000,
            'total_terbayar' => 200000,
            'sisa_piutang'   => 800000,
            'status'         => 'cicilan',
            'status_bayar'   => 'uang_muka',
            'created_by'     => $this->accountingUser->id,
        ]);

        $response = $this->actingAs($this->accountingUser)
            ->get(route('accounting.iklan.koreksi.cari-faktur', ['q' => '0099']));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'no_faktur'    => 'FK/CTK/2026/11/0099',
            'sisa_piutang' => 800000,
        ]);
    }

    public function test_credit_note_creates_balanced_gl_journal_and_syncs_to_faktur(): void
    {
        $today = Carbon::today()->toDateString();

        // 1. Setup Faktur dan Transaksi Koran berpiutang
        $trx = TransaksiKoran::create([
            'id_iklankoran'              => 1,
            'sales_iklankoran'           => 'FO',
            'nofakturkoran'              => 'FK/CTK/2026/11/0101',
            'nama_pemasangkoran'         => 'PT Maju Terus',
            'alamat_pemasangkoran'       => 'Jl. Perintis Kemerdekaan No. 10',
            'tanggal_transaksikoran'     => $today,
            'tanggal_muatkoran'          => $today,
            'totaltagihan_transaksikoran'=> 1120000,
            'ppn_transaksikoran'         => 120000,
            'jumlahbayar_transaksikoran' => 0,
            'piutang_transaksikoran'     => 1120000,
            'status_order'               => 'Menunggu_Bayar',
        ]);

        $faktur = Faktur::create([
            'no_faktur'      => 'FK/CTK/2026/11/0101',
            'customer_id'    => $this->customer->id,
            'kategori'       => 'iklan',
            'tanggal_faktur' => $today,
            'jatuh_tempo'    => Carbon::today()->addDays(30),
            'total_tagihan'  => 1120000,
            'ppn_nominal'    => 120000,
            'total_terbayar' => 0,
            'sisa_piutang'   => 1120000,
            'status'         => 'belum_bayar',
            'status_bayar'   => 'belum_bayar',
            'created_by'     => $this->accountingUser->id,
        ]);

        // 2. Terbitkan Credit Note sebesar Rp 560.000 (koreksi 50% karena komplain cetak)
        $payload = [
            'no_faktur'       => 'FK/CTK/2026/11/0101',
            'tanggal'         => $today,
            'jumlah_koreksi'  => '560000',
            'kategori_alasan' => 'KOMPLAIN_CETAK',
            'keterangan'      => 'Diskon 50% karena warna cetak pudar pada halaman 3',
        ];

        $response = $this->actingAs($this->accountingUser)
            ->post(route('accounting.iklan.koreksi.store'), $payload);

        $response->assertRedirect(route('accounting.iklan.koreksi'));
        $response->assertSessionHas('success');

        // 3. Verifikasi record KoreksiPiutang
        $this->assertDatabaseHas('koreksi_piutang', [
            'no_faktur'             => 'FK/CTK/2026/11/0101',
            'kategori_alasan'       => 'KOMPLAIN_CETAK',
            'jumlah_koreksi'        => 560000,
            'saldo_setelah_koreksi' => 560000,
        ]);

        // 4. Verifikasi sisa piutang di TransaksiKoran berkurang
        $trx->refresh();
        $this->assertEquals(560000, (float) $trx->piutang_transaksikoran);

        // 5. Verifikasi tabel Faktur tersinkronisasi otomatis
        $faktur->refresh();
        $this->assertEquals(560000, (float) $faktur->sisa_piutang);

        // 6. Verifikasi Jurnal Umum Akuntansi berpasangan (Debet = Kredit)
        $jurnal = JurnalUmum::where('sumber', 'iklan')->latest('id')->first();
        $this->assertNotNull($jurnal);
        $this->assertEquals(560000, (float) $jurnal->total_debet);
        $this->assertEquals(560000, (float) $jurnal->total_kredit);
        $this->assertTrue($jurnal->is_balanced);
    }

    public function test_credit_note_marks_faktur_lunas_when_fully_credited(): void
    {
        $today = Carbon::today()->toDateString();

        $trx = TransaksiKoran::create([
            'id_iklankoran'              => 1,
            'sales_iklankoran'           => 'FO',
            'nofakturkoran'              => 'FK/CTK/2026/11/0202',
            'nama_pemasangkoran'         => 'Toko Serba Ada',
            'alamat_pemasangkoran'       => 'Jl. RE Martadinata No. 5',
            'tanggal_transaksikoran'     => $today,
            'tanggal_muatkoran'          => $today,
            'totaltagihan_transaksikoran'=> 300000,
            'ppn_transaksikoran'         => 0,
            'jumlahbayar_transaksikoran' => 0,
            'piutang_transaksikoran'     => 300000,
            'status_order'               => 'Menunggu_Bayar',
        ]);

        $faktur = Faktur::create([
            'no_faktur'      => 'FK/CTK/2026/11/0202',
            'customer_id'    => $this->customer->id,
            'kategori'       => 'iklan',
            'tanggal_faktur' => $today,
            'jatuh_tempo'    => Carbon::today()->addDays(30),
            'total_tagihan'  => 300000,
            'sisa_piutang'   => 300000,
            'status'         => 'belum_bayar',
            'status_bayar'   => 'belum_bayar',
            'created_by'     => $this->accountingUser->id,
        ]);

        // Koreksi penuh 100% karena pemutihan piutang macet
        $payload = [
            'no_faktur'       => 'FK/CTK/2026/11/0202',
            'tanggal'         => $today,
            'jumlah_koreksi'  => '300000',
            'kategori_alasan' => 'HAPUS_PIUTANG',
            'keterangan'      => 'Pemutihan piutang macet sesuai instruksi Direksi',
        ];

        $this->actingAs($this->accountingUser)
            ->post(route('accounting.iklan.koreksi.store'), $payload);

        $trx->refresh();
        $faktur->refresh();

        $this->assertEquals(0, (float) $trx->piutang_transaksikoran);
        $this->assertEquals('Lunas', $trx->status_order);

        $this->assertEquals(0, (float) $faktur->sisa_piutang);
        $this->assertEquals('lunas', $faktur->status);
    }

    public function test_pulled_transactions_are_locked_from_fo_edit_and_deletion(): void
    {
        $trx = TransaksiKoran::create([
            'id_iklankoran'              => 1,
            'sales_iklankoran'           => 'FO',
            'nofakturkoran'              => 'FK/CTK/2026/11/0303',
            'nama_pemasangkoran'         => 'PT Kencana Indah',
            'alamat_pemasangkoran'       => 'Jl. Hz. Mustofa No. 88',
            'tanggal_transaksikoran'     => Carbon::yesterday()->toDateString(),
            'tanggal_muatkoran'          => Carbon::yesterday()->toDateString(),
            'totaltagihan_transaksikoran'=> 2000000,
            'piutang_transaksikoran'     => 2000000,
            'is_pulled_akuntansi'        => true,
            'pulled_at'                  => Carbon::now(),
            'status_order'               => 'Antrean_Pracetak',
        ]);

        // FO mencoba menghapus transaksi yang sudah ditarik akuntansi
        $response = $this->actingAs($this->iklanUser)
            ->deleteJson(route('transaksikoran.destroy', $trx->id));

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'status' => 'error',
        ]);
        $this->assertDatabaseHas('transaksikoran', ['id' => $trx->id]);

        // FO mencoba membatalkan transaksi yang sudah ditarik akuntansi
        $batalResponse = $this->actingAs($this->iklanUser)
            ->post(route('transaksikoran.batalkan', $trx->id), [
                'alasan_batal' => 'Minta dibatalkan oleh klien',
            ]);

        $batalResponse->assertSessionHas('error');
    }

    public function test_cetak_koreksi_voucher_renders_properly(): void
    {
        $koreksi = KoreksiPiutang::create([
            'no_bukti'              => 'CN-IKL-20260001',
            'jenis'                 => 'iklan',
            'media_type'            => 'online',
            'kategori_alasan'       => 'GAGAL_TAYANG',
            'tanggal_koreksi'       => Carbon::today(),
            'no_faktur'             => 'FK/ONL/2026/11/0001',
            'kode_perkiraan'        => '1.00.04.01.01.01.00001',
            'nama_entitas'          => 'Diskominfo Tasikmalaya',
            'nilai_penjualan'       => 1500000,
            'jumlah_koreksi'        => 500000,
            'dpp_koreksi'           => 446428.57,
            'ppn_koreksi'           => 53571.43,
            'saldo_setelah_koreksi' => 1000000,
            'keterangan'            => 'Banner portal web tidak tayang 2 hari karena server maintenance',
            'user_id'               => $this->accountingUser->id,
        ]);

        $response = $this->actingAs($this->accountingUser)
            ->get(route('accounting.iklan.cetak-koreksi', $koreksi->id));

        $response->assertStatus(200);
        $response->assertSee('CN-IKL-20260001');
        $response->assertSee('NOTA KREDIT (CREDIT NOTE)');
        $response->assertSee('Diskominfo Tasikmalaya');
        $response->assertSee('Iklan Online');
        $response->assertSee('Gagal Tayang / Batal Sebagian');
    }
}
