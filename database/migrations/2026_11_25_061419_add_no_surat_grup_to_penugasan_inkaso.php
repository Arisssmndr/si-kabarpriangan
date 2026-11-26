<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom no_surat_grup pada penugasan_inkaso.
     *
     * Satu Nomor Surat Penugasan (no_surat_grup) dapat mengikat banyak faktur/penugasan
     * yang di-assign dalam satu batch oleh Admin Faktur pada hari yang sama.
     *
     * Format: ST-YYYYMM-XXX (misal: ST-202611-001)
     * Data lama (NULL) = surat penugasan individual (backward-compatible).
     */
    public function up(): void
    {
        Schema::table('penugasan_inkaso', function (Blueprint $table) {
            if (!Schema::hasColumn('penugasan_inkaso', 'no_surat_grup')) {
                $table->string('no_surat_grup', 30)->nullable()->index()->after('no_tugas');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penugasan_inkaso', function (Blueprint $table) {
            $table->dropColumn('no_surat_grup');
        });
    }
};
