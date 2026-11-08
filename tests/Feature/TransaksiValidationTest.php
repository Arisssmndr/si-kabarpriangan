<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SalesAe;
use App\Models\JenisIklan;
use App\Models\KategoriMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $jenisOnl;
    protected $jenisPtv;
    protected $jenisKrn;
    protected $salesFo;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic dependencies for in-memory SQLite testing
        $mediaOnl = KategoriMedia::create([
            'kode' => 'ONL',
            'nama' => 'Online',
            'deskripsi' => 'Media Online',
            'is_active' => true,
        ]);

        $mediaPtv = KategoriMedia::create([
            'kode' => 'PTV',
            'nama' => 'Priangan TV',
            'deskripsi' => 'Priangan TV',
            'is_active' => true,
        ]);

        $mediaKrn = KategoriMedia::create([
            'kode' => 'KRN',
            'nama' => 'Koran Cetak',
            'deskripsi' => 'Koran Cetak',
            'is_active' => true,
        ]);

        $this->jenisOnl = JenisIklan::create([
            'kategori_media_id' => $mediaOnl->id,
            'kode_jenis' => 'ONL003',
            'nama_jenis' => 'Publikasi Portal Berita',
            'tipe_dasar' => 'PORTAL_BERITA',
            'tarif_dasar' => 750000,
            'is_active' => true,
        ]);

        $this->jenisPtv = JenisIklan::create([
            'kategori_media_id' => $mediaPtv->id,
            'kode_jenis' => 'PTV001',
            'nama_jenis' => 'Podcast Talkshow',
            'tipe_dasar' => 'PODCAST',
            'tarif_dasar' => 3500000,
            'is_active' => true,
        ]);

        $this->jenisKrn = JenisIklan::create([
            'kategori_media_id' => $mediaKrn->id,
            'kode_jenis' => 'DISP_BONUS',
            'nama_jenis' => 'Iklan Display Bonus / Barter / CSR',
            'tipe_dasar' => 'BONUS',
            'tarif_dasar' => 0,
            'is_active' => true,
        ]);

        $this->salesFo = SalesAe::create([
            'kode_ae' => 'FO',
            'nama_ae' => 'Front Office Loket',
            'jenis_kode' => 'FO',
            'is_active' => true,
        ]);
    }

    public function test_transaksi_online_store_rejects_empty_data()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('transaksionline.store'), [
            'nama_pemasangonline' => 'Budi Pemesan',
            'materi_iklan' => 'Promo Diskon',
            // Missing: id_iklanonline, sales_ae_id, jenis_pembayaran, harga > 0
        ]);

        $response->assertSessionHasErrors(['id_iklanonline', 'sales_ae_id', 'jenis_pembayaran']);
    }

    public function test_transaksi_online_store_rejects_zero_price_for_regular_ad()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('transaksionline.store'), [
            'nofakturonline' => 'ONL-TEST-001',
            'tanggal_transaksionline' => '2026-11-07',
            'nama_pemasangonline' => 'Budi Pemesan',
            'id_iklanonline' => $this->jenisOnl->id,
            'sales_ae_id' => $this->salesFo->id,
            'tanggal_muatiklanonline' => '2026-11-08',
            'total_muatiklanonline' => 1,
            'jenis_pembayaran' => 'Tunai',
            'materi_iklan' => 'Judul Berita Online',
            'harga_transaksionline' => '0',
            'jumlahbayar_transaksionline' => '0',
        ]);

        $response->assertSessionHasErrors(['harga_transaksionline']);
    }

    public function test_transaksi_priangan_store_rejects_missing_product_and_sales()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('transaksipriangan.store'), [
            'nama_pemasangpriangan' => 'Siti Pemesan',
            'materi_iklan' => 'Iklan Talkshow TV',
            // Missing: id_iklanpriangan, sales_ae_id, jenis_pembayaran
        ]);

        $response->assertSessionHasErrors(['id_iklanpriangan', 'sales_ae_id', 'jenis_pembayaran']);
    }

    public function test_transaksi_priangan_store_rejects_zero_price_for_regular_tv()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('transaksipriangan.store'), [
            'tanggal_transaksipriangan' => '2026-11-07',
            'nama_pemasangpriangan' => 'Siti Pemesan',
            'id_iklanpriangan' => $this->jenisPtv->id,
            'sales_ae_id' => $this->salesFo->id,
            'tanggal_muatiklanpriangan' => '2026-11-08',
            'jenis_pembayaran' => 'Tunai',
            'materi_iklan' => 'Materi Siaran TV',
            'durasi_detik' => 30,
            'harga_transaksipriangan' => '0',
            'jumlahbayar_transaksipriangan' => '0',
        ]);

        $response->assertSessionHasErrors(['harga_transaksipriangan']);
    }

    public function test_transaksi_koran_store_rejects_zero_price_for_regular_ad()
    {
        $user = User::factory()->create();
        $targetDate = now()->addDays(3)->toDateString();

        $response = $this->actingAs($user)->post(route('transaksikoran.store'), [
            'nofakturkoran' => 'KRN-TEST-001',
            'tanggal_transaksikoran' => now()->toDateString(),
            'nama_pemasangkoran' => 'Ahmad Pemesan',
            'id_iklankoran' => $this->jenisOnl->id,
            'kategori_form' => 'DISPLAY',
            'sales_ae_id' => $this->salesFo->id,
            'total_muatkoran' => 1,
            'tanggal_muat_list' => [$targetDate],
            'jenis_pembayaran' => 'Tunai',
            'harga_transaksikoran' => '0',
            'jumlahbayar_transaksikoran' => '0',
        ]);

        $response->assertSessionHasErrors(['harga_transaksikoran']);
    }

    public function test_transaksi_koran_store_allows_zero_price_for_bonus_package()
    {
        $user = User::factory()->create();
        $targetDate = now()->addDays(3)->toDateString();

        $response = $this->actingAs($user)->post(route('transaksikoran.store'), [
            'nofakturkoran' => 'KRN-BONUS-001',
            'tanggal_transaksikoran' => now()->toDateString(),
            'nama_pemasangkoran' => 'CSR Yayasan Peduli',
            'id_iklankoran' => $this->jenisKrn->id,
            'kategori_form' => 'BONUS',
            'sales_ae_id' => $this->salesFo->id,
            'total_muatkoran' => 1,
            'tanggal_muat_list' => [$targetDate],
            'jenis_pembayaran' => 'Tunai',
            'harga_transaksikoran' => '0',
            'jumlahbayar_transaksikoran' => '0',
        ]);

        $response->assertSessionDoesntHaveErrors(['harga_transaksikoran']);
    }

    public function test_transaksi_priangan_store_succeeds_without_file_materi_and_saves_cloud_url()
    {
        $user = User::factory()->create();
        $targetDate = now()->addDays(3)->toDateString();

        $response = $this->actingAs($user)->post(route('transaksipriangan.store'), [
            'tanggal_transaksipriangan' => now()->toDateString(),
            'nama_pemasangpriangan' => 'PT Maju Bersama Priangan',
            'id_iklanpriangan' => $this->jenisPtv->id,
            'sales_ae_id' => $this->salesFo->id,
            'tanggal_muatiklanpriangan' => $targetDate,
            'total_muatiklanpriangan' => 1,
            'jenis_pembayaran' => 'Tunai',
            'materi_iklan' => 'Talkshow Edukasi Digital',
            'durasi_detik' => 60,
            'jam_tayang' => '20:00 WIB',
            'catatan_redaksi' => 'Opening bumper 5 detik, closing logo',
            'url_materi_iklan' => 'https://drive.google.com/file/d/sample-large-video/view',
            'file_materi' => null, // File upload opsional
            'harga_transaksipriangan' => '1.500.000',
            'jumlahbayar_transaksipriangan' => '1.500.000',
        ]);

        $response->assertSessionDoesntHaveErrors();
        $this->assertDatabaseHas('transaksipriangan', [
            'nama_pemasangpriangan' => 'PT Maju Bersama Priangan',
            'materi_iklan' => 'Talkshow Edukasi Digital',
            'durasi_detik' => 60,
            'jam_tayang' => '20:00 WIB',
            'catatan_redaksi' => 'Opening bumper 5 detik, closing logo',
            'url_materi_iklan' => 'https://drive.google.com/file/d/sample-large-video/view',
            'file_materi' => null,
        ]);
    }
}
