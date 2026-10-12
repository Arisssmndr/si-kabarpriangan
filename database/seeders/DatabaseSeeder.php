<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password');

        $users = [
            [
                'email'    => 'admin@kabarpriangan.co.id',
                'name'     => 'Super Administrator',
                'password' => $defaultPassword,
                'role'     => 'administrator',
            ],
            [
                'email'    => 'iklan@kabarpriangan.co.id',
                'name'     => 'Staf Divisi Iklan',
                'password' => $defaultPassword,
                'role'     => 'iklan',
            ],
            [
                'email'    => 'keuangan@kabarpriangan.co.id',
                'name'     => 'Staf Divisi Keuangan',
                'password' => $defaultPassword,
                'role'     => 'keuangan',
            ],
            [
                'email'    => 'accounting@kabarpriangan.co.id',
                'name'     => 'Staf Divisi Accounting',
                'password' => $defaultPassword,
                'role'     => 'accounting',
            ],
            [
                'email'    => 'sirkulasi@kabarpriangan.co.id',
                'name'     => 'Staf Divisi Sirkulasi',
                'password' => $defaultPassword,
                'role'     => 'sirkulasi',
            ],
            [
                'email'    => 'kasir@kabarpriangan.co.id',
                'name'     => 'Staf Divisi Kasir',
                'password' => $defaultPassword,
                'role'     => 'kasir',
            ],
            // User pengujian lama diperbarui menjadi role iklan agar sinkron dengan modul iklan yang sudah dikerjakan
            [
                'email'    => 'test@example.com',
                'name'     => 'Test User Iklan',
                'password' => $defaultPassword,
                'role'     => 'iklan',
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => $data['password'],
                    'role'     => $data['role'],
                ]
            );
        }

        $this->call([
            KategoriDanJenisIklanSeeder::class,
            SampleTransaksiSeeder::class,
        ]);
    }
}
