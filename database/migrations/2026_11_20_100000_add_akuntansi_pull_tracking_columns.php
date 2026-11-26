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
        $tables = ['transaksikoran', 'transaksionline', 'transaksipriangan', 'paket_iklan', 'faktur'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    if (!Schema::hasColumn($table, 'is_pulled_akuntansi')) {
                        $t->boolean('is_pulled_akuntansi')->default(false)->index();
                    }
                    if (!Schema::hasColumn($table, 'pulled_at')) {
                        $t->timestamp('pulled_at')->nullable();
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['transaksikoran', 'transaksionline', 'transaksipriangan', 'paket_iklan', 'faktur'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    if (Schema::hasColumn($table, 'is_pulled_akuntansi')) {
                        $t->dropColumn('is_pulled_akuntansi');
                    }
                    if (Schema::hasColumn($table, 'pulled_at')) {
                        $t->dropColumn('pulled_at');
                    }
                });
            }
        }
    }
};
