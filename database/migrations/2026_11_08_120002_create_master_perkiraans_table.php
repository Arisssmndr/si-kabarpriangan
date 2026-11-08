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
        Schema::create('master_perkiraans', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 60)->unique();
            $table->string('nama');
            $table->string('kode_bagian', 10)->nullable()->index();
            $table->string('bidang', 100)->nullable();
            $table->string('kategori', 100)->nullable();
            $table->enum('tipe', ['DEBIT', 'KREDIT'])->default('DEBIT');
            $table->text('keterangan')->nullable();
            $table->decimal('default_alokasi', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_perkiraans');
    }
};
