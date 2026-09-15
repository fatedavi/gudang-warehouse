<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Services\XlsxReader;
use Illuminate\Database\Seeder;
use Throwable;

class XlsxBarangSeeder extends Seeder
{
    public function run(): void
    {
        $rows = XlsxReader::rows(storage_path('app/data gudang untuk skripsi.xlsx'));

        $perbaikiWarna = ['hiam' => 'hitam', 'jitam' => 'hitam'];
        $perbaikiMerk = ['aidas' => 'adidas'];
        $jenisSebelumnya = null;

        $existing = Barang::select('id', 'kode_produk')->get()->keyBy(
            fn (Barang $b) => Barang::tanpaSuffix($b->kode_produk)
        );

        foreach ($rows as $row) {
            $jenis = trim($row['jenis_barang'] ?? '');
            if ($jenis === '') {
                $jenis = $jenisSebelumnya;
            }
            $jenisSebelumnya = $jenis;

            $warna = strtolower(trim($row['warna_produk'] ?? ''));
            $warna = $perbaikiWarna[$warna] ?? $warna;

            $merk = trim($row['merk_produk'] ?? '');
            $merk = $perbaikiMerk[strtolower($merk)] ?? $merk;

            $qty = max(0, (int) ($row['qty'] ?? 0));
            $terjual = max(0, min($qty, (int) ($row['terjual'] ?? 0)));
            $keluar = 0;
            $sisaStok = max(0, $qty - $terjual - $keluar);

            $data = [
                'jenis_barang' => $jenis,
                'merk_produk' => $merk,
                'ukuran_produk' => trim($row['ukuran_produk'] ?? ''),
                'warna_produk' => $warna,
                'kondisi_barang' => trim($row['kondisi_barang'] ?? 'baru'),
                'qty' => $qty,
                'terjual' => $terjual,
                'keluar' => $keluar,
                'sisa_stok' => $sisaStok,
                'harga_gudang' => max(0, (int) ($row['harga_gudang'] ?? 0)),
                'harga_jual' => max(0, (int) ($row['harga_jual'] ?? 0)),
                'margin_kotor' => max(0, (int) ($row['margin_kotor'] ?? 0)),
                'margin_persen' => round((float) ($row['margin_persen'] ?? 0) * 100, 2),
            ];

            $kodeAsli = trim($row['kode_produk'] ?? '');
            $baseCode = Barang::tanpaSuffix($kodeAsli);

            if (isset($existing[$baseCode])) {
                $barang = $existing[$baseCode];
                $barang->update($data);
                $barang->units()->delete();
                $barang->generasiUnitAwal();
                $barang->perbaruiSuffixKode();

                continue;
            }

            try {
                Barang::create(array_merge($data, [
                    'kode_produk' => $baseCode.'-'.Barang::SUFIX_BARU,
                ]));
            } catch (Throwable $e) {
                $this->command?->warn("Baris {$baseCode} dilewati: {$e->getMessage()}");
            }
        }
    }
}
