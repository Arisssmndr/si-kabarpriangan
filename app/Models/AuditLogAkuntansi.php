<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLogAkuntansi extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'audit_log_akuntansi';

    protected $fillable = [
        'user_id',
        'aksi',
        'modul',
        'deskripsi',
        'payload_sebelum',
        'payload_sesudah',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'payload_sebelum' => 'array',
        'payload_sesudah' => 'array',
        'created_at'      => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Catat log audit akuntansi
     */
    public static function catat(string $aksi, string $modul, string $deskripsi, ?array $sebelum = null, ?array $sesudah = null): self
    {
        return self::create([
            'user_id'         => auth()->id(),
            'aksi'            => $aksi,
            'modul'           => $modul,
            'deskripsi'       => $deskripsi,
            'payload_sebelum' => $sebelum,
            'payload_sesudah' => $sesudah,
            'ip_address'      => request()->ip(),
            'created_at'      => now(),
        ]);
    }
}

