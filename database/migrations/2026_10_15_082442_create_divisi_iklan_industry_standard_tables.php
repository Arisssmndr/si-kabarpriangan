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
        // 1. Master KOP KOTA (Penempatan Halaman Koran & Biro)
        if (!Schema::hasTable('kop_kota')) {
            Schema::create('kop_kota', function (Blueprint $table) {
                $table->id();
                $table->string('kode', 30)->unique();
                $table->integer('nomor_halaman')->default(1);
                $table->string('nama_penempatan', 100);
                $table->string('wilayah_biro', 100)->default('Umum');
                $table->decimal('surcharge_persen', 5, 2)->default(0.00);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Master KOP IKLAN (Rubrik Kategori Iklan Baris)
        if (!Schema::hasTable('kop_iklan')) {
            Schema::create('kop_iklan', function (Blueprint $table) {
                $table->id();
                $table->string('kode', 30)->unique();
                $table->string('nama_kop', 100);
                $table->text('deskripsi')->nullable();
                $table->integer('urutan')->default(1);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 3. Master SALES / AE (Account Executive)
        if (!Schema::hasTable('sales_ae')) {
            Schema::create('sales_ae', function (Blueprint $table) {
                $table->id();
                $table->string('kode_ae', 20)->unique();
                $table->string('nama_ae', 150);
                $table->enum('jenis_kode', ['STRUKTURAL', 'KORESPONDEN', 'FO'])->default('STRUKTURAL');
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->decimal('tarif_insentif_default', 5, 2)->default(5.00);
                $table->decimal('tarif_komisi_max', 5, 2)->default(20.00);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 4. Update JENIS_IKLAN (Unified Product Catalog Standard)
        Schema::table('jenis_iklan', function (Blueprint $table) {
            if (!Schema::hasColumn('jenis_iklan', 'tipe_dasar')) {
                $table->enum('tipe_dasar', ['BARIS', 'KOLOM', 'DISPLAY', 'ADVERTORIAL', 'BONUS', 'KHUSUS'])->default('DISPLAY')->after('nama_jenis');
            }
            if (!Schema::hasColumn('jenis_iklan', 'mode_warna')) {
                $table->enum('mode_warna', ['BW', 'FC', 'SPOT'])->default('BW')->after('tipe_dasar');
            }
            if (!Schema::hasColumn('jenis_iklan', 'cara_hitung')) {
                $table->enum('cara_hitung', ['PER_BARIS', 'PER_MMK', 'PAKET_FIXED', 'FIXED_ZERO'])->default('PER_MMK')->after('mode_warna');
            }
            if (!Schema::hasColumn('jenis_iklan', 'is_fixed_size')) {
                $table->boolean('is_fixed_size')->default(false)->after('cara_hitung');
            }
            if (!Schema::hasColumn('jenis_iklan', 'default_kolom')) {
                $table->integer('default_kolom')->nullable()->after('is_fixed_size');
            }
            if (!Schema::hasColumn('jenis_iklan', 'default_tinggi_mm')) {
                $table->integer('default_tinggi_mm')->nullable()->after('default_kolom');
            }
            if (!Schema::hasColumn('jenis_iklan', 'max_kolom')) {
                $table->integer('max_kolom')->default(7)->after('default_tinggi_mm');
            }
            if (!Schema::hasColumn('jenis_iklan', 'max_tinggi_mm')) {
                $table->integer('max_tinggi_mm')->default(540)->after('max_kolom');
            }
            if (!Schema::hasColumn('jenis_iklan', 'min_order')) {
                $table->integer('min_order')->default(2)->after('max_tinggi_mm');
            }
        });

        // 5. Update TRANSAKSIKORAN (Master References & File Materi)
        Schema::table('transaksikoran', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksikoran', 'sales_ae_id')) {
                $table->foreignId('sales_ae_id')->nullable()->after('sales_iklankoran')->constrained('sales_ae')->nullOnDelete();
            }
            if (!Schema::hasColumn('transaksikoran', 'kop_iklan_id')) {
                $table->foreignId('kop_iklan_id')->nullable()->after('kop_iklan_kota')->constrained('kop_iklan')->nullOnDelete();
            }
            if (!Schema::hasColumn('transaksikoran', 'kop_kota_id')) {
                $table->foreignId('kop_kota_id')->nullable()->after('kop_iklan_id')->constrained('kop_kota')->nullOnDelete();
            }
            if (!Schema::hasColumn('transaksikoran', 'file_materi')) {
                $table->string('file_materi', 255)->nullable()->after('status_materi');
            }
        });

        // 6. Tabel ORDER_JADWAL_MUAT (Multi-Date Scheduling Standar Industri)
        if (!Schema::hasTable('order_jadwal_muat')) {
            Schema::create('order_jadwal_muat', function (Blueprint $table) {
                $table->id();
                $table->foreignId('transaksi_id')->constrained('transaksikoran')->cascadeOnDelete();
                $table->date('tanggal_muat');
                $table->integer('edisi_ke')->default(1);
                $table->enum('status_tayang', ['TERJADWAL', 'SUDAH_TAYANG', 'RESCHEDULE', 'BATAL'])->default('TERJADWAL');
                $table->string('bukti_muat_pdf', 255)->nullable();
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_jadwal_muat');

        Schema::table('transaksikoran', function (Blueprint $table) {
            if (Schema::hasColumn('transaksikoran', 'sales_ae_id')) {
                $table->dropForeign(['sales_ae_id']);
                $table->dropColumn('sales_ae_id');
            }
            if (Schema::hasColumn('transaksikoran', 'kop_iklan_id')) {
                $table->dropForeign(['kop_iklan_id']);
                $table->dropColumn('kop_iklan_id');
            }
            if (Schema::hasColumn('transaksikoran', 'kop_kota_id')) {
                $table->dropForeign(['kop_kota_id']);
                $table->dropColumn('kop_kota_id');
            }
            if (Schema::hasColumn('transaksikoran', 'file_materi')) {
                $table->dropColumn('file_materi');
            }
        });

        Schema::table('jenis_iklan', function (Blueprint $table) {
            $cols = ['tipe_dasar', 'mode_warna', 'cara_hitung', 'is_fixed_size', 'default_kolom', 'default_tinggi_mm', 'max_kolom', 'max_tinggi_mm', 'min_order'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('jenis_iklan', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::dropIfExists('sales_ae');
        Schema::dropIfExists('kop_iklan');
        Schema::dropIfExists('kop_kota');
    }
};
