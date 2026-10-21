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
        // 1. Tambah kolom pajak instansi & kode agen pada kas_masuks
        if (Schema::hasTable('kas_masuks')) {
            Schema::table('kas_masuks', function (Blueprint $table) {
                if (!Schema::hasColumn('kas_masuks', 'nilai_dpp')) {
                    $table->decimal('nilai_dpp', 15, 2)->default(0)->after('total_tagihan');
                }
                if (!Schema::hasColumn('kas_masuks', 'potongan_ppn')) {
                    $table->decimal('potongan_ppn', 15, 2)->default(0)->after('nilai_dpp');
                }
                if (!Schema::hasColumn('kas_masuks', 'potongan_pph23')) {
                    $table->decimal('potongan_pph23', 15, 2)->default(0)->after('potongan_ppn');
                }
                if (!Schema::hasColumn('kas_masuks', 'is_instansi_tax')) {
                    $table->boolean('is_instansi_tax')->default(false)->after('potongan_pph23');
                }
                if (!Schema::hasColumn('kas_masuks', 'kode_agen_input')) {
                    $table->string('kode_agen_input')->nullable()->after('kode_agen_ref');
                }
            });
        }

        // 2. Tambah kolom is_tax_offset & is_kasbon pada kas_keluars
        if (Schema::hasTable('kas_keluars')) {
            Schema::table('kas_keluars', function (Blueprint $table) {
                if (!Schema::hasColumn('kas_keluars', 'is_tax_offset')) {
                    $table->boolean('is_tax_offset')->default(false)->after('metode_bayar');
                }
                if (!Schema::hasColumn('kas_keluars', 'is_kasbon')) {
                    $table->boolean('is_kasbon')->default(false)->after('is_tax_offset');
                }
            });
        }

        // 3. Tambah kolom total_kasbon_gantung pada shift_kasirs
        if (Schema::hasTable('shift_kasirs')) {
            Schema::table('shift_kasirs', function (Blueprint $table) {
                if (!Schema::hasColumn('shift_kasirs', 'total_kasbon_gantung')) {
                    $table->decimal('total_kasbon_gantung', 15, 2)->default(0)->after('saldo_akhir_teoritis');
                }
            });
        }

        // 4. Buat tabel kasbons untuk pencatatan uang muka / kasbon operasional meja kasir
        if (!Schema::hasTable('kasbons')) {
            Schema::create('kasbons', function (Blueprint $table) {
                $table->id();
                $table->string('no_kasbon')->unique(); // Format: KB-YYYYMMXXXX
                $table->foreignId('shift_id')->nullable()->constrained('shift_kasirs')->nullOnDelete();
                $table->date('tanggal');
                $table->string('nama_penerima');
                $table->text('keperluan');
                $table->decimal('nominal', 15, 2)->default(0);
                $table->string('status')->default('pending'); // 'pending' (gantung), 'lunas', 'masuk_bp'
                $table->string('approved_by')->nullable(); // Nama Pejabat Keuangan yang ACC (e.g. Asisten Manajer Keuangan)
                $table->foreignId('kasir_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('keterangan')->nullable();
                $table->timestamp('selesai_at')->nullable();
                $table->timestamps();

                $table->index(['tanggal', 'status']);
                $table->index(['shift_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kasbons');

        if (Schema::hasTable('shift_kasirs') && Schema::hasColumn('shift_kasirs', 'total_kasbon_gantung')) {
            Schema::table('shift_kasirs', function (Blueprint $table) {
                $table->dropColumn('total_kasbon_gantung');
            });
        }

        if (Schema::hasTable('kas_keluars')) {
            Schema::table('kas_keluars', function (Blueprint $table) {
                if (Schema::hasColumn('kas_keluars', 'is_kasbon')) {
                    $table->dropColumn('is_kasbon');
                }
                if (Schema::hasColumn('kas_keluars', 'is_tax_offset')) {
                    $table->dropColumn('is_tax_offset');
                }
            });
        }

        if (Schema::hasTable('kas_masuks')) {
            Schema::table('kas_masuks', function (Blueprint $table) {
                if (Schema::hasColumn('kas_masuks', 'kode_agen_input')) {
                    $table->dropColumn('kode_agen_input');
                }
                if (Schema::hasColumn('kas_masuks', 'is_instansi_tax')) {
                    $table->dropColumn('is_instansi_tax');
                }
                if (Schema::hasColumn('kas_masuks', 'potongan_pph23')) {
                    $table->dropColumn('potongan_pph23');
                }
                if (Schema::hasColumn('kas_masuks', 'potongan_ppn')) {
                    $table->dropColumn('potongan_ppn');
                }
                if (Schema::hasColumn('kas_masuks', 'nilai_dpp')) {
                    $table->dropColumn('nilai_dpp');
                }
            });
        }
    }
};
