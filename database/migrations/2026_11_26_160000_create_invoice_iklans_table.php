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
        Schema::create('invoice_iklans', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice')->unique();
            $table->unsignedBigInteger('transaksi_koran_id')->nullable();
            $table->unsignedBigInteger('transaksi_online_id')->nullable();
            $table->string('kepada');
            $table->text('alamat')->nullable();
            $table->string('attn')->nullable()->default('Bagian Keuangan');
            $table->date('tanggal_invoice');
            $table->text('deskripsi');
            $table->integer('qty')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('dpp', 15, 2)->default(0);
            $table->decimal('ppn_persen', 5, 2)->default(11.00);
            $table->decimal('ppn_nominal', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('terbilang')->nullable();
            $table->text('catatan_transfer')->nullable();
            $table->string('kota_terbit')->default('Tasikmalaya');
            $table->string('nama_penandatangan')->default('Eli Yulianti');
            $table->string('jabatan_penandatangan')->default('Manager Keuangan');
            $table->string('status_pembayaran')->default('belum_lunas');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('transaksi_koran_id')->references('id')->on('transaksikoran')->onDelete('set null');
            $table->foreign('transaksi_online_id')->references('id')->on('transaksionline')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_iklans');
    }
};
