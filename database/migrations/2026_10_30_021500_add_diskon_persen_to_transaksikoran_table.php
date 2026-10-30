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
        if (Schema::hasTable('transaksikoran') && !Schema::hasColumn('transaksikoran', 'diskon_persen')) {
            Schema::table('transaksikoran', function (Blueprint $table) {
                $table->decimal('diskon_persen', 5, 2)->default(0)->after('diskon_khusus');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transaksikoran') && Schema::hasColumn('transaksikoran', 'diskon_persen')) {
            Schema::table('transaksikoran', function (Blueprint $table) {
                $table->dropColumn('diskon_persen');
            });
        }
    }
};
