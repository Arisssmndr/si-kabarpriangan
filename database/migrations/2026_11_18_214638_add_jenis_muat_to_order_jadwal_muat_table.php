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
        Schema::table('order_jadwal_muat', function (Blueprint $table) {
            $table->string('jenis_muat', 20)->default('BERNILAI')->after('edisi_ke')->comment('BERNILAI = Edisi beromzet/bernilai, BONUS = Edisi lanjutan/muat bonus');
            $table->decimal('nominal_muat', 15, 2)->default(0)->after('jenis_muat')->comment('Nilai omzet pemuatan edisi');
            $table->boolean('is_bonus')->default(false)->after('nominal_muat')->comment('Flag muat bonus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_jadwal_muat', function (Blueprint $table) {
            $table->dropColumn(['jenis_muat', 'nominal_muat', 'is_bonus']);
        });
    }
};
