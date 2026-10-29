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
        Schema::table('bukti_pengeluarans', function (Blueprint $table) {
            $table->string('kode_bagian', 20)->nullable()->after('divisi');
            $table->string('kode_perkiraan', 50)->nullable()->after('kode_bagian');
            $table->string('nama_perkiraan', 255)->nullable()->after('kode_perkiraan');
            $table->decimal('nominal_pengajuan', 15, 2)->nullable()->after('keperluan');
            $table->text('catatan')->nullable()->after('nominal_acc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bukti_pengeluarans', function (Blueprint $table) {
            $table->dropColumn(['kode_bagian', 'kode_perkiraan', 'nama_perkiraan', 'nominal_pengajuan', 'catatan']);
        });
    }
};
