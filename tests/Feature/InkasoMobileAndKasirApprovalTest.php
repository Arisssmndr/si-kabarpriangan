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
        $response->assertSee('Setoran BSK');
        $response->assertSee('Riwayat');
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

    public function test_dashboard_redirects_petugas_inkaso_directly_to_mobile_inkaso(): void
    {
        $petugas = User::factory()->create([
            'email' => 'inkaso@kabarpriangan.co.id',
            'role' => 'petugas_inkaso',
        ]);

        $response = $this->actingAs($petugas)->get(route('dashboard'));
        $response->assertRedirect(route('keuangan.inkaso.mobile'));
    }

    public function test_keuangan_dashboard_redirects_petugas_inkaso_to_mobile_inkaso(): void
    {
        $petugas = User::factory()->create([
            'email' => 'inkaso2@kabarpriangan.co.id',
            'role' => 'petugas_inkaso',
        ]);

        $response = $this->actingAs($petugas)->get(route('keuangan.dashboard'));
        $response->assertRedirect(route('keuangan.inkaso.mobile'));
    }

    public function test_inkaso_root_redirects_to_mobile(): void
    {
        $petugas = User::factory()->create([
            'email' => 'inkaso3@kabarpriangan.co.id',
            'role' => 'petugas_inkaso',
        ]);

        $response = $this->actingAs($petugas)->get(route('inkaso.root'));
        $response->assertRedirect(route('keuangan.inkaso.mobile'));
    }

    public function test_petugas_inkaso_cannot_access_desktop_assignment_page_and_is_redirected_to_mobile(): void
    {
        $petugas = User::factory()->create([
            'email' => 'inkaso4@kabarpriangan.co.id',
            'role' => 'petugas_inkaso',
        ]);

        // Karena route dilindungi role admin faktur/keuangan atau controller guard
        $response = $this->actingAs($petugas)->get(route('keuangan.inkaso.index'));
        $this->assertTrue(in_array($response->status(), [302, 403]));
        if ($response->status() === 302) {
            $response->assertRedirect(route('keuangan.inkaso.mobile'));
        }
    }

    public function test_petugas_inkaso_cannot_call_assign_route(): void
    {
        $petugas = User::factory()->create([
            'email' => 'inkaso5@kabarpriangan.co.id',
            'role' => 'petugas_inkaso',
        ]);

        $response = $this->actingAs($petugas)->post(route('keuangan.inkaso.assign'), [
            'faktur_id' => 1,
            'tanggal_tugas' => Carbon::today()->toDateString(),
        ]);

        $response->assertRedirect(route('keuangan.inkaso.mobile'));
    }

    public function test_admin_faktur_can_assign_invoices_which_appear_in_inkaso_mobile_surat_tugas(): void
    {
        $adminFaktur = User::factory()->create([
            'email' => 'faktur@kabarpriangan.co.id',
            'role' => 'faktur',
        ]);

        $petugas = User::factory()->create([
            'name' => 'Budi Penagih',
            'email' => 'budi.inkaso@kabarpriangan.co.id',
            'role' => 'petugas_inkaso',
        ]);

        $customer = Customer::create([
            'nama_customer' => 'PT Semesta Priangan',
            'tipe' => 'iklan',
            'kota' => 'Kota Tasikmalaya',
        ]);

        $faktur = Faktur::create([
            'no_faktur' => 'FAK-IKL-202611-099',
            'kategori' => 'iklan',
            'tipe_faktur' => 'biasa',
            'customer_id' => $customer->id,
            'tanggal_faktur' => Carbon::today(),
            'jatuh_tempo' => Carbon::today()->addDays(7),
            'total_tagihan' => 5000000,
            'total_terbayar' => 0,
            'sisa_piutang' => 5000000,
            'status' => 'belum_bayar',
            'status_bayar' => 'belum_bayar',
            'created_by' => $adminFaktur->id,
        ]);

        // 1. Admin Faktur menugaskan faktur ke Petugas Inkaso
        $assignResponse = $this->actingAs($adminFaktur)->post(route('keuangan.inkaso.assign'), [
            'faktur_ids' => [$faktur->id],
            'petugas_id' => $petugas->id,
            'petugas_nama' => $petugas->name,
            'tanggal_tugas' => Carbon::today()->toDateString(),
            'catatan' => 'Segera tagih ke bagian keuangan PT Semesta Priangan',
        ]);

        $assignResponse->assertSessionHas('success');

        // 2. Petugas Inkaso membuka portal mobile dan faktur langsung ada di Surat Tugas
        $mobileResponse = $this->actingAs($petugas)->get(route('keuangan.inkaso.mobile'));
        $mobileResponse->assertStatus(200);
        $mobileResponse->assertSee('PT Semesta Priangan');
        $mobileResponse->assertSee('FAK-IKL-202611-099');
        $mobileResponse->assertSee('Terima Tugas');
        $mobileResponse->assertSee('Tolak Faktur Ini');
    }

    public function test_petugas_inkaso_visiting_other_division_is_redirected_to_mobile_without_403(): void
    {
        $petugas = User::factory()->create([
            'name' => 'Kolektor Riki',
            'email' => 'riki.inkaso@kabarpriangan.co.id',
            'role' => 'petugas_inkaso',
        ]);

        // Saat Petugas Inkaso mencoba membuka dashboard divisi lain (misal iklan/kasir/accounting), sistem tidak melempar 403 melainkan redirect aman ke mobile
        $response = $this->actingAs($petugas)->get('/iklan/dashboard');
        $response->assertRedirect(route('keuangan.inkaso.mobile'));
    }

    public function test_petugas_inkaso_partial_reject_only_rejects_selected_invoice(): void
    {
        $adminFaktur = User::factory()->create([
            'email' => 'faktur2@kabarpriangan.co.id',
            'role' => 'faktur',
        ]);

        $petugas = User::factory()->create([
            'name' => 'Ahmad Supardi',
            'email' => 'ahmad@kabarpriangan.co.id',
            'role' => 'petugas_inkaso',
        ]);

        $customer1 = Customer::create([
            'nama_customer' => 'Agen Singaparna Barat',
            'tipe' => 'sirkulasi',
            'kota' => 'Kabupaten Tasikmalaya',
        ]);

        $customer2 = Customer::create([
            'nama_customer' => 'Agen Cihideung',
            'tipe' => 'sirkulasi',
            'kota' => 'Kota Tasikmalaya',
        ]);

        $faktur1 = Faktur::create([
            'no_faktur' => 'FAK-SRK-202611-001',
            'kategori' => 'sirkulasi',
            'tipe_faktur' => 'bulanan',
            'customer_id' => $customer1->id,
            'tanggal_faktur' => Carbon::today(),
            'jatuh_tempo' => Carbon::today()->addDays(7),
            'total_tagihan' => 2000000,
            'total_terbayar' => 0,
            'sisa_piutang' => 2000000,
            'status' => 'belum_bayar',
            'status_bayar' => 'belum_bayar',
            'created_by' => $adminFaktur->id,
        ]);

        $faktur2 = Faktur::create([
            'no_faktur' => 'FAK-SRK-202611-002',
            'kategori' => 'sirkulasi',
            'tipe_faktur' => 'bulanan',
            'customer_id' => $customer2->id,
            'tanggal_faktur' => Carbon::today(),
            'jatuh_tempo' => Carbon::today()->addDays(7),
            'total_tagihan' => 3000000,
            'total_terbayar' => 0,
            'sisa_piutang' => 3000000,
            'status' => 'belum_bayar',
            'status_bayar' => 'belum_bayar',
            'created_by' => $adminFaktur->id,
        ]);

        // Ditugaskan bersamaan dalam 1 batch
        $tugas1 = PenugasanInkaso::create([
            'no_tugas' => 'TGS-INK-202611-8888',
            'faktur_id' => $faktur1->id,
            'petugas_id' => $petugas->id,
            'nama_petugas' => $petugas->name,
            'tanggal_tugas' => Carbon::today(),
            'status_konfirmasi' => 'menunggu',
            'status_tugas' => 'berjalan',
        ]);

        $tugas2 = PenugasanInkaso::create([
            'no_tugas' => 'TGS-INK-202611-8889',
            'faktur_id' => $faktur2->id,
            'petugas_id' => $petugas->id,
            'nama_petugas' => $petugas->name,
            'tanggal_tugas' => Carbon::today(),
            'status_konfirmasi' => 'menunggu',
            'status_tugas' => 'berjalan',
        ]);

        // 1. Petugas menolak Faktur 1 saja (karena beda arah)
        $rejectResponse = $this->actingAs($petugas)->post(route('keuangan.inkaso.penugasan.konfirmasi', $tugas1->id), [
            'aksi' => 'tolak',
            'alasan_penolakan' => 'Beda arah rute (Singaparna terlalu jauh dari trayek hari ini)',
        ]);
        $rejectResponse->assertSessionHas('warning');

        $tugas1->refresh();
        $this->assertEquals('ditolak', $tugas1->status_konfirmasi);
        $this->assertEquals('batal', $tugas1->status_tugas);
        $this->assertEquals('Beda arah rute (Singaparna terlalu jauh dari trayek hari ini)', $tugas1->alasan_penolakan);

        // Faktur 2 harus TETAP menunggu dan TIDAK ikut terbatal!
        $tugas2->refresh();
        $this->assertEquals('menunggu', $tugas2->status_konfirmasi);
        $this->assertEquals('berjalan', $tugas2->status_tugas);

        // 2. Petugas menerima Faktur 2
        $acceptResponse = $this->actingAs($petugas)->post(route('keuangan.inkaso.penugasan.konfirmasi', $tugas2->id), [
            'aksi' => 'terima',
        ]);
        $acceptResponse->assertSessionHas('success');

        $tugas2->refresh();
        $this->assertEquals('diterima', $tugas2->status_konfirmasi);
        $this->assertEquals('berjalan', $tugas2->status_tugas);
    }

    public function test_inkaso_can_update_profile_name_telepon_and_foto_but_cannot_change_password(): void
    {
        $oldPasswordHash = bcrypt('passwordAwal123');
        $petugas = User::factory()->create([
            'name' => 'Kolektor Lapangan Asli',
            'email' => 'kolektor.asli@kabarpriangan.co.id',
            'role' => 'petugas_inkaso',
            'telepon' => '081234567890',
            'password' => $oldPasswordHash,
        ]);

        $fakePhoto = \Illuminate\Http\UploadedFile::fake()->image('profil_kolektor.jpg', 200, 200);

        $response = $this->actingAs($petugas)->post(route('keuangan.inkaso.profile.update'), [
            'name' => 'Ahmad Supardi, S.E.',
            'telepon' => '089988776655',
            'foto' => $fakePhoto,
            'password' => 'cobaGantiPasswordBaru123',
            'password_confirmation' => 'cobaGantiPasswordBaru123',
        ]);

        $response->assertRedirect(route('keuangan.inkaso.mobile', ['tab' => 'profil']));
        $response->assertSessionHas('success');

        $petugas->refresh();
        $this->assertEquals('Ahmad Supardi, S.E.', $petugas->name);
        $this->assertEquals('089988776655', $petugas->telepon);
        $this->assertNotNull($petugas->foto);
        $this->assertFileExists(public_path('uploads/avatars/' . $petugas->foto));

        // Verifikasi bahwa password TIDAK berubah sesuai kebijakan keamanan korporat
        $this->assertEquals($oldPasswordHash, $petugas->password);

        // Cleanup test uploaded file
        if (file_exists(public_path('uploads/avatars/' . $petugas->foto))) {
            @unlink(public_path('uploads/avatars/' . $petugas->foto));
        }
    }

    public function test_inkaso_mobile_renders_circular_whatsapp_style_avatar_and_profile_tab(): void
    {
        $petugas = User::factory()->create([
            'name' => 'Randi Penagih',
            'email' => 'randi.penagih@kabarpriangan.co.id',
            'role' => 'petugas_inkaso',
            'foto' => null, // Tanpa foto
        ]);

        $response = $this->actingAs($petugas)->get(route('keuangan.inkaso.mobile', ['tab' => 'profil']));
        $response->assertStatus(200);
        $response->assertSee('Randi Penagih');
        $response->assertSee('Profil');
        $response->assertSee('Nama Lengkap Petugas');
        $response->assertSee('Nomor HP / WhatsApp');
        // Assert keberadaan siluet bulat avatar WhatsApp default saat tanpa foto
        $response->assertSee('bg-[#CFD8DC]');
        $response->assertSee('WhatsApp Style Default Avatar', false);
        // Kebijakan keamanan korporat: form password ditiadakan
        $response->assertSee('Kebijakan Keamanan Akun Korporat');
        $response->assertDontSee('Ganti Kata Sandi');
        $response->assertDontSee('Kata Sandi Baru');
    }

    public function test_surat_penugasan_grup_binds_multiple_invoices_updates_dashboard_target_and_allows_partial_selesai(): void
    {
        $adminFaktur = User::factory()->create([
            'email' => 'admin.faktur.grup@kabarpriangan.co.id',
            'role' => 'faktur',
        ]);

        $petugas = User::factory()->create([
            'name' => 'Randi Grup Kolektor',
            'email' => 'randi.grup@kabarpriangan.co.id',
            'role' => 'petugas_inkaso',
        ]);

        $kasir = User::factory()->create([
            'name' => 'Siti Kasir Grup',
            'email' => 'siti.grup@kabarpriangan.co.id',
            'role' => 'kasir',
        ]);

        ShiftKasir::create([
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
            'nama_customer' => 'Agen Berkah Grup',
            'tipe' => 'iklan',
            'kota' => 'Kota Tasikmalaya',
        ]);

        $faktur1 = Faktur::create([
            'no_faktur' => 'FAK-GRP-001',
            'kategori' => 'iklan',
            'tipe_faktur' => 'biasa',
            'customer_id' => $customer->id,
            'tanggal_faktur' => Carbon::today(),
            'jatuh_tempo' => Carbon::today()->addDays(7),
            'total_tagihan' => 2000000,
            'total_terbayar' => 0,
            'sisa_piutang' => 2000000,
            'status' => 'belum_bayar',
            'status_bayar' => 'belum_bayar',
            'created_by' => $adminFaktur->id,
        ]);

        $faktur2 = Faktur::create([
            'no_faktur' => 'FAK-GRP-002',
            'kategori' => 'iklan',
            'tipe_faktur' => 'biasa',
            'customer_id' => $customer->id,
            'tanggal_faktur' => Carbon::today(),
            'jatuh_tempo' => Carbon::today()->addDays(7),
            'total_tagihan' => 3000000,
            'total_terbayar' => 0,
            'sisa_piutang' => 3000000,
            'status' => 'belum_bayar',
            'status_bayar' => 'belum_bayar',
            'created_by' => $adminFaktur->id,
        ]);

        $faktur3 = Faktur::create([
            'no_faktur' => 'FAK-GRP-003',
            'kategori' => 'iklan',
            'tipe_faktur' => 'biasa',
            'customer_id' => $customer->id,
            'tanggal_faktur' => Carbon::today(),
            'jatuh_tempo' => Carbon::today()->addDays(7),
            'total_tagihan' => 1000000,
            'total_terbayar' => 0,
            'sisa_piutang' => 1000000,
            'status' => 'belum_bayar',
            'status_bayar' => 'belum_bayar',
            'created_by' => $adminFaktur->id,
        ]);

        // 1. Admin assign 3 faktur sekaligus -> harus terikat 1 no_surat_grup (ST-YYYYMM-001)
        $this->actingAs($adminFaktur)->post(route('keuangan.inkaso.assign'), [
            'faktur_ids' => [$faktur1->id, $faktur2->id],
            'petugas_id' => $petugas->id,
            'petugas_nama' => $petugas->name,
            'tanggal_tugas' => Carbon::today()->toDateString(),
        ])->assertSessionHas('success');

        // Assign faktur ke-3 di hari yang sama ke petugas yang sama -> ikut terikat ke nomor surat aktif yg sama
        $this->actingAs($adminFaktur)->post(route('keuangan.inkaso.assign'), [
            'faktur_id' => $faktur3->id,
            'petugas_id' => $petugas->id,
            'petugas_nama' => $petugas->name,
            'tanggal_tugas' => Carbon::today()->toDateString(),
        ])->assertSessionHas('success');

        $tugasAll = PenugasanInkaso::whereIn('faktur_id', [$faktur1->id, $faktur2->id, $faktur3->id])->get();
        $this->assertCount(3, $tugasAll);
        $grupNumbers = $tugasAll->pluck('no_surat_grup')->unique();
        $this->assertCount(1, $grupNumbers);
        $noSuratGrup = $grupNumbers->first();
        $this->assertStringStartsWith('ST-', $noSuratGrup);

        // Sebelum surat diterima: Target Piutang Hari Ini masih Rp 0
        $beforeAccept = $this->actingAs($petugas)->get(route('keuangan.inkaso.mobile'));
        $beforeAccept->assertStatus(200);
        $beforeAccept->assertSee($noSuratGrup);
        $beforeAccept->assertSee('Terima Semua Surat Ini');
        $beforeAccept->assertSee('Rp 0');

        // 2. Petugas langsung terima seluruh surat grup
        $this->actingAs($petugas)
            ->post(route('keuangan.inkaso.penugasan.grup.konfirmasi', $noSuratGrup))
            ->assertSessionHas('success');

        // Target Piutang Hari Ini naik jadi Rp 6.000.000 (2jt + 3jt + 1jt)
        $afterAccept = $this->actingAs($petugas)->get(route('keuangan.inkaso.mobile'));
        $afterAccept->assertStatus(200);
        $afterAccept->assertSee('Rp 6.000.000');
        $afterAccept->assertSee('Selesai Penagihan Surat Ini');

        // 3. Faktur 1 dibayar lunas (2jt), Faktur 2 & 3 tidak bayar -> BSK pending memblokir selesai
        $tugas1 = PenugasanInkaso::where('faktur_id', $faktur1->id)->first();
        $this->actingAs($petugas)->post(route('keuangan.inkaso.setoran-lapangan.store'), [
            'faktur_id' => $faktur1->id,
            'penugasan_id' => $tugas1->id,
            'nominal_bayar' => 2000000,
            'metode_bayar' => 'tunai',
            'keterangan_angsuran' => 'Lunas faktur 1',
        ]);

        // Saat BSK pending: selesaiGrup harus menolak dengan warning
        $this->actingAs($petugas)
            ->post(route('keuangan.inkaso.penugasan.grup.selesai', $noSuratGrup))
            ->assertSessionHas('warning');

        // Kasir approve BSK faktur 1 -> target piutang berkurang jadi Rp 4.000.000
        $setoran1 = SetoranInkasoLapangan::where('faktur_id', $faktur1->id)->first();
        $this->actingAs($kasir)
            ->post(route('kasir.penerimaan.inkaso.approve', $setoran1->id))
            ->assertSessionHas('success');

        $afterApprove = $this->actingAs($petugas)->get(route('keuangan.inkaso.mobile'));
        $afterApprove->assertStatus(200);
        $afterApprove->assertSee('Rp 4.000.000');
        $afterApprove->assertSee('FAK-GRP-001'); // Rincian surat tetap menampilkan faktur 1 yang lunas
        $afterApprove->assertSee('Selesai Penagihan Surat Ini');

        // 4. Petugas selesaikan surat grup walau Faktur 2 & 3 belum bayar
        $selesaiRes = $this->actingAs($petugas)
            ->post(route('keuangan.inkaso.penugasan.grup.selesai', $noSuratGrup));
        $selesaiRes->assertRedirect(route('keuangan.inkaso.mobile', ['tab' => 'riwayat']));
        $selesaiRes->assertSessionHas('success');

        $this->assertEquals(0, PenugasanInkaso::where('no_surat_grup', $noSuratGrup)->where('status_tugas', 'berjalan')->count());

        $riwayatPage = $this->actingAs($petugas)->get(route('keuangan.inkaso.mobile', ['tab' => 'riwayat']));
        $riwayatPage->assertStatus(200);
        $riwayatPage->assertSee('Arsip Surat Penugasan Selesai');
        $riwayatPage->assertSee($noSuratGrup);
        $riwayatPage->assertSee('FAK-GRP-002');
        $riwayatPage->assertSee('FAK-GRP-003');
    }

    public function test_kasir_double_scan_and_duplicate_approve_are_rejected_with_validation_alert(): void
    {
        $petugas = User::factory()->create([
            'name' => 'Randi Anti Double',
            'email' => 'randi.double@kabarpriangan.co.id',
            'role' => 'petugas_inkaso',
        ]);

        $kasir = User::factory()->create([
            'name' => 'Siti Kasir Strict',
            'email' => 'siti.strict@kabarpriangan.co.id',
            'role' => 'kasir',
        ]);

        ShiftKasir::create([
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
            'nama_customer' => 'Agen Anti Double',
            'tipe' => 'iklan',
            'kota' => 'Tasikmalaya',
        ]);

        $faktur = Faktur::create([
            'no_faktur' => 'FAK-DBL-001',
            'kategori' => 'iklan',
            'tipe_faktur' => 'biasa',
            'customer_id' => $customer->id,
            'tanggal_faktur' => Carbon::today(),
            'jatuh_tempo' => Carbon::today()->addDays(7),
            'total_tagihan' => 5000000,
            'total_terbayar' => 0,
            'sisa_piutang' => 5000000,
            'status' => 'belum_bayar',
            'status_bayar' => 'belum_bayar',
            'created_by' => 1,
        ]);

        $setoran = SetoranInkasoLapangan::create([
            'no_bsk' => 'BSK-INK-20261125-9001',
            'faktur_id' => $faktur->id,
            'petugas_id' => $petugas->id,
            'nama_pembayar' => 'Agen Anti Double',
            'nominal_titipan' => 1500000,
            'metode_bayar' => 'tunai',
            'keterangan_angsuran' => 'Cicilan 1',
            'barcode_token' => 'BSK-INK-20261125-9001',
            'waktu_terima_lapangan' => Carbon::now(),
            'status_approval' => 'pending',
        ]);

        // Scan pertama berhasil (pending)
        $this->actingAs($kasir)
            ->getJson(route('kasir.api.scan-bsk', $setoran->barcode_token))
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        // Approve pertama berhasil
        $this->actingAs($kasir)
            ->post(route('kasir.penerimaan.inkaso.approve', $setoran->id))
            ->assertSessionHas('success');

        // Scan ke-2 pada barcode yang sama -> harus ditolak 409 + already_scanned = true
        $secondScan = $this->actingAs($kasir)
            ->getJson(route('kasir.api.scan-bsk', $setoran->barcode_token));
        $secondScan->assertStatus(409);
        $secondScan->assertJson([
            'success' => false,
            'already_scanned' => true,
            'status_approval' => 'approved',
        ]);

        // Submit approve ke-2 -> ditolak dan saldo tidak terpotong 2 kali
        $this->actingAs($kasir)
            ->post(route('kasir.penerimaan.inkaso.approve', $setoran->id))
            ->assertSessionHas('error');

        $this->actingAs($kasir)
            ->postJson(route('kasir.penerimaan.inkaso.approve', $setoran->id))
            ->assertStatus(409)
            ->assertJson(['success' => false, 'already_scanned' => true]);

        $faktur->refresh();
        $this->assertEquals(1500000, (float) $faktur->total_terbayar);
        $this->assertEquals(3500000, (float) $faktur->sisa_piutang);
        $this->assertEquals(1, KasMasuk::where('no_kuitansi_sementara', $setoran->no_bsk)->count());

        // BSK view juga menandai SUDAH DISETUJUI KASIR
        $this->actingAs($petugas)
            ->get(route('keuangan.inkaso.bsk.show', $setoran->no_bsk))
            ->assertStatus(200)
            ->assertSee('SUDAH DISETUJUI KASIR');
    }
}
