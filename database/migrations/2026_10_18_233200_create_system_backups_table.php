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
        Schema::create('system_backups', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('filepath', 500);
            $table->unsignedBigInteger('filesize_bytes');
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'semi_annual', 'yearly', 'manual'])->default('daily');
            $table->string('checksum_sha256', 64);
            $table->boolean('is_encrypted')->default(false);
            $table->enum('status', ['success', 'failed', 'verified'])->default('success');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['frequency', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_backups');
    }
};
