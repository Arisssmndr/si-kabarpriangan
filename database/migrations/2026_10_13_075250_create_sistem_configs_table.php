<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sistem_configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        // Seed nilai default sistem periklanan standar industri Kabar Priangan
        $defaults = [
            ['key' => 'ppn_persen', 'value' => '12', 'keterangan' => 'Tarif PPN Resmi (berlaku 12% sesuai UU HPP)'],
            ['key' => 'jam_cutoff_iklan', 'value' => '14:00', 'keterangan' => 'Batas Waktu Masuk Materi Iklan Koran untuk Terbit Esok Hari (WIB)'],
            ['key' => 'tarif_per_baris', 'value' => '15000', 'keterangan' => 'Tarif Dasar Iklan Baris Koran per Baris (Rp)'],
            ['key' => 'tarif_mmk_bw', 'value' => '25000', 'keterangan' => 'Tarif Iklan Kolom / Display Hitam Putih (BW) per mmk (Rp)'],
            ['key' => 'tarif_mmk_fc', 'value' => '45000', 'keterangan' => 'Tarif Iklan Kolom / Display Berwarna (FC) per mmk (Rp)'],
            ['key' => 'minimal_baris', 'value' => '3', 'keterangan' => 'Ketentuan Jumlah Baris Minimal Iklan Baris'],
            ['key' => 'maksimal_karakter_per_baris', 'value' => '32', 'keterangan' => 'Kapasitas Karakter Huruf per Baris Cetak'],
            ['key' => 'diskon_agensi_default', 'value' => '15', 'keterangan' => 'Standar Diskon Komisi Biro Iklan Terakreditasi (%)'],
            ['key' => 'materai_nominal', 'value' => '10000', 'keterangan' => 'Nilai Bea Meterai untuk Transaksi >= Rp 5.000.000 (UU Bea Meterai)'],
            ['key' => 'materai_threshold', 'value' => '5000000', 'keterangan' => 'Ambang Batas Nilai Transaksi Wajib Bea Meterai (Rp)'],
        ];

        foreach ($defaults as $item) {
            \Illuminate\Support\Facades\DB::table('sistem_configs')->insert(array_merge($item, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sistem_configs');
    }
};
