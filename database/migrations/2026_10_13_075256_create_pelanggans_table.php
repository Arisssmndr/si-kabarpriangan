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
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pelanggan')->unique();
            $table->string('nama_instansi');
            $table->enum('jenis_klien', ['Perorangan', 'Swasta', 'Pemda', 'BUMN', 'Agensi'])->default('Perorangan');
            $table->string('npwp_nik')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kontak_pic')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('email')->nullable();
            $table->bigInteger('credit_limit')->default(0)->comment('Batas kredit (Rp). 0 = Wajib Cash Before Print (CBP)');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed Klien Representatif Standar Industri Media Priangan Timur
        $kliens = [
            [
                'kode_pelanggan' => 'PLG-0001',
                'nama_instansi'  => 'Bagian Protokol & Kompim Pemkot Tasikmalaya',
                'jenis_klien'    => 'Pemda',
                'npwp_nik'       => '00.123.456.7-425.000',
                'alamat'         => 'Jl. Letnan Harun No. 1, Kota Tasikmalaya',
                'kontak_pic'     => 'H. Dedi Permana (Kasubag Kompim)',
                'no_telp'        => '081223456789',
                'email'          => 'humas@tasikmalayakota.go.id',
                'credit_limit'   => 50000000, // Rp 50 Juta
            ],
            [
                'kode_pelanggan' => 'PLG-0002',
                'nama_instansi'  => 'Biro Iklan Citra Pariwara Mandiri',
                'jenis_klien'    => 'Agensi',
                'npwp_nik'       => '01.987.654.3-425.000',
                'alamat'         => 'Jl. KHZ. Mustofa No. 142, Kota Tasikmalaya',
                'kontak_pic'     => 'Rian Firmansyah (Media Planner)',
                'no_telp'        => '081398765432',
                'email'          => 'media@citrapariwara.co.id',
                'credit_limit'   => 25000000, // Rp 25 Juta
            ],
            [
                'kode_pelanggan' => 'PLG-0003',
                'nama_instansi'  => 'Diskominfo Kabupaten Ciamis',
                'jenis_klien'    => 'Pemda',
                'npwp_nik'       => '00.321.654.9-425.000',
                'alamat'         => 'Jl. Jendral Sudirman No. 90, Ciamis',
                'kontak_pic'     => 'Drs. Asep Sunarya',
                'no_telp'        => '082112345678',
                'email'          => 'diskominfo@ciamiskab.go.id',
                'credit_limit'   => 40000000,
            ],
            [
                'kode_pelanggan' => 'PLG-0004',
                'nama_instansi'  => 'PT Bank BJB Kantor Cabang Tasikmalaya',
                'jenis_klien'    => 'BUMN',
                'npwp_nik'       => '01.234.567.8-425.000',
                'alamat'         => 'Jl. Mayor Utarya No. 20, Kota Tasikmalaya',
                'kontak_pic'     => 'Ibu Maya (Corporate Secretary)',
                'no_telp'        => '081122334455',
                'email'          => 'tasikmalaya@bankbjb.co.id',
                'credit_limit'   => 30000000,
            ],
            [
                'kode_pelanggan' => 'PLG-0005',
                'nama_instansi'  => 'Budi Santoso (Walk-in Customer)',
                'jenis_klien'    => 'Perorangan',
                'npwp_nik'       => '3278011205850001',
                'alamat'         => 'Jl. Tentara Pelajar No. 45, Tasikmalaya',
                'kontak_pic'     => 'Budi Santoso',
                'no_telp'        => '085223344556',
                'email'          => 'budi.santoso@gmail.com',
                'credit_limit'   => 0, // CBP WAJIB
            ],
        ];

        foreach ($kliens as $klien) {
            \Illuminate\Support\Facades\DB::table('pelanggan')->insert(array_merge($klien, [
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};
