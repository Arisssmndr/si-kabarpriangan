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
        Schema::create('pembayaran_iklan', function (Blueprint $table) {
            $table->id();
            $table->string('nokwitansi')->unique();
            $table->string('media_type')->default('koran'); // koran, online, priangan
            $table->unsignedBigInteger('transaksi_id');
            $table->string('nofaktur');
            $table->date('tanggal_bayar');
            $table->bigInteger('nominal_bayar')->default(0);
            $table->bigInteger('potongan_pph23')->default(0)->comment('Potongan PPh 23 (2%) jika pemda/BUMN/PT');
            $table->enum('metode_bayar', ['Tunai_Kasir', 'Transfer_Bank', 'Giro', 'Cek'])->default('Tunai_Kasir');
            $table->string('nama_penyetor')->nullable();
            $table->string('bukti_transfer_path')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('kasir_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['media_type', 'transaksi_id']);
            $table->index('nofaktur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_iklan');
    }
};
