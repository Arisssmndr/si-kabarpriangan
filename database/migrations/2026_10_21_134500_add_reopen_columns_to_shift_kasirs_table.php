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
        Schema::table('shift_kasirs', function (Blueprint $table) {
            if (!Schema::hasColumn('shift_kasirs', 'reopen_status')) {
                $table->string('reopen_status', 20)->default('none')->after('status');
            }
            if (!Schema::hasColumn('shift_kasirs', 'reopen_reason')) {
                $table->text('reopen_reason')->nullable()->after('reopen_status');
            }
            if (!Schema::hasColumn('shift_kasirs', 'reopen_requested_by')) {
                $table->foreignId('reopen_requested_by')->nullable()->constrained('users')->nullOnDelete()->after('reopen_reason');
            }
            if (!Schema::hasColumn('shift_kasirs', 'reopen_requested_at')) {
                $table->dateTime('reopen_requested_at')->nullable()->after('reopen_requested_by');
            }
            if (!Schema::hasColumn('shift_kasirs', 'reopen_approved_by')) {
                $table->foreignId('reopen_approved_by')->nullable()->constrained('users')->nullOnDelete()->after('reopen_requested_at');
            }
            if (!Schema::hasColumn('shift_kasirs', 'reopen_approved_at')) {
                $table->dateTime('reopen_approved_at')->nullable()->after('reopen_approved_by');
            }
            if (!Schema::hasColumn('shift_kasirs', 'reopen_notes')) {
                $table->text('reopen_notes')->nullable()->after('reopen_approved_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_kasirs', function (Blueprint $table) {
            $cols = [
                'reopen_status',
                'reopen_reason',
                'reopen_requested_by',
                'reopen_requested_at',
                'reopen_approved_by',
                'reopen_approved_at',
                'reopen_notes',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('shift_kasirs', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
