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
        // 1. Bersihkan kolom tarif insentif & komisi dari Master SALES_AE (karena kini fleksibel di transaksi)
        Schema::table('sales_ae', function (Blueprint $table) {
            if (Schema::hasColumn('sales_ae', 'tarif_insentif_default')) {
                $table->dropColumn('tarif_insentif_default');
            }
            if (Schema::hasColumn('sales_ae', 'tarif_komisi_max')) {
                $table->dropColumn('tarif_komisi_max');
            }
        });

        // 2. Tambahkan kolom Komisioner (Pihak Ketiga/Perantara/Calo) & Persentase Fleksibel pada Transaksi Koran
        Schema::table('transaksikoran', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksikoran', 'komisioner')) {
                $table->string('komisioner', 150)->nullable()->after('sales_ae_id');
            }
            if (!Schema::hasColumn('transaksikoran', 'komisi_persen')) {
                $table->decimal('komisi_persen', 5, 2)->default(0)->after('komisioner');
            }
            if (!Schema::hasColumn('transaksikoran', 'insentif_persen')) {
                $table->decimal('insentif_persen', 5, 2)->default(0)->after('komisi_transaksikoran');
            }
        });

        // 3. Selaraskan pula pada Transaksi Online & Priangan TV
        if (Schema::hasTable('transaksionline')) {
            Schema::table('transaksionline', function (Blueprint $table) {
                if (!Schema::hasColumn('transaksionline', 'komisioner')) {
                    $table->string('komisioner', 150)->nullable()->after('sales_ae_id');
                }
                if (!Schema::hasColumn('transaksionline', 'komisi_persen')) {
                    $table->decimal('komisi_persen', 5, 2)->default(0)->after('komisioner');
                }
                if (!Schema::hasColumn('transaksionline', 'komisi_transaksionline')) {
                    $table->bigInteger('komisi_transaksionline')->default(0)->after('komisi_persen');
                }
                if (!Schema::hasColumn('transaksionline', 'insentif_persen')) {
                    $table->decimal('insentif_persen', 5, 2)->default(0)->after('komisi_transaksionline');
                }
                if (!Schema::hasColumn('transaksionline', 'insentif_transaksionline')) {
                    $table->bigInteger('insentif_transaksionline')->default(0)->after('insentif_persen');
                }
            });
        }

        if (Schema::hasTable('transaksipriangan')) {
            Schema::table('transaksipriangan', function (Blueprint $table) {
                if (!Schema::hasColumn('transaksipriangan', 'komisioner')) {
                    $table->string('komisioner', 150)->nullable()->after('sales_iklanpriangan');
                }
                if (!Schema::hasColumn('transaksipriangan', 'komisi_persen')) {
                    $table->decimal('komisi_persen', 5, 2)->default(0)->after('komisioner');
                }
                if (!Schema::hasColumn('transaksipriangan', 'insentif_persen')) {
                    $table->decimal('insentif_persen', 5, 2)->default(0);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_ae', function (Blueprint $table) {
            $table->decimal('tarif_insentif_default', 5, 2)->default(5.00);
            $table->decimal('tarif_komisi_max', 5, 2)->default(20.00);
        });

        Schema::table('transaksikoran', function (Blueprint $table) {
            $table->dropColumn(['komisioner', 'komisi_persen', 'insentif_persen']);
        });
    }
};
