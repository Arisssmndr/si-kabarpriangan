<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('memo_unlocks')) {
            Schema::table('memo_unlocks', function (Blueprint $table) {
                if (!Schema::hasColumn('memo_unlocks', 'catatan_akuntansi')) {
                    $table->text('catatan_akuntansi')->nullable()->after('alasan');
                }
                if (!Schema::hasColumn('memo_unlocks', 'ditolak_pada')) {
                    $table->timestamp('ditolak_pada')->nullable()->after('diterima_akuntansi_pada');
                }
                if (!Schema::hasColumn('memo_unlocks', 'direspon_oleh_user_id')) {
                    $table->unsignedBigInteger('direspon_oleh_user_id')->nullable()->after('dibuka_oleh_user_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('memo_unlocks')) {
            Schema::table('memo_unlocks', function (Blueprint $table) {
                $cols = ['catatan_akuntansi', 'ditolak_pada', 'direspon_oleh_user_id'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('memo_unlocks', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
