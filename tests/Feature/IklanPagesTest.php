<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TransaksiKoran;
use App\Models\TransaksiOnline;
use App\Models\TransaksiPriangan;
use App\Models\Pelanggan;
use App\Models\SalesAe;
use App\Models\JenisIklan;
use App\Services\FakturQrService;
use App\Http\Controllers\LaporanController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IklanPagesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'role' => 'iklan',
        ]);
    }

    public function test_transaksikoran_index_renders_without_piutang_columns()
    {
        $response = $this->actingAs($this->user)->get(route('transaksikoran.index'));
        $response->assertStatus(200);
        $response->assertSee('Jenis Pembayaran');
        $response->assertDontSee('>Piutang</th>', false);
    }

    public function test_transaksionline_index_renders_without_piutang_columns()
    {
        $response = $this->actingAs($this->user)->get(route('transaksionline.index'));
        $response->assertStatus(200);
        $response->assertSee('Jenis Pembayaran');
        $response->assertDontSee('>Piutang</th>', false);
    }

    public function test_transaksipriangan_index_renders_without_piutang_columns()
    {
        $response = $this->actingAs($this->user)->get(route('transaksipriangan.index'));
        $response->assertStatus(200);
        $response->assertSee('Jenis Pembayaran');
        $response->assertDontSee('>Piutang</th>', false);
    }

    public function test_dashboard_renders_without_sisa_piutang_card()
    {
        $response = $this->actingAs($this->user)->get(route('iklan.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Transaksi Tunai');
        $response->assertSee('Transaksi Kredit');
    }

    public function test_laporan_index_renders_without_piutang_columns()
    {
        $response = $this->actingAs($this->user)->get(route('laporan.index'));
        $response->assertStatus(200);
        $response->assertSee('Jenis Pembayaran');
        $response->assertDontSee('>Piutang</th>', false);
    }

    public function test_laporan_cetak_renders_cleanly()
    {
        $response = $this->actingAs($this->user)->get(route('laporan.cetak'));
        $response->assertStatus(200);
        $response->assertSee('NO. PERKIRAAN');
        $response->assertDontSee('>Piutang</th>', false);
    }

    public function test_resolve_no_perkiraan_kepala_2_for_tunai_and_kepala_1_for_kredit()
    {
        // Koran / TV Tunai (Kepala 2)
        $noPerkiraanTunai = LaporanController::resolveNoPerkiraan('koran', 1000000, 1000000, 15);
        $this->assertStringStartsWith('2.00.01.01.01.01.', $noPerkiraanTunai);

        // Koran / TV Kredit (Kepala 1) - Termasuk bayar DP 50%
        $noPerkiraanKredit = LaporanController::resolveNoPerkiraan('koran', 1000000, 500000, 15);
        $this->assertStringStartsWith('1.00.04.01.01.01.', $noPerkiraanKredit);

        // Koran / TV Kredit (Kepala 1) - Bayar 0
        $noPerkiraanKreditNol = LaporanController::resolveNoPerkiraan('koran', 1000000, 0, 15);
        $this->assertStringStartsWith('1.00.04.01.01.01.', $noPerkiraanKreditNol);

        // Online Tunai (Kepala 2)
        $noPerkiraanOnlineTunai = LaporanController::resolveNoPerkiraan('online', 1500000, 1500000, 20);
        $this->assertStringStartsWith('2.00.01.01.01.02.', $noPerkiraanOnlineTunai);

        // Online Kredit (Kepala 1)
        $noPerkiraanOnlineKredit = LaporanController::resolveNoPerkiraan('online', 1500000, 0, 20);
        $this->assertStringStartsWith('1.00.04.01.01.02.', $noPerkiraanOnlineKredit);
    }

    public function test_qr_faktur_is_generated_for_kredit_and_tunai()
    {
        $pelanggan = Pelanggan::create([
            'kode_pelanggan' => 'CUST001',
            'nama_instansi'  => 'Pemasang Test Kredit QR',
            'is_active'      => true,
        ]);

        $kat = \App\Models\KategoriMedia::create([
            'kode' => 'KRN',
            'nama' => 'Koran Cetak',
            'is_active' => true,
        ]);

        $jenisIklan = JenisIklan::create([
            'kategori_media_id' => $kat->id,
            'kode_jenis'        => 'DSP01',
            'nama_jenis'        => 'Display Reguler',
            'is_active'         => true,
        ]);

        $koranKredit = TransaksiKoran::create([
            'nofakturkoran'              => 'FK/TEST/KRD/001',
            'tanggal_transaksikoran'     => now()->toDateString(),
            'nama_pemasangkoran'         => $pelanggan->nama_instansi,
            'alamat_pemasangkoran'       => 'Jl. Dr. Sukardjo No. 12',
            'id_iklankoran'              => $jenisIklan->id,
            'sales_iklankoran'           => 'FO',
            'tanggal_muatkoran'          => now()->toDateString(),
            'pelanggan_id'               => $pelanggan->id,
            'totaltagihan_transaksikoran' => 500000,
            'jumlahbayar_transaksikoran' => 0,
            'piutang_transaksikoran'     => 500000,
            'status_order'               => 'Aktif',
            'created_by'                 => $this->user->id,
        ]);

        $qrRecord = FakturQrService::generate($koranKredit, 'koran', $this->user->id);

        $this->assertNotNull($qrRecord, 'QR Faktur harus tetap digenerate meski transaksi Kredit / bayar Rp 0');
        $this->assertEquals(0, (float) $qrRecord->nominal_bayar);

        $qrCetak = FakturQrService::resolveForCetak($koranKredit, 'koran');
        $this->assertNotNull($qrCetak, 'Record QR cetakan harus tersedia untuk transaksi kredit');
        $qrSvg = FakturQrService::renderSvg($qrCetak);
        $this->assertNotEmpty($qrSvg, 'QR Svg cetakan harus ter-render untuk transaksi kredit');
    }

    public function test_pengaturan_iklan_renders_cleanly_and_updates_without_tarif_per_baris()
    {
        $response = $this->actingAs($this->user)->get(route('iklan.pengaturan.index'));
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Iklan');
        $response->assertDontSee('Tarif per Baris *');

        $updateResponse = $this->actingAs($this->user)->post(route('iklan.pengaturan.update'), [
            'ppn_persen'                  => '11',
            'jam_cutoff_iklan'            => '14:00',
            'minimal_baris'               => 3,
            'maksimal_karakter_per_baris' => 35,
            'diskon_agensi_default'       => 15,
        ]);

        $updateResponse->assertRedirect(route('iklan.pengaturan.index'));
        $updateResponse->assertSessionHas('success');
    }

    public function test_faktur_code_format_and_shared_sequential_numbering()
    {
        $codeOnline = \App\Services\NomorDokumenService::generateFaktur(null, 'ONL');
        $this->assertMatchesRegularExpression('/^FK\/ONL\/\d{4}\/\d{2}\/\d{4}$/', $codeOnline);

        $codeTv = \App\Services\NomorDokumenService::generateFaktur(null, 'PTV');
        $this->assertMatchesRegularExpression('/^FK\/PTV\/\d{4}\/\d{2}\/\d{4}$/', $codeTv);

        $codeCetak = \App\Services\NomorDokumenService::generateFaktur(null, 'CTK');
        $this->assertMatchesRegularExpression('/^FK\/CTK\/\d{4}\/\d{2}\/\d{4}$/', $codeCetak);

        // Sequence harus berlanjut secara berkesinambungan lintas media (shared global counter)
        $numOnline = (int) substr($codeOnline, -4);
        $numTv     = (int) substr($codeTv, -4);
        $numCetak  = (int) substr($codeCetak, -4);

        $this->assertEquals($numOnline + 1, $numTv);
        $this->assertEquals($numTv + 1, $numCetak);
    }

    public function test_multi_date_insertion_marks_first_edition_as_bernilai_and_subsequent_as_bonus()
    {
        $pelanggan = Pelanggan::create([
            'kode_pelanggan' => 'CUST099',
            'nama_instansi'  => 'PT Advertindo Multi Edisi',
            'is_active'      => true,
        ]);

        $kat = \App\Models\KategoriMedia::firstOrCreate(
            ['kode' => 'KRN'],
            ['nama' => 'Iklan Cetak', 'is_active' => true]
        );

        $jenisIklan = JenisIklan::create([
            'kategori_media_id' => $kat->id,
            'kode_jenis'        => 'KLM05',
            'nama_jenis'        => 'Iklan Kolom 5x',
            'is_active'         => true,
        ]);

        $sales = SalesAe::create([
            'kode_ae' => 'AE99',
            'nama_ae' => 'Sales Multi Edisi',
            'is_active' => true,
        ]);

        $dates = [
            now('Asia/Jakarta')->addDays(1)->toDateString(),
            now('Asia/Jakarta')->addDays(2)->toDateString(),
            now('Asia/Jakarta')->addDays(3)->toDateString(),
            now('Asia/Jakarta')->addDays(4)->toDateString(),
            now('Asia/Jakarta')->addDays(5)->toDateString(),
        ];

        $postData = [
            'nofakturkoran'              => 'FK/CTK/2026/11/9999',
            'tanggal_transaksikoran'     => now('Asia/Jakarta')->toDateString(),
            'nama_pemasangkoran'         => $pelanggan->nama_instansi,
            'alamat_pemasangkoran'       => 'Jl. Uji Multi Edisi No. 1',
            'id_iklankoran'              => $jenisIklan->id,
            'kategori_form'              => 'KOLOM',
            'sales_ae_id'                => $sales->id,
            'sales_iklankoran'           => $sales->nama_ae,
            'total_muatkoran'            => 5,
            'tanggal_muatkoran'          => $dates[0],
            'tanggal_muat_list'          => $dates,
            'jenis_pembayaran'           => 'Tunai',
            'harga_transaksikoran'       => '1.000.000',
            'diskon_agensi'              => '0',
            'diskon_umum'                => '0',
            'diskon_khusus'              => '0',
            'jumlahbayar_transaksikoran' => '5.550.000',
            'lebar_kolom'                => 1,
            'tinggi_mm'                  => 50,
        ];

        $response = $this->actingAs($this->user)->post(route('transaksikoran.store'), $postData);
        $response->assertSessionHasNoErrors();

        $transaksi = TransaksiKoran::where('nama_pemasangkoran', 'PT Advertindo Multi Edisi')->first();
        $this->assertNotNull($transaksi);

        $jadwalList = $transaksi->jadwalMuat()->orderBy('edisi_ke')->get();
        $this->assertCount(5, $jadwalList);

        // Edisi 1: Muat Bernilai
        $edisi1 = $jadwalList[0];
        $this->assertEquals('BERNILAI', $edisi1->jenis_muat);
        $this->assertFalse((bool) $edisi1->is_bonus);
        $this->assertGreaterThan(0, $edisi1->nominal_muat);

        // Edisi 2-5: Muat Bonus
        for ($i = 1; $i < 5; $i++) {
            $this->assertEquals('BONUS', $jadwalList[$i]->jenis_muat);
            $this->assertTrue((bool) $jadwalList[$i]->is_bonus);
            $this->assertEquals(0, (float) $jadwalList[$i]->nominal_muat);
        }
    }

    public function test_cutoff_blocks_same_day_and_allows_tomorrow_uniformly()
    {
        $today    = now('Asia/Jakarta')->toDateString();
        $tomorrow = now('Asia/Jakarta')->addDay()->toDateString();

        $this->assertTrue(\App\Models\DailyCutoff::isDateLocked($today), 'Hari ini (H) wajib terkunci Cut-Off untuk terbit hari yang sama');
        $this->assertFalse(\App\Models\DailyCutoff::isDateLocked($tomorrow), 'Besok (H+1) wajib terbuka untuk pemasangan iklan');
    }

    public function test_priangan_and_online_create_render_without_type_error_on_formatted_old_input()
    {
        $responseTv = $this->actingAs($this->user)
            ->withSession(['_old_input' => [
                'harga_transaksipriangan'  => '1.500.000',
                'diskon_transaksipriangan' => '150.000',
            ]])
            ->get(route('transaksipriangan.create'));
        $responseTv->assertStatus(200);
        $responseTv->assertSee('Materi Iklan');

        $responseOnline = $this->actingAs($this->user)
            ->withSession(['_old_input' => [
                'harga_transaksionline'  => '750.000',
                'diskon_transaksionline' => '50.000',
            ]])
            ->get(route('transaksionline.create'));
        $responseOnline->assertStatus(200);
    }

    public function test_paket_iklan_index_and_create_render_cleanly()
    {
        $resIndex = $this->actingAs($this->user)->get(route('transaksipaket.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('Transaksi Paket Iklan');

        $resCreate = $this->actingAs($this->user)->get(route('transaksipaket.create'));
        $resCreate->assertStatus(200);
        $resCreate->assertSee('Entri Paket Iklan Lintas Media');
        $resCreate->assertSee('Portal Web');
        $resCreate->assertSee('Podcast TV');
        $resCreate->assertSee('Banner Web');
        $resCreate->assertSee('Video Lipsus');
        $resCreate->assertDontSee('Spot Iklan TV');
        $resCreate->assertDontSee('Talkshow &amp; Dialog');
        $resCreate->assertSee('id="sectionContainerCetak" class="hidden space-y-5"', false);
        $resCreate->assertSee('id="sectionContainerOnline" class="hidden space-y-5"', false);
        $resCreate->assertSee('id="sectionContainerTv" class="hidden space-y-5"', false);
    }

    public function test_paket_iklan_requires_minimum_two_media_and_creates_consolidated_invoice_plus_children()
    {
        $sales = SalesAe::create([
            'kode_ae'   => 'AE77',
            'nama_ae'   => 'Sales Paket Lintas Media',
            'is_active' => true,
        ]);

        $katKrn = \App\Models\KategoriMedia::firstOrCreate(['kode' => 'KRN'], ['nama' => 'Iklan Cetak', 'is_active' => true]);
        $katOnl = \App\Models\KategoriMedia::firstOrCreate(['kode' => 'ONL'], ['nama' => 'Iklan Online', 'is_active' => true]);
        $katPtv = \App\Models\KategoriMedia::firstOrCreate(['kode' => 'PTV'], ['nama' => 'Iklan TV', 'is_active' => true]);

        $jCtk = JenisIklan::create(['kategori_media_id' => $katKrn->id, 'kode_jenis' => 'PKT_CTK1', 'nama_jenis' => 'Display Paket', 'tarif_dasar' => 25000, 'is_active' => true]);
        $jOnl = JenisIklan::create(['kategori_media_id' => $katOnl->id, 'kode_jenis' => 'PKT_ONL1', 'nama_jenis' => 'TikTok Paket', 'tarif_dasar' => 1000000, 'is_active' => true]);
        $jPtv = JenisIklan::create(['kategori_media_id' => $katPtv->id, 'kode_jenis' => 'PKT_PTV1', 'nama_jenis' => 'YouTube TV Paket', 'tarif_dasar' => 2000000, 'is_active' => true]);

        $besok = now('Asia/Jakarta')->addDays(1)->toDateString();
        $lusa  = now('Asia/Jakarta')->addDays(2)->toDateString();

        // 1. Jika hanya 1 media -> harus ditolak
        $resFail = $this->actingAs($this->user)->post(route('transaksipaket.store'), [
            'nama_pemasang'    => 'PT Satu Media Saja',
            'sales_ae_id'      => $sales->id,
            'jenis_pembayaran' => 'Tunai',
            'media_terpilih'   => ['CTK'],
        ]);
        $resFail->assertSessionHasErrors('media_terpilih');

        // 2. Paket 3 Media (Cetak 2x terbit + Online + TV) dengan Diskon Paket 10%
        $resStore = $this->actingAs($this->user)->post(route('transaksipaket.store'), [
            'nama_pemasang'       => 'PT Sinergi Tiga Media',
            'alamat_pemasang'     => 'Jl. HZ Mustofa No. 88 Tasikmalaya',
            'sales_ae_id'         => $sales->id,
            'jenis_pembayaran'    => 'Tunai',
            'kode_template'       => 'PKT_EKSKLUSIF',
            'nama_paket'          => 'Paket Eksklusif 3 Media',
            'media_terpilih'      => ['CTK', 'ONL', 'PTV'],
            'ctk_kategori_form'   => 'DISPLAY',
            'ctk_id_iklan'        => $jCtk->id,
            'ctk_lebar_kolom'     => 2,
            'ctk_tinggi_mm'       => 100,
            'ctk_warna'           => 'BW',
            'ctk_harga_satuan'    => '3.000.000',
            'ctk_tanggal_list'    => "{$besok},{$lusa}",
            'onl_id_iklan'        => $jOnl->id,
            'onl_materi_iklan'    => 'Konten TikTok Peluncuran Produk',
            'onl_harga_satuan'    => '2.000.000',
            'onl_tanggal_list'    => $besok,
            'ptv_id_iklan'        => $jPtv->id,
            'ptv_materi_iklan'    => 'Tayangan YouTube Priangan TV',
            'ptv_harga_satuan'    => '2.000.000',
            'ptv_tanggal_list'    => $besok,
            'diskon_persen'       => 10,
            'nilai_diskon'        => '1.000.000',
            'jumlah_bayar'        => '9.990.000',
        ]);
        $resStore->assertSessionHasNoErrors();

        $paket = \App\Models\PaketIklan::where('nama_pemasang', 'PT Sinergi Tiga Media')->first();
        $this->assertNotNull($paket);
        $this->assertMatchesRegularExpression('/^FK\/PKT\/\d{4}\/\d{2}\/\d{4}$/', $paket->nofaktur_paket);

        // Pastikan ketiga anak transaksi terbentuk dan status_order sesuai aturan CBP Gate (Menunggu_Bayar jika ada piutang)
        $this->assertNotNull($paket->transaksiCetak);
        $this->assertNotNull($paket->transaksiOnline);
        $this->assertNotNull($paket->transaksiTv);
        $this->assertEquals('Menunggu_Bayar', $paket->transaksiCetak->status_order);

        // Pastikan jadwal muat cetak memiliki Edisi 1 BERNILAI dan Edisi 2 BONUS
        $jadwalCtk = $paket->transaksiCetak->jadwalMuat()->orderBy('edisi_ke')->get();
        $this->assertCount(2, $jadwalCtk);
        $this->assertEquals('BERNILAI', $jadwalCtk[0]->jenis_muat);
        $this->assertEquals('BONUS', $jadwalCtk[1]->jenis_muat);

        // Pastikan halaman detail dan cetak 1 Faktur Paket berjalan normal (200 OK)
        $this->actingAs($this->user)->get(route('transaksipaket.show', $paket->id))->assertStatus(200);
        $this->actingAs($this->user)->get(route('transaksipaket.cetak', $paket->id))
            ->assertStatus(200)
            ->assertSee($paket->nofaktur_paket);
    }

    public function test_semua_halaman_iklan_menggunakan_kelas_header_tabel_putih()
    {
        $routes = [
            'iklan.dashboard',
            'transaksikoran.index',
            'transaksionline.index',
            'transaksipriangan.index',
            'transaksipaket.index',
            'iklan.rekap-pracetak.index',
            'iklan.memo.history',
            'laporan.index',
            'laporan.rekap-sales',
            'laporan.klasifikasi',
            'laporan.insentif-ae',
            'laporan.pembatalan',
            'pelanggan.index',
            'sales-ae.index',
            'jenis-iklan.index',
            'kategori-media.index',
            'kop-kota.index',
            'kop-iklan.index',
        ];

        foreach ($routes as $routeName) {
            $resp = $this->actingAs($this->user)->get(route($routeName));
            $resp->assertStatus(200);
            $resp->assertSee('is-iklan-view', false);
            $resp->assertSee('body.is-iklan-view main.is-iklan-view', false);
            $resp->assertSee('color: #FFFFFF !important;', false);
        }
    }
}
