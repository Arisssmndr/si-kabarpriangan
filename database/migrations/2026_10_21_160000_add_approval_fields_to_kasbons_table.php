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
        if (Schema::hasTable('kasbons')) {
            Schema::table('kasbons', function (Blueprint $table) {
                if (!Schema::hasColumn('kasbons', 'approved_at')) {
                    $table->timestamp('approved_at')->nullable()->after('approved_by');
                }
                if (!Schema::hasColumn('kasbons', 'alasan_tolak')) {
                    $table->text('alasan_tolak')->nullable()->after('selesai_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('kasbons')) {
            Schema::table('kasbons', function (Blueprint $table) {
                if (Schema::hasColumn('kasbons', 'alasan_tolak')) {
                    $table->dropColumn('alasan_tolak');
                }
                if (Schema::hasColumn('kasbons', 'approved_at')) {
                    $table->dropColumn('approved_at');
                }
            });
        }
    }
};
