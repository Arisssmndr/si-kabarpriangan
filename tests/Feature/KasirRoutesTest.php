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
        $koran = TransaksiKoran::first();

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
        $this->assertDatabaseHas('shift_kasirs', [
            'kasir_id' => $user->id,
            'status'   => 'closed',
        ]);
    }
}
