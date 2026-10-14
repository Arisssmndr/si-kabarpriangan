<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SistemConfig extends Model
{
    use HasFactory;

    protected $table = 'sistem_configs';

    protected $fillable = [
        'key',
        'value',
        'keterangan',
    ];

    /**
     * Ambil nilai config berdasarkan key dengan fallback default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $config = self::where('key', $key)->first();
        return $config !== null ? $config->value : $default;
    }

    /**
     * Simpan atau update konfigurasi.
     */
    public static function set(string $key, mixed $value, ?string $keterangan = null): self
    {
        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value,
                'keterangan' => $keterangan ?? self::where('key', $key)->value('keterangan'),
            ]
        );
    }

    /**
     * Helper untuk mendapatkan PPN persen (default 12%).
     */
    public static function getPpnPersen(): float
    {
        return (float) self::get('ppn_persen', 12);
    }

    /**
     * Helper untuk jam cutoff harian koran (default 14:00).
     */
    public static function getJamCutoff(): string
    {
        return (string) self::get('jam_cutoff_iklan', '14:00');
    }

    /**
     * Cek apakah saat ini sudah melewati jam cutoff iklan harian.
     */
    public static function isPastCutoff(): bool
    {
        $cutoffTime = self::getJamCutoff();
        $now = now()->format('H:i');
        return $now >= $cutoffTime;
    }
}
