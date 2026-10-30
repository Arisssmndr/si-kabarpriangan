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
        if (Schema::hasTable('transaksionline')) {
            Schema::table('transaksionline', function (Blueprint $table) {
                if (!Schema::hasColumn('transaksionline', 'tanggal_muat_list')) {
                    $table->text('tanggal_muat_list')->nullable()->after('tanggal_muatiklanonline');
                }
                if (!Schema::hasColumn('transaksionline', 'file_materi')) {
                    $table->string('file_materi')->nullable()->after('materi_iklan');
                }
            });
        }

        if (Schema::hasTable('transaksipriangan')) {
            Schema::table('transaksipriangan', function (Blueprint $table) {
                if (!Schema::hasColumn('transaksipriangan', 'tanggal_muat_list')) {
                    $table->text('tanggal_muat_list')->nullable()->after('tanggal_muatiklanpriangan');
                }
                if (!Schema::hasColumn('transaksipriangan', 'platforms_publikasi')) {
                    $table->text('platforms_publikasi')->nullable()->after('id_iklanpriangan');
                }
                if (!Schema::hasColumn('transaksipriangan', 'file_materi')) {
                    $table->string('file_materi')->nullable()->after('materi_iklan');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transaksionline')) {
            Schema::table('transaksionline', function (Blueprint $table) {
                if (Schema::hasColumn('transaksionline', 'tanggal_muat_list')) {
                    $table->dropColumn('tanggal_muat_list');
                }
                if (Schema::hasColumn('transaksionline', 'file_materi')) {
                    $table->dropColumn('file_materi');
                }
            });
        }

        if (Schema::hasTable('transaksipriangan')) {
            Schema::table('transaksipriangan', function (Blueprint $table) {
                if (Schema::hasColumn('transaksipriangan', 'tanggal_muat_list')) {
                    $table->dropColumn('tanggal_muat_list');
                }
                if (Schema::hasColumn('transaksipriangan', 'platforms_publikasi')) {
                    $table->dropColumn('platforms_publikasi');
                }
                if (Schema::hasColumn('transaksipriangan', 'file_materi')) {
                    $table->dropColumn('file_materi');
                }
            });
        }
    }
};
