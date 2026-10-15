<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BuktiPengeluaran extends Model
{
    use HasFactory;

    protected $table = 'bukti_pengeluarans';

    protected $fillable = [
        'no_bp',
        'no_register_anggaran',
        'pengajuan_anggaran_id',
        'divisi',
        'tanggal_pengajuan',
        'pemohon',
        'keperluan',
        'nominal_acc',
        'status',
        'approved_by',
        'dicairkan_oleh',
        'dicairkan_at',
    ];

    /**
     * Generator Nomor Register Anggaran (PR)
     * Format: PR-YYYYMMDD-XXXX
     */
    public static function generateNoRegister(): string
    {
        $ymd = date('Ymd');
        $prefix = "PR-{$ymd}-";
        $latest = self::where('no_register_anggaran', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('no_register_anggaran');

        $seq = 1;
        if ($latest && str_starts_with($latest, $prefix)) {
            $seq = intval(substr($latest, strlen($prefix))) + 1;
        }

        return $prefix . sprintf('%04d', $seq);
    }

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'nominal_acc'       => 'decimal:2',
        'dicairkan_at'      => 'datetime',
    ];

    /**
     * Relasi ke pengajuan anggaran asli di divisi keuangan
     */
    public function pengajuanAnggaran(): BelongsTo
    {
        return $this->belongsTo(PengajuanAnggaran::class, 'pengajuan_anggaran_id');
    }

    /**
     * Bukti Kas Keluar (BKK) kasir setelah dicairkan
     */
    public function kasKeluar(): HasOne
    {
        return $this->hasOne(KasKeluar::class, 'bp_id');
    }

    /**
     * Pejabat yang menyetujui anggaran (Keuangan / Anggaran)
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Petugas kasir yang mencairkan kas
     */
    public function disburser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicairkan_oleh');
    }

    /**
     * Helper status badge
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'approved' => [
                'bg'    => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'label' => 'Approved (Siap Cair)',
                'icon'  => 'check-circle',
            ],
            'dicairkan_kasir' => [
                'bg'    => 'bg-slate-100 text-slate-700 border-slate-300',
                'label' => 'Sudah Dicairkan',
                'icon'  => 'archive',
            ],
            'rejected' => [
                'bg'    => 'bg-rose-50 text-rose-700 border-rose-200',
                'label' => 'Ditolak',
                'icon'  => 'x-circle',
            ],
            default => [
                'bg'    => 'bg-amber-50 text-amber-700 border-amber-200',
                'label' => 'Pending Approval',
                'icon'  => 'clock',
            ],
        };
    }
}
