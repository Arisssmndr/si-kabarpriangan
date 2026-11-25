<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPerkiraan extends Model
{
    use HasFactory;

    protected $table = 'master_perkiraans';

    protected $fillable = [
        'kode',
        'nama',
        'kode_bagian',
        'bidang',
        'kategori',
        'tipe',
        'keterangan',
        'default_alokasi',
        'is_active',
    ];

    protected $casts = [
        'default_alokasi' => 'decimal:2',
        'is_active'       => 'boolean',
    ];

    /**
     * Hitung total transaksi dan rupiah pemakaian akun COA ini pada Bukti Pengeluaran (BP)
     */
    public function getStatistikPenggunaanAttribute(): array
    {
        $bps = BuktiPengeluaran::where('kode_perkiraan', $this->kode)->get();
        return [
            'total_bp'     => $bps->count(),
            'total_rupiah' => (float) $bps->whereIn('status', ['approved', 'dicairkan_kasir'])->sum('nominal_acc'),
        ];
    }

    /**
     * Seed data awal standar COA jika tabel masih kosong
     */
    public static function seedDefaultData(): void
    {
        $seeder = new \Database\Seeders\MasterPerkiraanSeeder();
        $seeder->run();
    }
}

