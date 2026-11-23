<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('shift_kasirs') && !Schema::hasColumn('shift_kasirs', 'rincian_pecahan')) {
            Schema::table('shift_kasirs', function (Blueprint $table) {
                $table->json('rincian_pecahan')->nullable()->after('saldo_akhir_fisik');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('shift_kasirs') && Schema::hasColumn('shift_kasirs', 'rincian_pecahan')) {
            Schema::table('shift_kasirs', function (Blueprint $table) {
                $table->dropColumn('rincian_pecahan');
            });
        }
    }
};

