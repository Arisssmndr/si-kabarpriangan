<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Faktur;
use App\Models\KasMasuk;
use App\Models\PenugasanInkaso;
use App\Models\SetoranInkasoLapangan;
use App\Models\ShiftKasir;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InkasoMobileAndKasirApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles & user
        User::factory()->create([
            'id' => 1,
            'name' => 'Administrator',
            'email' => 'admin@kabar-priangan.com',
            'role' => 'administrator',
        ]);
    }

    public function test_inkaso_mobile_page_renders_for_authenticated_petugas_inkaso()
    {
        $petugas = User::factory()->create([
            'name' => 'Randi Kurnia',
            'email' => 'randi@kabar-priangan.com',
            'role' => 'petugas_inkaso',
        ]);

        $response = $this->actingAs($petugas)->get(route('keuangan.inkaso.mobile'));
        $response->assertStatus(200);
        $response->assertSee('Randi Kurnia');
        $response->assertSee('Kolektor Lapangan');
        $response->assertSee('Surat Tugas');
        $response->assertSee('Tagihan Aktif');
        $response->assertSee('Setoran Hari Ini');
    }

    public function test_inkaso_can_accept_and_reject_assignment()
    {
        $petugas = User::factory()->create([
            'name' => 'Asep Saepuloh',
            'email' => 'asep@kabar-priangan.com',
            'role' => 'petugas_inkaso',
        ]);

        $customer = Customer::create([
            'nama_customer' => 'Agen Sariksa Agency',
            'tipe' => 'sirkulasi',
            'kota' => 'Kota Tasikmalaya',
        ]);

        $faktur1 = Faktur::create([
            'no_faktur' => 'FAK-SRK-104-202611-AG002',
            'kategori' => 'sirkulasi',
            'tipe_faktur' => 'bulanan',
            'customer_id' => $customer->id,
            'tanggal_faktur' => Carbon::today(),
            'jatuh_tempo' => Carbon::today()->addDays(14),
            'total_tagihan' => 30000000,
            'total_terbayar' => 0,
            'sisa_piutang' => 30000000,
            'status' => 'belum_bayar',
            'status_bayar' => 'belum_bayar',
            'created_by' => 1,
        ]);

        $tugas1 = PenugasanInkaso::create([
            'no_tugas' => 'TGS-INK-202611-0001',
            'faktur_id' => $faktur1->id,
            'petugas_id' => $petugas->id,
            'nama_petugas' => $petugas->name,
            'tanggal_tugas' => Carbon::today(),
            'status_konfirmasi' => 'menunggu',
            'status_tugas' => 'berjalan',
        ]);

        // 1. Terima Tugas
        $response = $this->actingAs($petugas)->post(route('keuangan.inkaso.penugasan.konfirmasi', $tugas1->id), [
            'aksi' => 'terima',
        ]);
        $response->assertSessionHas('success');

        $tugas1->refresh();
        $this->assertEquals('diterima', $tugas1->status_konfirmasi);
        $this->assertEquals('berjalan', $tugas1->status_tugas);

        // 2. Tolak Tugas pada faktur kedua
        $faktur2 = Faktur::create([
            'no_faktur' => 'FAK-SRK-104-202611-AG003',
            'kategori' => 'sirkulasi',
            'tipe_faktur' => 'bulanan',
            'customer_id' => $customer->id,
            'tanggal_faktur' => Carbon::today(),
            'jatuh_tempo' => Carbon::today()->addDays(14),
            'total_tagihan' => 15000000,
            'total_terbayar' => 0,
            'sisa_piutang' => 15000000,
            'status' => 'belum_bayar',
            'status_bayar' => 'belum_bayar',
            'created_by' => 1,
        ]);

        $tugas2 = PenugasanInkaso::create([
            'no_tugas' => 'TGS-INK-202611-0002',
            'faktur_id' => $faktur2->id,
            'petugas_id' => $petugas->id,
            'nama_petugas' => $petugas->name,
            'tanggal_tugas' => Carbon::today(),
            'status_konfirmasi' => 'menunggu',
            'status_tugas' => 'berjalan',
        ]);

        $response2 = $this->actingAs($petugas)->post(route('keuangan.inkaso.penugasan.konfirmasi', $tugas2->id), [
            'aksi' => 'tolak',
            'alasan_penolakan' => 'Toko tutup permanen dan agen pindah rute',
        ]);
        $response2->assertSessionHas('warning');

        $tugas2->refresh();
        $this->assertEquals('ditolak', $tugas2->status_konfirmasi);
        $this->assertEquals('batal', $tugas2->status_tugas);
        $this->assertEquals('Toko tutup permanen dan agen pindah rute', $tugas2->alasan_penolakan);
    }

    public function test_inkaso_field_payment_and_kasir_scan_approval_lifecycle()
    {
        $petugas = User::factory()->create([
            'name' => 'Randi Kurnia',
            'email' => 'randi@kabar-priangan.com',
            'role' => 'petugas_inkaso',
        ]);

        $kasir = User::factory()->create([
            'name' => 'Siti Kasir',
            'email' => 'siti@kabar-priangan.com',
            'role' => 'kasir',
        ]);

        // Buat Shift Kasir Aktif
        $shift = ShiftKasir::create([
            'kasir_id' => $kasir->id,
            'nama_shift' => 'Shift Pagi',
            'tanggal_shift' => Carbon::today(),
            'saldo_awal' => 500000,
            'total_kas_masuk' => 0,
            'total_kas_keluar' => 0,
            'saldo_akhir' => 500000,
            'status' => 'open',
        ]);

        $customer = Customer::create([
            'nama_customer' => 'Sariksa Agency',
            'tipe' => 'sirkulasi',
            'kota' => 'Kota Tasikmalaya',
        ]);

        // Faktur 30 Juta
        $faktur = Faktur::create([
            'no_faktur' => 'FAK-SRK-104-202611-AG002',
            'kategori' => 'sirkulasi',
            'tipe_faktur' => 'bulanan',
            'customer_id' => $customer->id,
            'tanggal_faktur' => Carbon::today(),
            'jatuh_tempo' => Carbon::today()->addDays(14),
            'total_tagihan' => 30000000,
            'total_terbayar' => 0,
            'sisa_piutang' => 30000000,
            'status' => 'belum_bayar',
            'status_bayar' => 'belum_bayar',
            'created_by' => 1,
        ]);

        $tugas = PenugasanInkaso::create([
            'no_tugas' => 'TGS-INK-202611-0010',
            'faktur_id' => $faktur->id,
            'petugas_id' => $petugas->id,
            'nama_petugas' => $petugas->name,
            'tanggal_tugas' => Carbon::today(),
            'status_konfirmasi' => 'diterima',
            'status_tugas' => 'berjalan',
        ]);

        // 1. Petugas Inkaso mencatat pembayaran di lapangan: 10 Juta dari 30 Juta (Cicilan pertama)
        $responseBayar = $this->actingAs($petugas)->post(route('keuangan.inkaso.setoran-lapangan.store'), [
            'faktur_id' => $faktur->id,
            'penugasan_id' => $tugas->id,
            'nominal_bayar' => 10000000,
            'metode_bayar' => 'tunai',
            'keterangan_angsuran' => 'Pembayaran pertama agen Sariksa',
        ]);

        $responseBayar->assertStatus(302);
        
        $setoran = SetoranInkasoLapangan::where('faktur_id', $faktur->id)->latest('id')->first();
        $this->assertNotNull($setoran);
        $this->assertEquals(10000000, (float) $setoran->nominal_titipan);
        $this->assertEquals('pending', $setoran->status_approval);
        $this->assertStringStartsWith('BSK-INK-', $setoran->no_bsk);

        // 2. Layar BSK Digital dapat diakses dengan Barcode & QR Code
        $responseBsk = $this->actingAs($petugas)->get(route('keuangan.inkaso.bsk.show', $setoran->no_bsk));
        $responseBsk->assertStatus(200);
        $responseBsk->assertSee($setoran->no_bsk);
        $responseBsk->assertSee('Rp 10.000.000');
        $responseBsk->assertSee('Arahkan Barcode Scanner Kasir ke Sini');

        // 3. Kasir scan API Barcode BSK
        $responseScan = $this->actingAs($kasir)->get(route('kasir.api.scan-bsk', $setoran->barcode_token));
        $responseScan->assertStatus(200);
        $responseScan->assertJson([
            'success' => true,
            'data' => [
                'no_bsk' => $setoran->no_bsk,
                'nominal_titipan' => 10000000,
                'no_faktur' => 'FAK-SRK-104-202611-AG002',
            ]
        ]);

        // 4. Kasir memverifikasi fisik uang dan klik APPROVE
        $responseApprove = $this->actingAs($kasir)->post(route('kasir.penerimaan.inkaso.approve', $setoran->id), [
            'catatan_kasir' => 'Uang fisik pas Rp 10.000.000 (100 lembar Rp 100.000) diverifikasi sah.',
        ]);
        $responseApprove->assertSessionHas('success');

        // 5. Verifikasi Efek Finansial Real-time
        $setoran->refresh();
        $this->assertEquals('approved', $setoran->status_approval);
        $this->assertNotNull($setoran->no_kuitansi_kasir);
        $this->assertStringStartsWith('KI-02-', $setoran->no_kuitansi_kasir);

        // Faktur terpotong: 30 Juta bayar 10 Juta -> sisa 20 Juta, status: cicilan
        $faktur->refresh();
        $this->assertEquals(10000000, (float) $faktur->total_terbayar);
        $this->assertEquals(20000000, (float) $faktur->sisa_piutang);
        $this->assertEquals('cicilan', $faktur->status);

        // Kas Masuk BKM terbit secara resmi
        $kasMasuk = KasMasuk::where('no_kuitansi', $setoran->no_kuitansi_kasir)->first();
        $this->assertNotNull($kasMasuk);
        $this->assertEquals(10000000, (float) $kasMasuk->jumlah_dibayar);
        $this->assertEquals('02', $kasMasuk->kode_bagian);
        $this->assertEquals($kasir->id, $kasMasuk->kasir_id);
    }
}
