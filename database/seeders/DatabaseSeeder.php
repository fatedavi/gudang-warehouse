<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\KonfigurasiGudang;
use App\Models\User;
use App\Services\XlsxReader;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $barangs = XlsxReader::rows(storage_path('app/data gudang untuk skripsi.xlsx'));

        $perbaikiWarna = ['hiam' => 'hitam', 'jitam' => 'hitam'];
        $perbaikiMerk = ['aidas' => 'adidas'];
        $jenisSebelumnya = null;

        foreach ($barangs as $row) {
            $jenis = trim($row['jenis_barang'] ?? '');
            if ($jenis === '') {
                $jenis = $jenisSebelumnya;
            }
            $jenisSebelumnya = $jenis;

            $warna = strtolower(trim($row['warna_produk'] ?? ''));
            $warna = $perbaikiWarna[$warna] ?? $warna;

            $merk = trim($row['merk_produk'] ?? '');
            $merk = $perbaikiMerk[strtolower($merk)] ?? $merk;

            $kode = trim($row['kode_produk'] ?? '');
            if ($kode !== '' && ! str_ends_with($kode, '-'.Barang::SUFIX_BARU) && ! str_ends_with($kode, '-'.Barang::SUFIX_LAMA)) {
                $kode .= '-'.Barang::SUFIX_BARU;
            }

            Barang::create([
                'kode_produk' => $kode,
                'jenis_barang' => $jenis,
                'merk_produk' => $merk,
                'ukuran_produk' => trim($row['ukuran_produk'] ?? ''),
                'warna_produk' => $warna,
                'kondisi_barang' => trim($row['kondisi_barang'] ?? 'baru'),
                'qty' => (int) ($row['qty'] ?? 0),
                'terjual' => (int) ($row['terjual'] ?? 0),
                'sisa_stok' => (int) ($row['sisa_stok'] ?? 0),
                'harga_gudang' => (int) ($row['harga_gudang'] ?? 0),
                'harga_jual' => (int) ($row['harga_jual'] ?? 0),
                'margin_kotor' => (int) ($row['margin_kotor'] ?? 0),
                'margin_persen' => round((float) ($row['margin_persen'] ?? 0) * 100, 2),
            ]);
        }

        KonfigurasiGudang::create([
            'nama' => 'Kapasitas Gudang Utama',
            'kapasitas_maks' => 80,
            'aktif' => true,
        ]);

        User::factory()->create([
            'name' => 'Admin Gudang',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
        ]);

        User::factory()->create([
            'name' => 'Penjual A',
            'email' => 'penjual@example.com',
            'password' => 'password',
            'role' => User::ROLE_USER,
        ]);
    }
}
