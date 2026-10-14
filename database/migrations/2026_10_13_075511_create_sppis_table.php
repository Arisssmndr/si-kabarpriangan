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
        Schema::create('sppi', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_sppi')->unique();
            $table->string('media_type')->default('koran');
            $table->unsignedBigInteger('transaksi_id');
            $table->string('nofaktur');
            $table->date('tanggal_sppi');
            $table->enum('status_sppi', ['Belum_Ditandatangani', 'Sudah_Ditandatangani'])->default('Belum_Ditandatangani');
            $table->date('tanggal_ditandatangani')->nullable();
            $table->string('ditandatangani_oleh_nama')->nullable();
            $table->string('file_pdf_path')->nullable();
            $table->text('catatan')->nullable();
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
        Schema::dropIfExists('sppi');
    }
};
