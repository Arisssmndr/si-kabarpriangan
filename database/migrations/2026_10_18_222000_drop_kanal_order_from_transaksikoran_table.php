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
        if (Schema::hasTable('transaksikoran') && Schema::hasColumn('transaksikoran', 'kanal_order')) {
            Schema::table('transaksikoran', function (Blueprint $table) {
                $table->dropColumn('kanal_order');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transaksikoran') && !Schema::hasColumn('transaksikoran', 'kanal_order')) {
            Schema::table('transaksikoran', function (Blueprint $table) {
                $table->enum('kanal_order', ['FO', 'AE', 'KORP', 'BIRO_IKLAN'])->default('FO')->after('input_oleh_user_id');
            });
        }
    }
};
