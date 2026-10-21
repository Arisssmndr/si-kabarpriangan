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
        if (Schema::hasTable('kas_keluars')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE `kas_keluars` MODIFY COLUMN `kategori` VARCHAR(100) NOT NULL DEFAULT 'umum'");
                DB::statement("ALTER TABLE `kas_keluars` MODIFY COLUMN `kategori_beban` VARCHAR(100) NULL DEFAULT 'umum'");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('kas_keluars')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE `kas_keluars` MODIFY COLUMN `kategori` ENUM('komisi_kh_insentif','operasional_bbm','redaksi','umum') NOT NULL DEFAULT 'umum'");
            }
        }
    }
};
