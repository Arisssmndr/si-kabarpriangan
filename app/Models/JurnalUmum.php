<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalUmum extends Model
{
    use HasFactory;

    protected $table = 'jurnal_umum';

    protected $fillable = [
        'no_jurnal',
        'tanggal',
        'tipe',
        'sumber',
        'referensi',
        'keterangan',
        'total_debet',
        'total_kredit',
        'is_balanced',
        'status',
        'user_id',
    ];

    protected $casts = [
        'tanggal'      => 'date',
        'total_debet'  => 'decimal:2',
        'total_kredit' => 'decimal:2',
        'is_balanced'  => 'boolean',
    ];

    public function details()
    {
        return $this->hasMany(JurnalUmumDetail::class, 'jurnal_umum_id')->orderBy('urutan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Generate Nomor Jurnal Urut Standar: JU/YYYY/MM/XXXX
     */
    public static function generateNomorJurnal(?string $date = null): string
    {
        $d = $date ? \Carbon\Carbon::parse($date) : now();
        $prefix = 'JU/' . $d->format('Y/m') . '/';

        $last = self::where('no_jurnal', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $seq = 1;
        if ($last && preg_match('/(\d{4})$/', $last->no_jurnal, $m)) {
            $seq = ((int) $m[1]) + 1;
        }

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}

