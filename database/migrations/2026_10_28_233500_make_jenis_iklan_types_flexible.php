<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('jenis_iklan')) {
            // Ubah tipe_dasar dan cara_hitung menjadi varchar fleksibel agar dapat menampung kategori digital & TV
            DB::statement("ALTER TABLE `jenis_iklan` MODIFY `tipe_dasar` VARCHAR(50) NOT NULL DEFAULT 'DISPLAY'");
            DB::statement("ALTER TABLE `jenis_iklan` MODIFY `cara_hitung` VARCHAR(50) NOT NULL DEFAULT 'PAKET_FIXED'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert jika diperlukan
    }
};
