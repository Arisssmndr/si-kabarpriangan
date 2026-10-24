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
        if (Schema::hasTable('transaksipriangan')) {
            Schema::table('transaksipriangan', function (Blueprint $table) {
                if (!Schema::hasColumn('transaksipriangan', 'nama_pic')) {
                    $table->string('nama_pic')->nullable()->after('nama_pemasangpriangan');
                }
                if (!Schema::hasColumn('transaksipriangan', 'jabatan_pic')) {
                    $table->string('jabatan_pic')->nullable()->after('nama_pic');
                }
                if (!Schema::hasColumn('transaksipriangan', 'no_telp')) {
                    $table->string('no_telp', 50)->nullable()->after('jabatan_pic');
                }
                if (!Schema::hasColumn('transaksipriangan', 'produk_jasa')) {
                    $table->string('produk_jasa')->nullable()->after('nama_pemasangpriangan');
                }
                if (!Schema::hasColumn('transaksipriangan', 'no_spk')) {
                    $table->string('no_spk')->nullable()->after('produk_jasa');
                }
                if (!Schema::hasColumn('transaksipriangan', 'materi_iklan')) {
                    $table->string('materi_iklan')->nullable()->after('id_iklanpriangan');
                }
                if (!Schema::hasColumn('transaksipriangan', 'durasi_detik')) {
                    $table->integer('durasi_detik')->nullable()->default(30)->after('materi_iklan');
                }
                if (!Schema::hasColumn('transaksipriangan', 'jam_tayang')) {
                    $table->string('jam_tayang')->nullable()->after('tanggal_muatiklanpriangan');
                }
                if (!Schema::hasColumn('transaksipriangan', 'status_tayang')) {
                    $table->string('status_tayang')->nullable()->default('terjadwal')->after('jam_tayang');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transaksipriangan')) {
            Schema::table('transaksipriangan', function (Blueprint $table) {
                $cols = ['nama_pic', 'jabatan_pic', 'no_telp', 'produk_jasa', 'no_spk', 'materi_iklan', 'durasi_detik', 'jam_tayang', 'status_tayang'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('transaksipriangan', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
