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
        // 1. Audit Kolom Pembatalan pada Transaksi Koran
        Schema::table('transaksikoran', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksikoran', 'alasan_batal')) {
                $table->text('alasan_batal')->nullable()->after('status_order');
            }
            if (!Schema::hasColumn('transaksikoran', 'dibatalkan_at')) {
                $table->timestamp('dibatalkan_at')->nullable()->after('alasan_batal');
            }
            if (!Schema::hasColumn('transaksikoran', 'dibatalkan_oleh_user_id')) {
                $table->foreignId('dibatalkan_oleh_user_id')->nullable()->after('dibatalkan_at')->constrained('users')->nullOnDelete();
            }
        });

        // 2. Tabel Memo Unlock (Audit Trail Pembukaan Kunci Transaksi Pasca Cut-Off)
        if (!Schema::hasTable('memo_unlocks')) {
            Schema::create('memo_unlocks', function (Blueprint $table) {
                $table->id();
                $table->date('tanggal_target');
                $table->string('nomor_memo', 50);
                $table->text('alasan');
                $table->foreignId('dibuka_oleh_user_id')->constrained('users')->cascadeOnDelete();
                $table->enum('status', ['OPEN', 'CLOSED'])->default('OPEN');
                $table->timestamp('dibuka_pada')->nullable();
                $table->timestamp('ditutup_kembali_pada')->nullable();
                $table->timestamps();
            });
        }

        // 3. Tabel Daily Cut-Off (Pencatatan Tutup Transaksi Harian per Edisi Koran)
        if (!Schema::hasTable('daily_cutoffs')) {
            Schema::create('daily_cutoffs', function (Blueprint $table) {
                $table->id();
                $table->date('tanggal_edisi')->unique();
                $table->string('jam_cutoff', 10)->default('14:00');
                $table->boolean('is_closed')->default(false);
                $table->foreignId('closed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('closed_at')->nullable();
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }

        // 4. Harmonisasi Transaksi Online ke Standar Master AE & Pelanggan & PPN Dinamis
        Schema::table('transaksionline', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksionline', 'sales_ae_id')) {
                $table->foreignId('sales_ae_id')->nullable()->after('sales_iklanonline')->constrained('sales_ae')->nullOnDelete();
            }
            if (!Schema::hasColumn('transaksionline', 'pelanggan_id')) {
                $table->foreignId('pelanggan_id')->nullable()->after('nama_pemasangonline')->constrained('pelanggan')->nullOnDelete();
            }
            if (!Schema::hasColumn('transaksionline', 'ppn_persen')) {
                $table->decimal('ppn_persen', 5, 2)->default(12.00)->after('ppn_transaksionline');
            }
        });

        // 5. Harmonisasi Transaksi Priangan TV ke Standar Master AE & Pelanggan & PPN Dinamis
        Schema::table('transaksipriangan', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksipriangan', 'sales_ae_id')) {
                $table->foreignId('sales_ae_id')->nullable()->after('sales_iklanpriangan')->constrained('sales_ae')->nullOnDelete();
            }
            if (!Schema::hasColumn('transaksipriangan', 'pelanggan_id')) {
                $table->foreignId('pelanggan_id')->nullable()->after('nama_pemasangpriangan')->constrained('pelanggan')->nullOnDelete();
            }
            if (!Schema::hasColumn('transaksipriangan', 'ppn_persen')) {
                $table->decimal('ppn_persen', 5, 2)->default(12.00)->after('ppn_transaksipriangan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksipriangan', function (Blueprint $table) {
            if (Schema::hasColumn('transaksipriangan', 'sales_ae_id')) {
                $table->dropForeign(['sales_ae_id']);
                $table->dropColumn('sales_ae_id');
            }
            if (Schema::hasColumn('transaksipriangan', 'pelanggan_id')) {
                $table->dropForeign(['pelanggan_id']);
                $table->dropColumn('pelanggan_id');
            }
            if (Schema::hasColumn('transaksipriangan', 'ppn_persen')) {
                $table->dropColumn('ppn_persen');
            }
        });

        Schema::table('transaksionline', function (Blueprint $table) {
            if (Schema::hasColumn('transaksionline', 'sales_ae_id')) {
                $table->dropForeign(['sales_ae_id']);
                $table->dropColumn('sales_ae_id');
            }
            if (Schema::hasColumn('transaksionline', 'pelanggan_id')) {
                $table->dropForeign(['pelanggan_id']);
                $table->dropColumn('pelanggan_id');
            }
            if (Schema::hasColumn('transaksionline', 'ppn_persen')) {
                $table->dropColumn('ppn_persen');
            }
        });

        Schema::dropIfExists('daily_cutoffs');
        Schema::dropIfExists('memo_unlocks');

        Schema::table('transaksikoran', function (Blueprint $table) {
            if (Schema::hasColumn('transaksikoran', 'dibatalkan_oleh_user_id')) {
                $table->dropForeign(['dibatalkan_oleh_user_id']);
                $table->dropColumn('dibatalkan_oleh_user_id');
            }
            if (Schema::hasColumn('transaksikoran', 'dibatalkan_at')) {
                $table->dropColumn('dibatalkan_at');
            }
            if (Schema::hasColumn('transaksikoran', 'alasan_batal')) {
                $table->dropColumn('alasan_batal');
            }
        });
    }
};
