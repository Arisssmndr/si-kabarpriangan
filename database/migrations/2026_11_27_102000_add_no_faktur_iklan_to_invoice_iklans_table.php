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
        Schema::table('invoice_iklans', function (Blueprint $table) {
            $table->string('no_faktur_iklan')->nullable()->after('no_invoice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_iklans', function (Blueprint $table) {
            $table->dropColumn('no_faktur_iklan');
        });
    }
};
