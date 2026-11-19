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
        Schema::table('penugasan_inkaso', function (Blueprint $table) {
            if (!Schema::hasColumn('penugasan_inkaso', 'nama_petugas')) {
                $table->string('nama_petugas', 150)->nullable()->after('petugas_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penugasan_inkaso', function (Blueprint $table) {
            if (Schema::hasColumn('penugasan_inkaso', 'nama_petugas')) {
                $table->dropColumn('nama_petugas');
            }
        });
    }
};
