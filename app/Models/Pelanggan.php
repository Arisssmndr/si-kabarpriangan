<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';

    protected $fillable = [
        'kode_pelanggan',
        'nama_instansi',
        'jenis_klien',
        'npwp_nik',
        'alamat',
        'kontak_pic',
        'no_telp',
        'email',
        'credit_limit',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $pelanggan) {
            \App\Models\Customer::updateOrCreate(
                ['nama_customer' => $pelanggan->nama_instansi],
                [
                    'no_telepon' => $pelanggan->no_telp,
                    'alamat'     => $pelanggan->alamat,
                    'tipe'       => 'iklan',
                ]
            );
        });
    }

    public function transaksiKoran()
    {
        return $this->hasMany(TransaksiKoran::class, 'pelanggan_id');
    }

    public function transaksiOnline()
    {
        return $this->hasMany(TransaksiOnline::class, 'pelanggan_id');
    }

    public function transaksiPriangan()
    {
        return $this->hasMany(TransaksiPriangan::class, 'pelanggan_id');
    }

    /**
     * Hitung total piutang berjalan klien di seluruh koran.
     */
    public function getTotalPiutangKoranAttribute(): int
    {
        return (int) $this->transaksiKoran()->sum('piutang_transaksikoran');
    }

    /**
     * Hitung total piutang berjalan klien lintas seluruh media (Koran + Online + TV).
     */
    public function getTotalPiutangAttribute(): int
    {
        $koran = (int) $this->transaksiKoran()->sum('piutang_transaksikoran');
        $online = (int) $this->transaksiOnline()->sum('piutang_transaksionline');
        $tv = (int) $this->transaksiPriangan()->sum('piutang_transaksipriangan');
        return $koran + $online + $tv;
    }

    /**
     * Cek apakah klien ini melebihi batas kredit (credit limit).
     */
    public function isExceedingCreditLimit(int $tagihanBaru = 0): bool
    {
        if ($this->credit_limit <= 0) {
            return true; // 0 = Harus Cash Before Print (CBP)
        }
        return ($this->total_piutang + $tagihanBaru) > $this->credit_limit;
    }

    /**
     * Resolusi Pelanggan dari Request Form (Auto-link atau auto-create terpadu)
     */
    public static function resolveFromRequest($request, string $namaField, ?string $alamatField = null, ?string $telpField = null): ?int
    {
        $pelangganId = $request->pelanggan_id;
        $nama = trim($request->input($namaField) ?? '');

        if (!$pelangganId && !empty($nama)) {
            $existing = self::where('nama_instansi', $nama)->first();
            if ($existing) {
                return $existing->id;
            }

            $newPlg = self::create([
                'kode_pelanggan' => self::createCode(),
                'nama_instansi'  => $nama,
                'jenis_klien'    => 'Perorangan',
                'alamat'         => $alamatField ? $request->input($alamatField) : null,
                'no_telp'        => $telpField ? $request->input($telpField) : null,
                'kontak_pic'     => $request->input('nama_pic') ?? null,
                'is_active'      => true,
            ]);
            return $newPlg->id;
        }

        return $pelangganId ? (int) $pelangganId : null;
    }

    /**
     * Generate Kode Pelanggan Otomatis (Format: PLG-0001)
     */
    public static function createCode(): string
    {
        $latest = self::orderBy('id', 'desc')->first();
        if (!$latest) {
            return 'PLG-0001';
        }
        $num = intval(substr($latest->kode_pelanggan, 4)) + 1;
        return 'PLG-' . sprintf('%04d', $num);
    }
}
