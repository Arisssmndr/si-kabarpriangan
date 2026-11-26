<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('paket_iklan')) {
            Schema::create('paket_iklan', function (Blueprint $table) {
                $table->id();
                $table->string('nofaktur_paket', 40)->unique();
                $table->date('tanggal_transaksi');
                $table->string('kode_template', 40)->default('CUSTOM');
                $table->string('nama_paket', 120)->nullable();
                $table->unsignedBigInteger('pelanggan_id')->nullable()->index();
                $table->string('nama_pemasang', 150);
                $table->text('alamat_pemasang')->nullable();
                $table->string('telepon_pemasang', 40)->nullable();
                $table->string('nama_pic', 100)->nullable();
                $table->unsignedBigInteger('sales_ae_id')->nullable()->index();
                $table->string('sales_nama', 100)->nullable();
                $table->string('komisioner', 100)->nullable();
                $table->decimal('komisi_persen', 5, 2)->default(0);
                $table->bigInteger('komisi_nominal')->default(0);
                $table->decimal('insentif_persen', 5, 2)->default(0);
                $table->bigInteger('insentif_nominal')->default(0);
                $table->json('media_terpilih')->nullable();
                $table->bigInteger('nilai_brutto')->default(0);
                $table->decimal('diskon_persen', 5, 2)->default(0);
                $table->bigInteger('nilai_diskon')->default(0);
                $table->bigInteger('nilai_netto')->default(0);
                $table->decimal('ppn_persen', 5, 2)->default(11);
                $table->bigInteger('nilai_ppn')->default(0);
                $table->bigInteger('total_tagihan')->default(0);
                $table->bigInteger('jumlah_bayar')->default(0);
                $table->bigInteger('piutang')->default(0);
                $table->string('jenis_pembayaran', 20)->default('Tunai');
                $table->date('jatuh_tempo')->nullable();
                $table->string('status_order', 30)->default('Aktif');
                $table->text('alasan_batal')->nullable();
                $table->unsignedBigInteger('dibatalkan_oleh')->nullable();
                $table->timestamp('dibatalkan_pada')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('transaksikoran') && !Schema::hasColumn('transaksikoran', 'paket_id')) {
            Schema::table('transaksikoran', function (Blueprint $table) {
                $table->unsignedBigInteger('paket_id')->nullable()->after('id')->index();
            });
        }

        if (Schema::hasTable('transaksionline') && !Schema::hasColumn('transaksionline', 'paket_id')) {
            Schema::table('transaksionline', function (Blueprint $table) {
                $table->unsignedBigInteger('paket_id')->nullable()->after('id')->index();
            });
        }

        if (Schema::hasTable('transaksipriangan') && !Schema::hasColumn('transaksipriangan', 'paket_id')) {
            Schema::table('transaksipriangan', function (Blueprint $table) {
                $table->unsignedBigInteger('paket_id')->nullable()->after('id')->index();
            });
        }

        if (Schema::hasTable('faktur_qr_pembayaran')) {
            Schema::table('faktur_qr_pembayaran', function (Blueprint $table) {
                $table->string('kategori', 20)->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transaksikoran') && Schema::hasColumn('transaksikoran', 'paket_id')) {
            Schema::table('transaksikoran', function (Blueprint $table) {
                $table->dropColumn('paket_id');
            });
        }
        if (Schema::hasTable('transaksionline') && Schema::hasColumn('transaksionline', 'paket_id')) {
            Schema::table('transaksionline', function (Blueprint $table) {
                $table->dropColumn('paket_id');
            });
        }
        if (Schema::hasTable('transaksipriangan') && Schema::hasColumn('transaksipriangan', 'paket_id')) {
            Schema::table('transaksipriangan', function (Blueprint $table) {
                $table->dropColumn('paket_id');
            });
        }
        Schema::dropIfExists('paket_iklan');
    }
};
