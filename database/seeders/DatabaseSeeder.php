<?php

namespace Database\Seeders;

use App\Models\KonfigurasiGudang;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(XlsxBarangSeeder::class);

        KonfigurasiGudang::updateOrCreate(
            ['nama' => 'Kapasitas Gudang Utama'],
            ['kapasitas_maks' => 80, 'aktif' => true],
        );

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Gudang',
                'password' => 'password',
                'role' => User::ROLE_ADMIN,
            ],
        );

        User::updateOrCreate(
            ['email' => 'penjual@example.com'],
            [
                'name' => 'Penjual A',
                'password' => 'password',
                'role' => User::ROLE_USER,
            ],
        );
    }
}
