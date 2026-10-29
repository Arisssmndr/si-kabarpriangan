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
        // 1. Tambah kolom faktur fisik HU. Priangan ke transaksikoran
        if (Schema::hasTable('transaksikoran')) {
            Schema::table('transaksikoran', function (Blueprint $table) {
                if (!Schema::hasColumn('transaksikoran', 'identitas')) {
                    $table->string('identitas')->nullable()->after('nofakturkoran');
                }
                if (!Schema::hasColumn('transaksikoran', 'no_order')) {
                    $table->string('no_order')->nullable()->after('identitas');
                }
                if (!Schema::hasColumn('transaksikoran', 'kota')) {
                    $table->string('kota')->nullable()->after('alamat_pemasangkoran');
                }
                if (!Schema::hasColumn('transaksikoran', 'kop_iklan')) {
                    $table->string('kop_iklan')->nullable()->after('warna_iklan');
                }
                if (!Schema::hasColumn('transaksikoran', 'merk')) {
                    $table->string('merk')->nullable()->after('kop_iklan');
                }
                if (!Schema::hasColumn('transaksikoran', 'materi_iklan')) {
                    $table->text('materi_iklan')->nullable()->after('merk');
                }
                if (!Schema::hasColumn('transaksikoran', 'diskon_persen')) {
                    $table->decimal('diskon_persen', 6, 2)->default(0)->after('diskon_transaksikoran');
                }
                if (!Schema::hasColumn('transaksikoran', 'brutto_transaksikoran')) {
                    $table->bigInteger('brutto_transaksikoran')->default(0)->after('harga_transaksikoran');
                }
                if (!Schema::hasColumn('transaksikoran', 'netto_transaksikoran')) {
                    $table->bigInteger('netto_transaksikoran')->default(0)->after('diskon_persen');
                }
                if (!Schema::hasColumn('transaksikoran', 'materai_transaksikoran')) {
                    $table->bigInteger('materai_transaksikoran')->default(0)->after('ppn_transaksikoran');
                }
            });
        }

        // 2. Tambah kolom ke customers
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                if (!Schema::hasColumn('customers', 'identitas')) {
                    $table->string('identitas')->nullable()->after('nama_customer');
                }
                if (!Schema::hasColumn('customers', 'kota')) {
                    $table->string('kota')->nullable()->after('alamat');
                }
            });
        }

        // 3. Tambah kolom ke faktur
        if (Schema::hasTable('faktur')) {
            Schema::table('faktur', function (Blueprint $table) {
                if (!Schema::hasColumn('faktur', 'no_order')) {
                    $table->string('no_order')->nullable()->after('no_faktur');
                }
                if (!Schema::hasColumn('faktur', 'materi_iklan')) {
                    $table->text('materi_iklan')->nullable()->after('tipe_faktur');
                }
                if (!Schema::hasColumn('faktur', 'brutto')) {
                    $table->decimal('brutto', 15, 2)->default(0)->after('rincian_biaya');
                }
                if (!Schema::hasColumn('faktur', 'diskon_persen')) {
                    $table->decimal('diskon_persen', 6, 2)->default(0)->after('brutto');
                }
                if (!Schema::hasColumn('faktur', 'diskon_nominal')) {
                    $table->decimal('diskon_nominal', 15, 2)->default(0)->after('diskon_persen');
                }
                if (!Schema::hasColumn('faktur', 'netto')) {
                    $table->decimal('netto', 15, 2)->default(0)->after('diskon_nominal');
                }
                if (!Schema::hasColumn('faktur', 'ppn_nominal')) {
                    $table->decimal('ppn_nominal', 15, 2)->default(0)->after('netto');
                }
                if (!Schema::hasColumn('faktur', 'materai')) {
                    $table->decimal('materai', 15, 2)->default(0)->after('ppn_nominal');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transaksikoran')) {
            Schema::table('transaksikoran', function (Blueprint $table) {
                $table->dropColumn([
                    'identitas',
                    'no_order',
                    'kota',
                    'kop_iklan',
                    'merk',
                    'materi_iklan',
                    'diskon_persen',
                    'brutto_transaksikoran',
                    'netto_transaksikoran',
                    'materai_transaksikoran',
                ]);
            });
        }

        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn(['identitas', 'kota']);
            });
        }

        if (Schema::hasTable('faktur')) {
            Schema::table('faktur', function (Blueprint $table) {
                $table->dropColumn([
                    'no_order',
                    'materi_iklan',
                    'brutto',
                    'diskon_persen',
                    'diskon_nominal',
                    'netto',
                    'ppn_nominal',
                    'materai',
                ]);
            });
        }
    }
};
