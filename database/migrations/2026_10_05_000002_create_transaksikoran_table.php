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
        Schema::create('transaksikoran', function (Blueprint $table) {
            $table->id();
            $table->string('nofakturkoran')->unique();
            $table->date('tanggal_transaksikoran');
            $table->string('nama_pemasangkoran');
            $table->string('alamat_pemasangkoran');
            $table->unsignedBigInteger('id_iklankoran');
            $table->string('halaman_iklan')->default('Halaman Dalam'); // Hal 1, Hal Dalam, Hal Belakang
            $table->string('warna_iklan')->default('Hitam Putih (BW)'); // BW / FC
            $table->string('ukuran_iklan')->nullable(); // e.g. 2 x 100 mmk, 4 Baris
            $table->string('sales_iklankoran');
            $table->integer('total_muatkoran')->default(1); // Berapa edisi terbit
            $table->date('tanggal_muatkoran');

            // Bagian Keuangan
            $table->bigInteger('harga_transaksikoran')->default(0);
            $table->bigInteger('diskon_transaksikoran')->default(0);
            $table->bigInteger('insentif_transaksikoran')->default(0);
            $table->bigInteger('komisi_transaksikoran')->default(0);
            $table->bigInteger('ppn_transaksikoran')->default(0);
            $table->bigInteger('totaltagihan_transaksikoran')->default(0);
            $table->bigInteger('jumlahbayar_transaksikoran')->default(0);
            $table->bigInteger('piutang_transaksikoran')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksikoran');
    }
};
