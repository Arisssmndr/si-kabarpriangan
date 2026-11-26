<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutupAnggaran extends Model
{
    use HasFactory;

    protected $table = 'tutup_anggarans';

    protected $fillable = [
        'periode',
        'tanggal_tutup',
        'total_plafon',
        'total_bp_acc',
        'total_cair_kasir',
        'sisa_anggaran',
        'status',
        'catatan',
        'ditutup_oleh',
    ];

    protected $casts = [
        'tanggal_tutup' => 'date',
    ];

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditutup_oleh');
    }
}
