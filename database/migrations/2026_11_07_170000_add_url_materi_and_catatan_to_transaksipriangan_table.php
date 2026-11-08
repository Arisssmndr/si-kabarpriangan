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
                if (!Schema::hasColumn('transaksipriangan', 'url_materi_iklan')) {
                    $table->text('url_materi_iklan')->nullable()->after('file_materi');
                }
                if (!Schema::hasColumn('transaksipriangan', 'catatan_redaksi')) {
                    $table->text('catatan_redaksi')->nullable()->after('url_materi_iklan');
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
                if (Schema::hasColumn('transaksipriangan', 'catatan_redaksi')) {
                    $table->dropColumn('catatan_redaksi');
                }
                if (Schema::hasColumn('transaksipriangan', 'url_materi_iklan')) {
                    $table->dropColumn('url_materi_iklan');
                }
            });
        }
    }
};

