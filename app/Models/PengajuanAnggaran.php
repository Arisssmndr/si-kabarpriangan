<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PengajuanAnggaran extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_anggaran';

    protected $fillable = [
        'no_pengajuan',
        'divisi',
        'tanggal_pengajuan',
        'nominal_diajukan',
        'nominal_disetujui',
        'status',
        'approved_by',
        'cair_by',
        'tanggal_cair',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_cair'      => 'date',
        'nominal_diajukan'  => 'decimal:2',
        'nominal_disetujui' => 'decimal:2',
    ];

    /**
     * User pembuat pengajuan.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Pejabat yang menyetujui (Kepala Divisi / Admin Keuangan).
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Kasir yang mencairkan dana.
     */
    public function disburser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cair_by');
    }

    /**
     * Bukti Pengeluaran (BP) yang terhubung
     */
    public function buktiPengeluaran()
    {
        return $this->hasOne(BuktiPengeluaran::class, 'pengajuan_anggaran_id');
    }

    /**
     * Catatan mutasi kas keluar saat pencairan.
     */
    public function mutasiKas(): MorphMany
    {
        return $this->morphMany(KasTransaksi::class, 'referensi', 'referensi_tipe', 'referensi_id');
    }

    /**
     * Label divisi yang mudah dibaca.
     */
    public function getNamaDivisiAttribute(): string
    {
        return match ($this->divisi) {
            'iklan'          => 'Divisi Iklan',
            'sirkulasi'      => 'Divisi Sirkulasi',
            'sekum'          => 'Sekretariat Umum (Sekum)',
            'redaksi'        => 'Divisi Redaksi',
            'akuntansi'      => 'Divisi Akuntansi',
            'keuangan'       => 'Divisi Keuangan',
            'bank'           => 'Kas & Bank',
            'management_fee' => 'Management Fee',
            default          => strtoupper($this->divisi),
        };
    }
}
