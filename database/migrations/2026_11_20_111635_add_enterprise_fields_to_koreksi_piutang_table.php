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
        Schema::table('koreksi_piutang', function (Blueprint $table) {
            if (!Schema::hasColumn('koreksi_piutang', 'media_type')) {
                $table->string('media_type', 30)->nullable()->after('jenis')->index();
            }
            if (!Schema::hasColumn('koreksi_piutang', 'kategori_alasan')) {
                $table->string('kategori_alasan', 50)->nullable()->after('keterangan')->index();
            }
            if (!Schema::hasColumn('koreksi_piutang', 'dpp_koreksi')) {
                $table->decimal('dpp_koreksi', 15, 2)->default(0)->after('jumlah_koreksi');
            }
            if (!Schema::hasColumn('koreksi_piutang', 'ppn_koreksi')) {
                $table->decimal('ppn_koreksi', 15, 2)->default(0)->after('dpp_koreksi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('koreksi_piutang', function (Blueprint $table) {
            $dropCols = [];
            foreach (['media_type', 'kategori_alasan', 'dpp_koreksi', 'ppn_koreksi'] as $col) {
                if (Schema::hasColumn('koreksi_piutang', $col)) {
                    $dropCols[] = $col;
                }
            }
            if (!empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });
    }
};
