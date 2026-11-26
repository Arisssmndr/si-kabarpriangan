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
        // 1. Tabel RAB Priangan (Rencana Anggaran Biaya per Bidang)
        if (!Schema::hasTable('anggaran_rabs')) {
            Schema::create('anggaran_rabs', function (Blueprint $table) {
                $table->id();
                $table->string('periode', 7); // Format: YYYY-MM
                $table->string('kode_bagian', 20); // '01' - '07'
                $table->string('divisi');
                $table->decimal('plafon_anggaran', 15, 2)->default(0);
                $table->text('keterangan')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['periode', 'kode_bagian']);
            });
        }

        // 2. Tabel Tutup Anggaran (Closing Periode Anggaran)
        if (!Schema::hasTable('tutup_anggarans')) {
            Schema::create('tutup_anggarans', function (Blueprint $table) {
                $table->id();
                $table->string('periode', 7)->unique(); // Format: YYYY-MM
                $table->date('tanggal_tutup');
                $table->decimal('total_plafon', 15, 2)->default(0);
                $table->decimal('total_bp_acc', 15, 2)->default(0);
                $table->decimal('total_cair_kasir', 15, 2)->default(0);
                $table->decimal('sisa_anggaran', 15, 2)->default(0);
                $table->enum('status', ['closed', 'draft'])->default('closed');
                $table->text('catatan')->nullable();
                $table->foreignId('ditutup_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 3. Tabel DP Kasbon (Uang Muka / Kasbon Operasional Divisi)
        if (!Schema::hasTable('dp_kasbons')) {
            Schema::create('dp_kasbons', function (Blueprint $table) {
                $table->id();
                $table->string('no_kasbon')->unique(); // KB-YYYYMM-XXXX
                $table->date('tanggal_kasbon');
                $table->string('kode_bagian', 20)->default('03');
                $table->string('divisi');
                $table->string('penerima');
                $table->decimal('nominal', 15, 2);
                $table->text('keperluan');
                $table->date('tgl_jatuh_tempo')->nullable();
                $table->enum('status', ['aktif', 'selesai', 'batal'])->default('aktif');
                $table->foreignId('bp_id')->nullable()->constrained('bukti_pengeluarans')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dp_kasbons');
        Schema::dropIfExists('tutup_anggarans');
        Schema::dropIfExists('anggaran_rabs');
    }
};
