<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiKoran extends Model
{
    use HasFactory;

    protected $table = 'transaksikoran';

    protected $fillable = [
        'nofakturkoran',
        'tanggal_transaksikoran',
        'nama_pemasangkoran',
        'nama_pic',
        'alamat_pemasangkoran',
        'pelanggan_id',
        'id_iklankoran',
        'halaman_iklan',
        'warna_iklan',
        'ukuran_iklan',
        'tanggal_muatkoran',
        'tanggal_muat_list',
        'sales_iklankoran',
        'input_oleh_user_id',
        'kategori_form',
        'naskah_iklan_baris',
        'jumlah_baris',
        'lebar_kolom',
        'tinggi_mm',
        'produk_jasa',
        'jenis_harga',
        'jenis_pembayaran',
        'jatuh_tempo_pembayaran',
        'keterangan_materi',
        'status_materi',
        'kop_iklan_kota',
        'sales_ae_id',
        'komisioner',
        'komisi_persen',
        'insentif_persen',
        'kop_iklan_id',
        'kop_kota_id',
        'file_materi',
        'total_muatkoran',
        'harga_transaksikoran',
        'diskon_transaksikoran',
        'diskon_agensi',
        'diskon_umum',
        'diskon_khusus',
        'diskon_persen',
        'materai',
        'ppn_persen',
        'insentif_transaksikoran',
        'komisi_transaksikoran',
        'ppn_transaksikoran',
        'totaltagihan_transaksikoran',
        'jumlahbayar_transaksikoran',
        'piutang_transaksikoran',
        'siap_cair_kh_insentif',
        'status_order',
        'status_sppi',
        'disetujui_kredit',
        'disetujui_kredit_oleh',
        'alasan_batal',
        'dibatalkan_at',
        'dibatalkan_oleh_user_id',
    ];

    protected $casts = [
        'tanggal_transaksikoran' => 'date',
        'tanggal_muatkoran'      => 'date',
        'jatuh_tempo_pembayaran' => 'date',
        'dibatalkan_at'          => 'datetime',
        'tanggal_muat_list'      => 'array',
        'total_muatkoran'        => 'integer',
        'jumlah_baris'           => 'integer',
        'lebar_kolom'            => 'integer',
        'tinggi_mm'              => 'integer',
        'harga_transaksikoran'   => 'integer',
        'diskon_transaksikoran'  => 'integer',
        'diskon_agensi'          => 'integer',
        'diskon_umum'            => 'integer',
        'diskon_khusus'          => 'integer',
        'materai'                => 'integer',
        'ppn_persen'             => 'float',
        'komisi_persen'          => 'float',
        'insentif_persen'        => 'float',
        'insentif_transaksikoran'=> 'integer',
        'komisi_transaksikoran'  => 'integer',
        'ppn_transaksikoran'     => 'integer',
        'totaltagihan_transaksikoran' => 'integer',
        'jumlahbayar_transaksikoran'  => 'integer',
        'piutang_transaksikoran'      => 'integer',
        'siap_cair_kh_insentif'  => 'boolean',
        'disetujui_kredit'       => 'boolean',
    ];

    public function kasMasuks()
    {
        return $this->hasMany(KasMasuk::class, 'ref_id')->where('tipe_transaksi', 'iklan');
    }

    public function iklankoran()
    {
        return $this->belongsTo(JenisIklan::class, 'id_iklankoran');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function inputOleh()
    {
        return $this->belongsTo(User::class, 'input_oleh_user_id');
    }

    public function salesAe()
    {
        return $this->belongsTo(SalesAe::class, 'sales_ae_id');
    }

    public function kopIklan()
    {
        return $this->belongsTo(KopIklan::class, 'kop_iklan_id');
    }

    public function kopKota()
    {
        return $this->belongsTo(KopKota::class, 'kop_kota_id');
    }

    public function jadwalMuat()
    {
        return $this->hasMany(OrderJadwalMuat::class, 'transaksi_id');
    }

    public function disetujuiKreditOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_kredit_oleh');
    }

    public function sppi()
    {
        return $this->hasOne(Sppi::class, 'transaksi_id')->where('media_type', 'koran');
    }

    public function pembayaranList()
    {
        return $this->hasMany(PembayaranIklan::class, 'transaksi_id')->where('media_type', 'koran');
    }

    public function dibatalkanOleh()
    {
        return $this->belongsTo(User::class, 'dibatalkan_oleh_user_id');
    }

    /**
     * Cek apakah order terkunci oleh aturan Cash Before Print (CBP).
     * Terkunci jika: Pembayaran Tunai & Masih Piutang (belum lunas).
     */
    public function isCbpLocked(): bool
    {
        if ($this->jenis_pembayaran === 'Tunai' && $this->piutang_transaksikoran > 0) {
            return true;
        }

        if ($this->jenis_pembayaran === 'Kredit' && !$this->disetujui_kredit) {
            return true;
        }

        return false;
    }

    /**
     * Cek apakah transaksi masih dapat diedit sebelum jam cut-off ditutup.
     */
    public function canEdit(): bool
    {
        // 1. Transaksi Batal atau Terbit tidak dapat diedit
        if (in_array($this->status_order, ['Batal', 'Terbit'])) {
            return false;
        }

        // 2. Ambil seluruh tanggal edisi muat
        $dates = [];
        if ($this->relationLoaded('jadwalMuat') && $this->jadwalMuat->isNotEmpty()) {
            $dates = $this->jadwalMuat->pluck('tanggal_muat')->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())->all();
        } elseif ($this->jadwalMuat()->exists()) {
            $dates = $this->jadwalMuat()->pluck('tanggal_muat')->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())->all();
        } elseif (!empty($this->tanggal_muat_list)) {
            $list = is_string($this->tanggal_muat_list) ? json_decode($this->tanggal_muat_list, true) : $this->tanggal_muat_list;
            if (is_array($list)) {
                $dates = array_map(fn($d) => \Carbon\Carbon::parse($d)->toDateString(), $list);
            }
        } elseif ($this->tanggal_muatkoran) {
            $dates = [\Carbon\Carbon::parse($this->tanggal_muatkoran)->toDateString()];
        }

        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();
        $isPastCutoff = SistemConfig::isPastCutoff();

        foreach ($dates as $tgl) {
            // Jika edisi tanggal ini sudah ditutup di DailyCutoff (dan tidak ada Memo Unlock aktif)
            if (DailyCutoff::isDateLocked($tgl)) {
                return false;
            }

            // Jika tanggal edisi adalah hari-hari lampau
            if ($tgl < $today) {
                return false;
            }

            // Jika tanggal edisi adalah hari ini (sudah dicetak semalam/subuh untuk distribusi pagi)
            if ($tgl === $today) {
                return false;
            }

            // Jika tanggal edisi adalah besok dan waktu saat ini sudah lewat jam cutoff harian
            if ($tgl === $tomorrow && $isPastCutoff) {
                return false;
            }
        }

        return true;
    }

    /**
     * Keterangan alasan mengapa transaksi terkunci cut-off.
     */
    public function getCutoffLockReason(): ?string
    {
        if ($this->status_order === 'Batal') {
            return 'Transaksi sudah dibatalkan.';
        }
        if ($this->status_order === 'Terbit') {
            return 'Transaksi sudah terbit di koran.';
        }

        $dates = [];
        if ($this->relationLoaded('jadwalMuat') && $this->jadwalMuat->isNotEmpty()) {
            $dates = $this->jadwalMuat->pluck('tanggal_muat')->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())->all();
        } elseif ($this->jadwalMuat()->exists()) {
            $dates = $this->jadwalMuat()->pluck('tanggal_muat')->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())->all();
        } elseif (!empty($this->tanggal_muat_list)) {
            $list = is_string($this->tanggal_muat_list) ? json_decode($this->tanggal_muat_list, true) : $this->tanggal_muat_list;
            if (is_array($list)) {
                $dates = array_map(fn($d) => \Carbon\Carbon::parse($d)->toDateString(), $list);
            }
        } elseif ($this->tanggal_muatkoran) {
            $dates = [\Carbon\Carbon::parse($this->tanggal_muatkoran)->toDateString()];
        }

        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();
        $jamCutoff = SistemConfig::getJamCutoff();
        $isPastCutoff = SistemConfig::isPastCutoff();

        foreach ($dates as $tgl) {
            if (DailyCutoff::isDateLocked($tgl)) {
                return "Edisi {$tgl} sudah ditutup (Daily Cut-off). Buka Memo Unlock Supervisor jika perlu revisi.";
            }
            if ($tgl <= $today) {
                return "Edisi {$tgl} sudah selesai dicetak / beredar.";
            }
            if ($tgl === $tomorrow && $isPastCutoff) {
                return "Sudah melewati batas jam cutoff harian ({$jamCutoff} WIB) untuk edisi terbit besok ({$tgl}).";
            }
        }

        return null;
    }

    /**
     * Helper status badge UI
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status_order) {
            'Draft' => [
                'bg' => 'bg-slate-50',
                'text' => 'text-slate-700',
                'border' => 'border-slate-200',
                'label' => 'Draft',
            ],
            'Menunggu_Bayar' => [
                'bg' => 'bg-amber-50',
                'text' => 'text-amber-800',
                'border' => 'border-amber-200',
                'label' => 'Menunggu Pembayaran',
            ],
            'Menunggu_SPPI' => [
                'bg' => 'bg-blue-50',
                'text' => 'text-blue-800',
                'border' => 'border-blue-200',
                'label' => 'Menunggu SPPI',
            ],
            'Antrean_Pracetak' => [
                'bg' => 'bg-indigo-50',
                'text' => 'text-indigo-800',
                'border' => 'border-indigo-200',
                'label' => 'Antrean Pracetak',
            ],
            'Menunggu_Proof' => [
                'bg' => 'bg-purple-50',
                'text' => 'text-purple-800',
                'border' => 'border-purple-200',
                'label' => 'Menunggu Proof',
            ],
            'Terbit' => [
                'bg' => 'bg-cyan-50',
                'text' => 'text-cyan-800',
                'border' => 'border-cyan-200',
                'label' => 'Sudah Terbit',
            ],
            'Lunas' => [
                'bg' => 'bg-emerald-50',
                'text' => 'text-emerald-800',
                'border' => 'border-emerald-200',
                'label' => 'Lunas',
            ],
            'Batal' => [
                'bg' => 'bg-red-50',
                'text' => 'text-red-800',
                'border' => 'border-red-200',
                'label' => 'Dibatalkan',
            ],
            default => [
                'bg' => 'bg-slate-50',
                'text' => 'text-slate-700',
                'border' => 'border-slate-200',
                'label' => $this->status_order ?? 'Diproses',
            ],
        };
    }

    /**
     * Financial OOP Accessors: Standar Akuntansi & Dokumen Order Fisik
     * Brutto -> Diskon -> Netto (DPP) -> PPN -> Materai -> Jumlah Yang Dibayar
     */
    public function getNilaiBruttoAttribute(): int
    {
        $qty = max(1, (int) $this->total_muatkoran);
        return (int) ($this->harga_transaksikoran * $qty);
    }

    public function getNilaiDiskonAttribute(): int
    {
        return (int) ($this->diskon_transaksikoran ?? 0);
    }

    public function getNilaiNettoAttribute(): int
    {
        return max(0, $this->nilai_brutto - $this->nilai_diskon);
    }

    public function getNilaiDppAttribute(): int
    {
        return $this->nilai_netto;
    }

    public function getNilaiPpnAttribute(): int
    {
        return (int) ($this->ppn_transaksikoran ?? 0);
    }

    public function getNilaiMateraiAttribute(): int
    {
        return (int) ($this->materai ?? 0);
    }

    public function getJumlahYangDibayarAttribute(): int
    {
        return (int) ($this->totaltagihan_transaksikoran ?? ($this->nilai_netto + $this->nilai_ppn + $this->nilai_materai));
    }

    public function getSisaPiutangAttribute(): int
    {
        return (int) ($this->piutang_transaksikoran ?? max(0, $this->jumlah_yang_dibayar - ($this->jumlahbayar_transaksikoran ?? 0)));
    }

    /**
     * Generate Nomor Faktur Iklan Standar Industri (FK/YYYY/MM/0001)
     * Terintegrasi dengan counter sekuensial atomik bebas race condition.
     */
    public static function createCode(): string
    {
        return \App\Services\NomorDokumenService::generateFaktur();
    }
}
