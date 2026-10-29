<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnggaranRab extends Model
{
    use HasFactory;

    protected $table = 'anggaran_rabs';

    protected $fillable = [
        'periode',
        'kode_bagian',
        'divisi',
        'plafon_anggaran',
        'keterangan',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
