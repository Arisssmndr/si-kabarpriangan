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
        if (!Schema::hasTable('rutes')) {
            Schema::create('rutes', function (Blueprint $table) {
                $table->id();
                $table->string('kode', 10)->unique();
                $table->string('nama_rute', 150);
                $table->string('armada', 150)->nullable();
                $table->string('sopir', 150)->nullable();
                $table->string('jam_berangkat', 50)->nullable()->default('02:30 WIB');
                $table->text('titik_tujuan')->nullable();
                $table->integer('alokasi_oplah')->default(0);
                $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
                $table->timestamps();

                $table->index(['kode', 'status']);
            });

            // Seed 15 Jurusan Rute Armada Distribusi Sirkulasi
            $initialRoutes = [
                [
                    'kode' => '01',
                    'nama_rute' => 'Ciamis - Pangandaran',
                    'armada' => 'Mobil L300 Box 01 (Z 8344 KC)',
                    'sopir' => 'Pak Tatang Supriatna',
                    'jam_berangkat' => '02:00 WIB',
                    'titik_tujuan' => 'Ciamis, Banjarsari, Kalipucang, Pangandaran, Parigi, Cijulang',
                    'alokasi_oplah' => 2200,
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode' => '02',
                    'nama_rute' => 'Tasikmalaya - Cipatujah',
                    'armada' => 'Mobil L300 Box 02 (Z 8190 HN)',
                    'sopir' => 'Pak Dedi Sutisna',
                    'jam_berangkat' => '02:15 WIB',
                    'titik_tujuan' => 'Sukawening, Karangnunggal, Cibalong, Pamijahan, Cipatujah',
                    'alokasi_oplah' => 1650,
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode' => '03',
                    'nama_rute' => 'Garut - Pameungpeuk',
                    'armada' => 'Mobil Gran Max Box 03 (Z 8621 DA)',
                    'sopir' => 'Kang Agus Mulyadi',
                    'jam_berangkat' => '02:15 WIB',
                    'titik_tujuan' => 'Tarogong, Garut Kota, Bayongbong, Cikajang, Cisurupan, Pameungpeuk',
                    'alokasi_oplah' => 1950,
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode' => '04',
                    'nama_rute' => 'NPTS Bandung - Panjalu',
                    'armada' => 'Mobil Carry Box 04 (Z 8812 HP)',
                    'sopir' => 'Kang Asep Kurniawan',
                    'jam_berangkat' => '02:30 WIB',
                    'titik_tujuan' => 'Rajapolah, Jamanis, Cihaurbeuti, Sukamantri, Situ Panjalu',
                    'alokasi_oplah' => 1400,
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode' => '05',
                    'nama_rute' => 'Bandung - Cirebon',
                    'armada' => 'Mobil Isuzu Elf 05 (Z 7720 EA)',
                    'sopir' => 'Pak Hendra Gunawan',
                    'jam_berangkat' => '01:45 WIB',
                    'titik_tujuan' => 'Cileunyi, Sumedang, Tomo, Kadipaten, Majalengka, Sumber Cirebon',
                    'alokasi_oplah' => 1550,
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode' => '06',
                    'nama_rute' => 'Tasikmalaya Kota - Singaparna',
                    'armada' => 'Mobil Carry Box 06 (Z 8421 HB)',
                    'sopir' => 'Kang Iwan Ridwan',
                    'jam_berangkat' => '03:00 WIB',
                    'titik_tujuan' => 'Mitra Batik, Pasar Wetan, Cihideung, Alun-alun Singaparna, Kudang',
                    'alokasi_oplah' => 3800,
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode' => '07',
                    'nama_rute' => 'Ciamis Kota - Kawali',
                    'armada' => 'Mobil Luxio Box 07 (Z 1450 TA)',
                    'sopir' => 'Kang Yana Suryana',
                    'jam_berangkat' => '02:45 WIB',
                    'titik_tujuan' => 'Alun-alun Ciamis, Pasar Manis, Baregbeg, Kawali, Lumbung',
                    'alokasi_oplah' => 2100,
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode' => '08',
                    'nama_rute' => 'Banjar Patroman - Banjarsari',
                    'armada' => 'Mobil Carry Box 08 (Z 8091 KA)',
                    'sopir' => 'Pak Nanang Sumarna',
                    'jam_berangkat' => '02:30 WIB',
                    'titik_tujuan' => 'Terminal Banjar, Langensari, Pataruman, Pamarican, Banjarsari',
                    'alokasi_oplah' => 1300,
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode' => '09',
                    'nama_rute' => 'Malangbong - Garut Kota',
                    'armada' => 'Mobil Gran Max 09 (Z 8234 DG)',
                    'sopir' => 'Kang Cecep Solihin',
                    'jam_berangkat' => '02:20 WIB',
                    'titik_tujuan' => 'Gentong, Malangbong, Bandrek, Cibatu, Wanaraja, Garut Kota',
                    'alokasi_oplah' => 1250,
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode' => '10',
                    'nama_rute' => 'Rajapolah - Cihaurbeuti - Panjalu',
                    'armada' => 'Mobil Carry Box 10 (Z 8555 TA)',
                    'sopir' => 'Pak Oding Rohidin',
                    'jam_berangkat' => '03:15 WIB',
                    'titik_tujuan' => 'Pasar Rajapolah, Ciawi, Sukamantri, Alun-alun Panjalu',
                    'alokasi_oplah' => 950,
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode' => '11',
                    'nama_rute' => 'Karangnunggal - Cibalong',
                    'armada' => 'Mobil L300 Box 11 (Z 8667 HP)',
                    'sopir' => 'Kang Ujang Mulyana',
                    'jam_berangkat' => '02:10 WIB',
                    'titik_tujuan' => 'Simpang, Karangnunggal, Bantarkalong, Cipatujah Pesisir',
                    'alokasi_oplah' => 850,
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode' => '12',
                    'nama_rute' => 'Sumedang - Wado - Darmaraja',
                    'armada' => 'Mobil Gran Max 12 (Z 8112 SD)',
                    'sopir' => 'Pak Encep Suhendar',
                    'jam_berangkat' => '02:00 WIB',
                    'titik_tujuan' => 'Kirihim, Wado, Waduk Jatigede, Darmaraja, Situraja, Sumedang',
                    'alokasi_oplah' => 1100,
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode' => '13',
                    'nama_rute' => 'Majalengka - Kuningan',
                    'armada' => 'Mobil Luxio Box 13 (Z 8712 MJ)',
                    'sopir' => 'Kang Rudi Hermawan',
                    'jam_berangkat' => '01:50 WIB',
                    'titik_tujuan' => 'Cikijing, Talaga, Maja, Majalengka Kota, Kadugede, Kuningan',
                    'alokasi_oplah' => 1200,
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode' => '14',
                    'nama_rute' => 'Bandung Timur - Cileunyi',
                    'armada' => 'Mobil Gran Max Box 14 (D 8452 BD)',
                    'sopir' => 'Pak Bambang Irawan',
                    'jam_berangkat' => '01:45 WIB',
                    'titik_tujuan' => 'Rancaekek, Jatinangor, Cileunyi, Ujungberung, Cibiru Bandung',
                    'alokasi_oplah' => 1450,
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode' => '15',
                    'nama_rute' => 'Jalur Khusus Kantor / Ekspedisi Subuh',
                    'armada' => 'Mobil Operasional Kantor 15 (Z 1001 KP)',
                    'sopir' => 'Tim Ekspedisi Internal KP',
                    'jam_berangkat' => '03:30 WIB',
                    'titik_tujuan' => 'Pengepakan Redaksi, Drop Point Khusus, Langganan VIP & Kantor Cabang',
                    'alokasi_oplah' => 750,
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            DB::table('rutes')->insert($initialRoutes);
        }

        if (Schema::hasTable('agens') && !Schema::hasColumn('agens', 'alamat')) {
            Schema::table('agens', function (Blueprint $table) {
                $table->text('alamat')->nullable()->after('wilayah');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('agens') && Schema::hasColumn('agens', 'alamat')) {
            Schema::table('agens', function (Blueprint $table) {
                $table->dropColumn('alamat');
            });
        }

        Schema::dropIfExists('rutes');
    }
};
