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
        Schema::table('transaksipriangan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_iklanpriangan')->change();
            $table->integer('total_muatiklanpriangan')->default(1)->after('tanggal_muatiklanpriangan');
            $table->bigInteger('harga_transaksipriangan')->change();
            $table->bigInteger('diskon_transaksipriangan')->default(0)->after('harga_transaksipriangan');
            $table->bigInteger('insentif_transaksipriangan')->default(0)->after('diskon_transaksipriangan');
            $table->bigInteger('komisi_transaksipriangan')->default(0)->after('insentif_transaksipriangan');
            $table->bigInteger('ppn_transaksipriangan')->default(0)->after('komisi_transaksipriangan');
            $table->bigInteger('totaltagihan_transaksipriangan')->default(0)->after('ppn_transaksipriangan');
            $table->bigInteger('jumlahbayar_transaksipriangan')->change();
            $table->bigInteger('piutang_transaksipriangan')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksipriangan', function (Blueprint $table) {
            $table->dropColumn([
                'total_muatiklanpriangan',
                'diskon_transaksipriangan',
                'insentif_transaksipriangan',
                'komisi_transaksipriangan',
                'ppn_transaksipriangan',
                'totaltagihan_transaksipriangan',
            ]);
            $table->string('id_iklanpriangan')->change();
        });
    }
};
