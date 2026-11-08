<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemoUnlock extends Model
{
    use HasFactory;

    protected $table = 'memo_unlocks';

    protected $fillable = [
        'tanggal_target',
        'nomor_memo',
        'kategori_urgensi',
        'nama_pemasang',
        'spesifikasi_iklan',
        'estimasi_nominal',
        'alasan',
        'tujuan_divisi',
        'status_akuntansi',
        'diketahui_oleh',
        'dibuka_oleh_user_id',
        'status',
        'dibuka_pada',
        'ditutup_kembali_pada',
        'diterima_akuntansi_pada',
    ];

    protected $casts = [
        'tanggal_target'          => 'date',
        'estimasi_nominal'        => 'integer',
        'dibuka_pada'             => 'datetime',
        'ditutup_kembali_pada'    => 'datetime',
        'diterima_akuntansi_pada' => 'datetime',
    ];

    public function dibukaOleh()
    {
        return $this->belongsTo(User::class, 'dibuka_oleh_user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'dibuka_oleh_user_id');
    }

    public function getPeriodeAttribute(): string
    {
        return $this->tanggal_target ? \Carbon\Carbon::parse($this->tanggal_target)->format('Y-m-d') : '-';
    }

    // Compatibility accessors
    public function getNoMemoAttribute(): ?string
    {
        return $this->nomor_memo;
    }

    public function getAlasanBukaAttribute(): ?string
    {
        return $this->alasan;
    }

    public function getDiajukanOlehAttribute(): ?string
    {
        return $this->diketahui_oleh ?: 'User';
    }

    public function getStatusPersetujuanAttribute(): string
    {
        return $this->status ?: 'Disetujui';
    }

    /**
     * Generate Nomor Memo Digital Otomatis Standar Pers: MEMO/IKL/YYYY/MM/XXXX
     */
    public static function generateNomorMemo(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $prefix = "MEMO/IKL/{$year}/{$month}/";

        $lastMemo = self::where('nomor_memo', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;
        if ($lastMemo && preg_match('/(\d{3,4})$/', $lastMemo->nomor_memo, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        } else {
            $nextNumber = self::whereYear('created_at', $year)->count() + 1;
        }

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
