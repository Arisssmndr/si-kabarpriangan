<?php

namespace Tests\Feature;

use App\Models\Agen;
use App\Models\Rute;
use App\Models\SirkulasiFakturBulanan;
use App\Models\SirkulasiRetur;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SirkulasiRuteDanAgenAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $userSirkulasi;
    protected User $userAccounting;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userSirkulasi = User::factory()->create([
            'role'   => 'sirkulasi',
            'status' => 'active',
        ]);

        $this->userAccounting = User::factory()->create([
            'role'   => 'accounting',
            'status' => 'active',
        ]);
    }

    public function test_laporan_penjualan_sirkulasi_loads_fast_without_qr_code_overhead()
    {
        $response = $this->actingAs($this->userSirkulasi)->get(route('sirkulasi.cetak.penjualan'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Penjualan Sirkulasi');
        $response->assertDontSee('Barcode QR');
        $response->assertDontSee('showQrModal');
        $response->assertDontSee('qr-svg');
    }

    public function test_sirkulasi_can_manage_master_rute()
    {
        // 1. Sirkulasi can view master rute list
        $response = $this->actingAs($this->userSirkulasi)->get(route('sirkulasi.tabel.rute'));
        $response->assertStatus(200);
        $response->assertSee('Master Rute');

        // 2. Sirkulasi can create a new route
        $storeResponse = $this->actingAs($this->userSirkulasi)->post(route('sirkulasi.tabel.rute.store'), [
            'kode'          => '99',
            'nama_rute'     => 'Jalur Uji Coba Cipatujah Timur',
            'armada'        => 'Mobil Box Testing (Z 9999 KP)',
            'sopir'         => 'Kru Testing',
            'jam_berangkat' => '02:00 WIB',
            'titik_tujuan'  => 'Cipatujah, Pamijahan',
            'alokasi_oplah' => 1200,
        ]);

        $storeResponse->assertRedirect(route('sirkulasi.tabel.rute'));
        $this->assertDatabaseHas('rutes', ['kode' => '99', 'nama_rute' => 'Jalur Uji Coba Cipatujah Timur']);

        // 3. Sirkulasi can update the route
        $rute = Rute::where('kode', '99')->first();
        $updateResponse = $this->actingAs($this->userSirkulasi)->put(route('sirkulasi.tabel.rute.update', $rute->id), [
            'nama_rute'     => 'Jalur Uji Coba Diperbarui',
            'armada'        => 'Mobil Box Testing Edit',
            'sopir'         => 'Kru Testing Edit',
            'jam_berangkat' => '02:30 WIB',
            'titik_tujuan'  => 'Cipatujah Baru',
            'alokasi_oplah' => 1500,
        ]);

        $updateResponse->assertRedirect(route('sirkulasi.tabel.rute'));
        $this->assertDatabaseHas('rutes', ['kode' => '99', 'nama_rute' => 'Jalur Uji Coba Diperbarui']);

        // 4. Sirkulasi can toggle status
        $this->actingAs($this->userSirkulasi)->patch(route('sirkulasi.tabel.rute.toggle', $rute->id));
        $this->assertEquals('nonaktif', $rute->fresh()->status);

        // 5. Sirkulasi can delete the route
        $deleteResponse = $this->actingAs($this->userSirkulasi)->delete(route('sirkulasi.tabel.rute.destroy', $rute->id));
        $deleteResponse->assertRedirect(route('sirkulasi.tabel.rute'));
        $this->assertDatabaseMissing('rutes', ['kode' => '99']);
    }

    public function test_sirkulasi_is_strictly_read_only_on_data_agen()
    {
        // View as Sirkulasi
        $response = $this->actingAs($this->userSirkulasi)->get(route('sirkulasi.tabel.agen'));
        $response->assertStatus(200);
        $response->assertSee('Mode Baca');
        $response->assertDontSee('modalTambahAgen');

        // Trying to POST as Sirkulasi must be rejected
        $postResponse = $this->actingAs($this->userSirkulasi)->post(route('sirkulasi.tabel.agen.store'), [
            'nama_agen'     => 'Agen Ilegal Sirkulasi',
            'kategori'      => '11',
            'wilayah_kode'  => '01',
            'jurusan_kode'  => '01',
            'jatah_default' => 50,
        ]);

        $postResponse->assertSessionHas('error');
        $this->assertDatabaseMissing('agens', ['nama_agen' => 'Agen Ilegal Sirkulasi']);
    }

    public function test_akuntansi_has_full_crud_authority_over_data_agen()
    {
        // 1. Akuntansi view agen management page
        $response = $this->actingAs($this->userAccounting)->get(route('accounting.sirkulasi.agen'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Mitra Agen Sirkulasi');

        // 2. Akuntansi can add a new agent with proper master relations
        $storeResponse = $this->actingAs($this->userAccounting)->post(route('accounting.sirkulasi.agen.store'), [
            'kode_agen'      => 'AG-11-999',
            'nama_agen'      => 'Agen Baru Akuntansi',
            'kategori'       => '11',
            'wilayah_kode'   => '01',
            'jurusan_kode'   => '01',
            'alamat'         => 'Jl. Dr. Sukardjo No. 45 Tasikmalaya',
            'telepon'        => '081234567890',
            'jatah_default'  => 250,
            'plafon_piutang' => 10000000,
            'induk_agen'     => 'Haji Ahmad',
        ]);

        $storeResponse->assertRedirect(route('accounting.sirkulasi.agen'));
        $this->assertDatabaseHas('agens', [
            'kode_agen'     => 'AG-11-999',
            'nama_agen'     => 'Agen Baru Akuntansi',
            'kategori'      => '11',
            'jatah_default' => 250,
        ]);

        $agen = Agen::where('kode_agen', 'AG-11-999')->first();

        // 3. Akuntansi can update the agent
        $updateResponse = $this->actingAs($this->userAccounting)->put(route('accounting.sirkulasi.agen.update', $agen->id), [
            'nama_agen'      => 'Agen Akuntansi Sukses',
            'kategori'       => '11',
            'wilayah_kode'   => '01',
            'jurusan_kode'   => '01',
            'alamat'         => 'Jl. Baru No. 100',
            'telepon'        => '081234567899',
            'jatah_default'  => 300,
            'plafon_piutang' => 15000000,
            'induk_agen'     => 'Haji Ahmad Edit',
        ]);

        $updateResponse->assertRedirect(route('accounting.sirkulasi.agen'));
        $this->assertDatabaseHas('agens', [
            'kode_agen'     => 'AG-11-999',
            'nama_agen'     => 'Agen Akuntansi Sukses',
            'jatah_default' => 300,
        ]);

        // 4. Akuntansi can toggle status
        $this->actingAs($this->userAccounting)->patch(route('accounting.sirkulasi.agen.toggle', $agen->id));
        $this->assertEquals('nonaktif', $agen->fresh()->status);

        // 5. Akuntansi can delete the agent
        $deleteResponse = $this->actingAs($this->userAccounting)->delete(route('accounting.sirkulasi.agen.destroy', $agen->id));
        $deleteResponse->assertRedirect(route('accounting.sirkulasi.agen'));
        $this->assertDatabaseMissing('agens', ['kode_agen' => 'AG-11-999']);
    }

    public function test_laporan_pemasaran_renders_all_eight_sirk_reports_with_flexible_filters_and_db_sync(): void
    {
        $agenTasik = Agen::create([
            'kode_agen'      => 'AG-11-771',
            'nama_agen'      => 'Agen Priangan Tasik',
            'kategori'       => '11',
            'rute_kode'      => '01',
            'wilayah'        => 'Tasikmalaya',
            'jatah_default'  => 100,
            'plafon_piutang' => 10000000,
            'saldo_piutang'  => 500000,
            'status'         => 'aktif',
        ]);

        $agenGarut = Agen::create([
            'kode_agen'      => 'AG-11-772',
            'nama_agen'      => 'Agen Intan Garut',
            'kategori'       => '11',
            'rute_kode'      => '02',
            'wilayah'        => 'Garut',
            'jatah_default'  => 80,
            'plafon_piutang' => 10000000,
            'saldo_piutang'  => 0,
            'status'         => 'aktif',
        ]);

        $tglTarget = '2026-11-26';
        Cache::put('jatah_harian_' . $tglTarget, [
            $agenTasik->id => ['langganan' => 70, 'konsinyasi' => 40, 'gratis' => 10, 'pengenalan' => 5, 'insidentil' => 0],
        ], now()->addDay());

        SirkulasiRetur::create([
            'no_bapr'                => 'BAPR-SRK-202611-901',
            'periode_bulan'          => '2026-11',
            'agen_id'                => $agenTasik->id,
            'kode_agen'              => $agenTasik->kode_agen,
            'nama_agen'              => $agenTasik->nama_agen,
            'tanggal_retur'          => $tglTarget,
            'jumlah_retur_fisik'     => 15,
            'jumlah_retur_disetujui' => 15,
            'status'                 => 'disetujui',
        ]);

        SirkulasiFakturBulanan::create([
            'no_faktur'            => 'FAK-SRK-202611-771',
            'periode_bulan'        => '2026-11',
            'agen_id'              => $agenTasik->id,
            'kode_agen'            => $agenTasik->kode_agen,
            'nama_agen'            => $agenTasik->nama_agen,
            'kategori_agen'        => '11',
            'wilayah'              => 'Tasikmalaya',
            'total_oplah_kirim'    => 3000,
            'total_retur_eks'      => 15,
            'oplah_bersih'         => 2985,
            'tarif_per_eks'        => 2800,
            'total_tagihan_kotor'  => 8400000,
            'total_potongan_retur' => 42000,
            'total_tagihan_bersih' => 8358000,
            'total_terbayar'       => 8358000,
            'sisa_piutang'         => 0,
            'status_pembayaran'    => 'lunas',
            'tanggal_faktur'       => '2026-11-26',
            'jatuh_tempo'          => '2026-12-10',
        ]);

        $tabs = [
            'agen-besar',
            'sub-agen',
            'rekap-terbit-bulan',
            'rekap-wilayah',
            'rekap-jurusan',
            'penerimaan-koran',
            'oplah-tahun',
            'rekap-bln-wilayah',
        ];

        foreach ($tabs as $tab) {
            $resp = $this->actingAs($this->userSirkulasi)->get(route('sirkulasi.cetak.pemasaran', [
                'tab'          => $tab,
                'mode_periode' => 'harian',
                'tanggal'      => $tglTarget,
            ]));
            $resp->assertStatus(200);
            $resp->assertSee('Mode Periode:');
            $resp->assertSee('Cetak Laporan Pemasaran');
        }

        // Verifikasi filter wilayah + pencarian + harian bekerja sekaligus pada tampilan & print
        $filteredResp = $this->actingAs($this->userSirkulasi)->get(route('sirkulasi.cetak.pemasaran', [
            'tab'          => 'agen-besar',
            'mode_periode' => 'harian',
            'tanggal'      => $tglTarget,
            'wilayah'      => '01',
            'q'            => 'Priangan Tasik',
        ]));
        $filteredResp->assertStatus(200);
        $filteredResp->assertSee('Agen Priangan Tasik');
        $filteredResp->assertSee('125 eks'); // 70+40+10+5 dari cache harian
        $filteredResp->assertDontSee('Agen Intan Garut');

        // Verifikasi tab Penerimaan Koran terhubung ke SirkulasiFakturBulanan
        $penerimaanResp = $this->actingAs($this->userSirkulasi)->get(route('sirkulasi.cetak.pemasaran', [
            'tab'          => 'penerimaan-koran',
            'mode_periode' => 'bulanan',
            'bulan'        => 11,
            'tahun'        => 2026,
            'status_bayar' => 'lunas',
        ]));
        $penerimaanResp->assertStatus(200);
        $penerimaanResp->assertSee('FAK-SRK-202611-771');
        $penerimaanResp->assertSee('Rp 8.358.000');

        // Verifikasi halaman Cetak Fleksibel mencetak strictly sesuai filter aktif
        $printResp = $this->actingAs($this->userSirkulasi)->get(route('sirkulasi.cetak.pemasaran.print', [
            'tab'          => 'agen-besar',
            'mode_periode' => 'custom',
            'dari'         => $tglTarget,
            'sampai'       => $tglTarget,
            'wilayah'      => '01',
            'jurusan'      => '01',
            'q'            => 'Priangan Tasik',
        ]));
        $printResp->assertStatus(200);
        $printResp->assertSee('LAPORAN AGEN BESAR');
        $printResp->assertSee('Agen Priangan Tasik');
        $printResp->assertDontSee('Agen Intan Garut');
        $printResp->assertSee('125 eks');
    }
}
