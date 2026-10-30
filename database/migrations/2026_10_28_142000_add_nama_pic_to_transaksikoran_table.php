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
        if (Schema::hasTable('transaksikoran') && !Schema::hasColumn('transaksikoran', 'nama_pic')) {
            Schema::table('transaksikoran', function (Blueprint $table) {
                $table->string('nama_pic')->nullable()->after('nama_pemasangkoran');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transaksikoran') && Schema::hasColumn('transaksikoran', 'nama_pic')) {
            Schema::table('transaksikoran', function (Blueprint $table) {
                $table->dropColumn('nama_pic');
            });
        }
    }
};
