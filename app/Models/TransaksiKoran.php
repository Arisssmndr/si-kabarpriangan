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
        'kanal_order',
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
        'total_muatkoran',
        'harga_transaksikoran',
        'diskon_transaksikoran',
        'diskon_agensi',
        'diskon_umum',
        'diskon_khusus',
        'materai',
        'ppn_persen',
        'insentif_transaksikoran',
        'komisi_transaksikoran',
        'ppn_transaksikoran',
        'totaltagihan_transaksikoran',
        'jumlahbayar_transaksikoran',
        'piutang_transaksikoran',
        'status_order',
        'status_sppi',
        'disetujui_kredit',
        'disetujui_kredit_oleh',
    ];

    protected $casts = [
        'tanggal_transaksikoran' => 'date',
        'tanggal_muatkoran'      => 'date',
        'jatuh_tempo_pembayaran' => 'date',
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
        'insentif_transaksikoran'=> 'integer',
        'komisi_transaksikoran'  => 'integer',
        'ppn_transaksikoran'     => 'integer',
        'totaltagihan_transaksikoran' => 'integer',
        'jumlahbayar_transaksikoran'  => 'integer',
        'piutang_transaksikoran'      => 'integer',
        'disetujui_kredit'       => 'boolean',
    ];

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
     * Helper status badge UI
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status_order) {
            'Draft' => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'label' => 'Draft'],
            'Menunggu_Bayar' => ['bg' => 'bg-amber-100 text-amber-800 border-amber-200', 'label' => 'Menunggu Pembayaran (CBP)'],
            'Menunggu_SPPI' => ['bg' => 'bg-blue-100 text-blue-800 border-blue-200', 'label' => 'Menunggu TTD SPPI'],
            'Antrean_Pracetak' => ['bg' => 'bg-indigo-100 text-indigo-800 border-indigo-200', 'label' => 'Antrean Pracetak'],
            'Menunggu_Proof' => ['bg' => 'bg-purple-100 text-purple-800 border-purple-200', 'label' => 'Menunggu Proof'],
            'Terbit' => ['bg' => 'bg-cyan-100 text-cyan-800 border-cyan-200', 'label' => 'Sudah Terbit'],
            'Lunas' => ['bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'label' => 'Lunas'],
            'Batal' => ['bg' => 'bg-red-100 text-red-800 border-red-200', 'label' => 'Batal'],
            default => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'label' => $this->status_order ?? 'Diproses'],
        };
    }

    public static function createCode()
    {
        $latestCode = self::orderBy('nofakturkoran', 'desc')->value('nofakturkoran');
        $latestCodeNumber = $latestCode ? intval(substr($latestCode, 5)) : 0;
        $nextCodeNumber = $latestCodeNumber + 1;
        $formattedCodeNumber = sprintf("%03d", $nextCodeNumber);
        return 'FKKRN' . $formattedCodeNumber;
    }
}
