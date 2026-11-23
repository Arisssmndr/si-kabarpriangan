<?php

namespace Tests\Feature;

use App\Models\Agen;
use App\Models\Rute;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
