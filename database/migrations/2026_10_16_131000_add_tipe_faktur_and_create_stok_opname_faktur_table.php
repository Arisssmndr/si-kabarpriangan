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
        if (Schema::hasTable('faktur')) {
            Schema::table('faktur', function (Blueprint $table) {
                if (!Schema::hasColumn('faktur', 'tipe_faktur')) {
                    $table->string('tipe_faktur')->default('reguler')->after('kategori');
                    $table->index(['kategori', 'tipe_faktur']);
                }
            });
        }

        if (!Schema::hasTable('stok_opname_faktur')) {
            Schema::create('stok_opname_faktur', function (Blueprint $table) {
                $table->id();
                $table->string('no_dokumen')->unique();
                $table->string('kategori')->default('iklan');
                $table->date('tanggal_opname');
                $table->string('periode_bulan', 7);
                $table->string('nomor_seri_awal');
                $table->string('nomor_seri_akhir');
                $table->integer('stok_awal')->default(0);
                $table->integer('jumlah_terpakai')->default(0);
                $table->integer('jumlah_rusak_batal')->default(0);
                $table->integer('sisa_blangko_fisik')->default(0);
                $table->integer('selisih')->default(0);
                $table->enum('status_opname', ['sesuai', 'selisih'])->default('sesuai');
                $table->text('keterangan')->nullable();
                $table->foreignId('petugas_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->index(['kategori', 'periode_bulan']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_opname_faktur');

        if (Schema::hasTable('faktur') && Schema::hasColumn('faktur', 'tipe_faktur')) {
            Schema::table('faktur', function (Blueprint $table) {
                $table->dropColumn('tipe_faktur');
            });
        }
    }
};
