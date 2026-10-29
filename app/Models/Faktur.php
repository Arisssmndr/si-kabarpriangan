<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faktur extends Model
{
    use HasFactory;

    protected $table = 'faktur';

    protected $fillable = [
        'no_faktur',
        'customer_id',
        'kategori',
        'tanggal_faktur',
        'jatuh_tempo',
        'rincian_biaya',
        'total_tagihan',
        'total_terbayar',
        'sisa_piutang',
        'status',
        'created_by',
        'catatan',
    ];

    protected $casts = [
        'tanggal_faktur' => 'date',
        'jatuh_tempo'    => 'date',
        'rincian_biaya'  => 'array',
        'total_tagihan'  => 'decimal:2',
        'total_terbayar' => 'decimal:2',
        'sisa_piutang'   => 'decimal:2',
    ];

    /**
     * Customer pemilik faktur.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * User yang menerbitkan faktur.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Seluruh penugasan inkaso yang pernah dibuat untuk faktur ini.
     */
    public function penugasanInkaso(): HasMany
    {
        return $this->hasMany(PenugasanInkaso::class, 'faktur_id');
    }

    /**
     * Penugasan inkaso yang saat ini aktif/berjalan.
     */
    public function penugasanAktif()
    {
        return $this->hasOne(PenugasanInkaso::class, 'faktur_id')
                    ->where('status_tugas', 'berjalan')
                    ->latestOfMany();
    }

    /**
     * Seluruh riwayat pembayaran faktur (DP, pelunasan langsung, maupun setoran inkaso).
     */
    public function pembayaran(): HasMany
    {
        return $this->hasMany(PembayaranFaktur::class, 'faktur_id');
    }

    /**
     * Sinkronisasi ulang status faktur berdasarkan saldo terbayar dan sisa piutang.
     */
    public function sinkronkanStatus(): void
    {
        $totalBayar = (float) $this->pembayaran()->sum('nominal_bayar');
        $this->total_terbayar = $totalBayar;
        $this->sisa_piutang = max(0, (float) $this->total_tagihan - $totalBayar);

        if ($this->sisa_piutang <= 0) {
            $this->status = 'lunas';
            $this->penugasanInkaso()->where('status_tugas', 'berjalan')->update(['status_tugas' => 'selesai']);
        } elseif ($totalBayar > 0) {
            $this->status = 'cicilan';
        } else {
            $this->status = 'belum_bayar';
        }

        $this->save();
    }

    /**
     * Payload JSON terstruktur untuk Kode QR Kasir.
     * Bagian Kasir dapat membaca seluruh rincian pemesanan langsung saat melakukan scan faktur.
     */
    public function getQrKasirPayloadAttribute(): string
    {
        $items = [];
        if (is_array($this->rincian_biaya)) {
            foreach ($this->rincian_biaya as $item) {
                $items[] = [
                    'item'     => $item['item'] ?? '-',
                    'qty'      => (int) ($item['qty'] ?? 1),
                    'harga'    => (float) ($item['harga'] ?? 0),
                    'subtotal' => (float) ($item['subtotal'] ?? 0),
                ];
            }
        }

        $payload = [
            'app'             => 'SI-KABARPRIANGAN',
            'tipe'            => 'FAKTUR_PEMESANAN',
            'sumber'          => 'keuangan_faktur',
            'no_faktur'       => $this->no_faktur,
            'faktur'          => $this->no_faktur,
            'kategori'        => $this->kategori,
            'status'          => $this->status,
            'customer'        => $this->customer->nama_customer ?? '-',
            'nama_agen'       => $this->customer->nama_customer ?? '-',
            'harga_bayar'     => (float) $this->total_tagihan,
            'alamat'          => $this->customer->alamat ?? '-',
            'telepon'         => $this->customer->no_telepon ?? '-',
            'tanggal_faktur'  => $this->tanggal_faktur ? $this->tanggal_faktur->format('Y-m-d') : null,
            'jatuh_tempo'     => $this->jatuh_tempo ? $this->jatuh_tempo->format('Y-m-d') : null,
            'total_tagihan'   => (float) $this->total_tagihan,
            'total_terbayar'  => (float) $this->total_terbayar,
            'sisa_piutang'    => (float) $this->sisa_piutang,
            'rincian_pesanan' => $items,
            'api_url'         => url('/api/faktur/' . rawurlencode($this->no_faktur)),
        ];

        return json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}

