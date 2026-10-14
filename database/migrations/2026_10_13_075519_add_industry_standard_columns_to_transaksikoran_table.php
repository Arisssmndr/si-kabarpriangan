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
        Schema::table('transaksikoran', function (Blueprint $table) {
            $table->foreignId('pelanggan_id')->nullable()->after('alamat_pemasangkoran')->constrained('pelanggan')->nullOnDelete();
            $table->foreignId('input_oleh_user_id')->nullable()->after('sales_iklankoran')->constrained('users')->nullOnDelete();
            $table->enum('kanal_order', ['FO', 'AE', 'KORP', 'BIRO_IKLAN'])->default('FO')->after('input_oleh_user_id');
            $table->enum('kategori_form', ['BARIS', 'KOLOM', 'DISPLAY', 'ADVERTORIAL'])->default('BARIS')->after('kanal_order');
            $table->text('naskah_iklan_baris')->nullable()->after('kategori_form');
            $table->integer('jumlah_baris')->default(0)->after('naskah_iklan_baris');
            $table->integer('lebar_kolom')->default(0)->after('jumlah_baris');
            $table->integer('tinggi_mm')->default(0)->after('lebar_kolom');
            $table->string('produk_jasa')->nullable()->after('tinggi_mm');
            $table->enum('jenis_harga', ['Normal', 'Paket'])->default('Normal')->after('produk_jasa');
            $table->enum('jenis_pembayaran', ['Tunai', 'Kredit'])->default('Tunai')->after('jenis_harga');
            $table->date('jatuh_tempo_pembayaran')->nullable()->after('jenis_pembayaran');
            $table->text('keterangan_materi')->nullable()->after('jatuh_tempo_pembayaran');
            $table->enum('status_materi', ['Siap_Cetak', 'Butuh_Setting'])->default('Siap_Cetak')->after('keterangan_materi');
            $table->string('kop_iklan_kota')->nullable()->after('status_materi');
            $table->json('tanggal_muat_list')->nullable()->after('tanggal_muatkoran');
            $table->bigInteger('diskon_agensi')->default(0)->after('diskon_transaksikoran');
            $table->bigInteger('diskon_umum')->default(0)->after('diskon_agensi');
            $table->bigInteger('diskon_khusus')->default(0)->after('diskon_umum');
            $table->bigInteger('materai')->default(0)->after('ppn_transaksikoran');
            $table->decimal('ppn_persen', 5, 2)->default(12.00)->after('materai');
            $table->enum('status_order', [
                'Draft',
                'Menunggu_Bayar',
                'Menunggu_SPPI',
                'Antrean_Pracetak',
                'Menunggu_Proof',
                'Terbit',
                'Lunas',
                'Komplain',
                'Batal'
            ])->default('Menunggu_Bayar')->after('piutang_transaksikoran');
            $table->enum('status_sppi', ['Belum_Ditandatangani', 'Sudah_Ditandatangani'])->default('Belum_Ditandatangani')->after('status_order');
            $table->boolean('disetujui_kredit')->default(false)->after('status_sppi');
            $table->foreignId('disetujui_kredit_oleh')->nullable()->after('disetujui_kredit')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksikoran', function (Blueprint $table) {
            $table->dropForeign(['pelanggan_id']);
            $table->dropForeign(['input_oleh_user_id']);
            $table->dropForeign(['disetujui_kredit_oleh']);
            $table->dropColumn([
                'pelanggan_id',
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
                'tanggal_muat_list',
                'diskon_agensi',
                'diskon_umum',
                'diskon_khusus',
                'materai',
                'ppn_persen',
                'status_order',
                'status_sppi',
                'disetujui_kredit',
                'disetujui_kredit_oleh',
            ]);
        });
    }
};
