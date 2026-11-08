<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SistemConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SistemConfigAdminController extends Controller
{
    /**
     * Konfigurasi default standar industri pers jika belum tersimpan di DB
     */
    protected array $defaults = [
        // Identitas & Legalitas Perusahaan Pers
        'nama_perusahaan'       => 'PT Berkah Pikiran Rakyat',
        'nama_media'            => 'Harian Umum Kabar Priangan',
        'slogan_media'          => 'Menebar Informasi, Merekat Priangan',
        'alamat_perusahaan'     => 'Jl. Dr. Sukardjo No. 47, Tawangsari, Kec. Tawang, Kota Tasikmalaya, Jawa Barat 46112',
        'kota_terbit'           => 'Tasikmalaya',
        'telepon_perusahaan'    => '(0265) 331000 / 331111',
        'email_perusahaan'      => 'iklan@kabarpriangan.co.id',
        'website_url'           => 'https://kabarpriangan.pikiran-rakyat.com',
        'npwp_perusahaan'       => '01.234.567.8-425.000',

        // Rekening Resmi Perusahaan (Digunakan di Faktur, Invoice & Kwitansi)
        'bank_nama'             => 'Bank BJB (Bank Pembangunan Daerah Jawa Barat)',
        'bank_cabang'           => 'Kantor Cabang Tasikmalaya',
        'bank_rekening'         => '0010010203040',
        'bank_atas_nama'        => 'PT BERKAH PIKIRAN RAKYAT',
        'bank_secondary_nama'   => 'Bank Mandiri',
        'bank_secondary_rekening' => '131-00-1122334-5',
        'bank_secondary_atas_nama' => 'PT BERKAH PIKIRAN RAKYAT',

        // Kebijakan Operasional Iklan & Pajak
        'ppn_persen'            => '12',
        'jam_cutoff_iklan'      => '14:00',
        'materai_threshold'     => '5000000',
        'materai_nominal'       => '10000',
        'credit_limit_default'  => '25000000',
        'diskon_agensi_default' => '15',

        // Format & Prefix Penomoran Dokumen
        'prefix_kwitansi'       => 'KWT',
        'prefix_faktur'         => 'FKT',
        'prefix_sppi'           => 'SPPI',
        'format_nomor_dokumen'  => '{PREFIX}/{MEDIA}/{TAHUN}/{BULAN}/{NO_URUT}',

        // Feature Toggle / Modul Aktif
        'modul_iklan_koran'     => '1',
        'modul_iklan_online'    => '1',
        'modul_iklan_tv'        => '1',
        'modul_sirkulasi'       => '1',
        'modul_keuangan'        => '1',
    ];

    /**
     * Tampilkan panel konfigurasi sistem terpusat untuk Administrator
     */
    public function index()
    {
        $stored = SistemConfig::all()->keyBy('key');

        $configs = [];
        foreach ($this->defaults as $key => $defaultVal) {
            $configs[$key] = isset($stored[$key]) ? $stored[$key]->value : $defaultVal;
        }

        // Ambil juga config yang sudah ada di DB di luar defaults
        foreach ($stored as $key => $model) {
            if (!isset($configs[$key])) {
                $configs[$key] = $model->value;
            }
        }

        return view('pages.admin.pengaturan.index', compact('configs'));
    }

    /**
     * Simpan pembaruan konfigurasi sistem terpusat
     */
    public function update(Request $request)
    {
        $request->validate([
            'nama_perusahaan'       => 'required|string|max:255',
            'nama_media'            => 'required|string|max:255',
            'alamat_perusahaan'     => 'required|string|max:500',
            'kota_terbit'           => 'required|string|max:100',
            'telepon_perusahaan'    => 'nullable|string|max:100',
            'email_perusahaan'      => 'nullable|email|max:150',
            'npwp_perusahaan'       => 'nullable|string|max:50',
            'bank_nama'             => 'required|string|max:100',
            'bank_rekening'         => 'required|string|max:50',
            'bank_atas_nama'        => 'required|string|max:150',
            'ppn_persen'            => 'required|numeric|min:0|max:100',
            'jam_cutoff_iklan'      => 'required|string|max:10',
            'materai_threshold'     => 'required|string',
            'materai_nominal'       => 'required|string',
            'credit_limit_default'  => 'required|string',
            'logo_perusahaan'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except(['_token', '_method', 'logo_perusahaan']);

        // Format angka ribuan
        $moneyFields = ['materai_threshold', 'materai_nominal', 'credit_limit_default'];
        foreach ($moneyFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = preg_replace('/[^0-9]/', '', (string) $data[$field]);
            }
        }

        // Toggle checkbox handling (jika tidak dicentang maka 0)
        $toggles = ['modul_iklan_koran', 'modul_iklan_online', 'modul_iklan_tv', 'modul_sirkulasi', 'modul_keuangan'];
        foreach ($toggles as $toggle) {
            $data[$toggle] = $request->has($toggle) ? '1' : '0';
        }

        // Handle upload logo perusahaan
        if ($request->hasFile('logo_perusahaan')) {
            $file = $request->file('logo_perusahaan');
            $filename = 'logo_perusahaan_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('company', $filename, 'public');
            $data['logo_perusahaan'] = '/storage/' . $path;
        }

        // Simpan ke database melalui model SistemConfig
        foreach ($data as $key => $val) {
            if ($val !== null) {
                SistemConfig::set($key, (string) $val);
            }
        }

        // Catat jejak audit trail
        ActivityLog::record(
            'UPDATE_SYSTEM_CONFIG',
            'Administrator memperbarui konfigurasi sistem terpusat (Identitas, Rekening Bank, Kebijakan Operasional)',
            [
                'updated_keys' => array_keys($data),
                'perusahaan'   => $data['nama_perusahaan'] ?? null,
                'media'        => $data['nama_media'] ?? null,
            ]
        );

        return redirect()->route('admin.pengaturan.index')
            ->with('success', 'Konfigurasi sistem terpusat berhasil diperbarui dan diterapkan ke seluruh modul!');
    }
}
