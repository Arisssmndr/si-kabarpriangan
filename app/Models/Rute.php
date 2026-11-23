<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rute extends Model
{
    use HasFactory;

    protected $table = 'rutes';

    protected $fillable = [
        'kode',
        'nama_rute',
        'armada',
        'sopir',
        'jam_berangkat',
        'titik_tujuan',
        'alokasi_oplah',
        'status',
    ];

    protected $casts = [
        'alokasi_oplah' => 'integer',
    ];

    protected $appends = [
        'kode_jurusan',
        'nama_jurusan',
    ];

    public function getKodeJurusanAttribute(): string
    {
        return (string) ($this->attributes['kode'] ?? '');
    }

    public function getNamaJurusanAttribute(): string
    {
        return (string) ($this->attributes['nama_rute'] ?? '');
    }

    /**
     * Agen yang terdaftar pada rute armada distribusi ini
     */
    public function agens(): HasMany
    {
        return $this->hasMany(Agen::class, 'rute_kode', 'kode');
    }
}
