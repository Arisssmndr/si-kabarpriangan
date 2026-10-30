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
        if (Schema::hasTable('pembayaran_faktur')) {
            DB::statement("ALTER TABLE `pembayaran_faktur` MODIFY COLUMN `tipe_bayar` VARCHAR(50) NOT NULL DEFAULT 'lunas_langsung'");
            DB::statement("ALTER TABLE `pembayaran_faktur` MODIFY COLUMN `metode_bayar` VARCHAR(50) NOT NULL DEFAULT 'tunai'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pembayaran_faktur')) {
            DB::statement("ALTER TABLE `pembayaran_faktur` MODIFY COLUMN `tipe_bayar` ENUM('lunas_langsung', 'cicilan_inkaso', 'dp_titipan') NOT NULL DEFAULT 'lunas_langsung'");
        }
    }
};
