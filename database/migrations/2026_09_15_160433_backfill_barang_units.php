<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $barangs = DB::table('barangs')
            ->where('qty', '>', 0)
            ->get();

        foreach ($barangs as $barang) {
            $qty = (int) $barang->qty;
            $terjual = (int) $barang->terjual;
            $keluarNow = (int) $barang->keluar;

            $pernahKeluarUnit = (int) DB::table('barang_keluars')
                ->where('barang_id', $barang->id)
                ->whereNull('kembali_dari')
                ->whereNull('terjual_dari')
                ->sum('jumlah');

            $returned = max(0, $pernahKeluarUnit - $terjual - $keluarNow);
            $belum = max(0, $qty - $pernahKeluarUnit);

            $rows = [];
            $n = 1;

            for ($i = 0; $i < $terjual; $i++) {
                $rows[] = [
                    'barang_id' => $barang->id,
                    'nomor_urut' => $n++,
                    'pernah_keluar' => true,
                    'status' => 'terjual',
                    'created_at' => $barang->created_at,
                    'updated_at' => $barang->updated_at,
                ];
            }

            for ($i = 0; $i < $keluarNow; $i++) {
                $rows[] = [
                    'barang_id' => $barang->id,
                    'nomor_urut' => $n++,
                    'pernah_keluar' => true,
                    'status' => 'keluar',
                    'created_at' => $barang->created_at,
                    'updated_at' => $barang->updated_at,
                ];
            }

            for ($i = 0; $i < $returned; $i++) {
                $rows[] = [
                    'barang_id' => $barang->id,
                    'nomor_urut' => $n++,
                    'pernah_keluar' => true,
                    'status' => 'di_gudang',
                    'created_at' => $barang->created_at,
                    'updated_at' => $barang->updated_at,
                ];
            }

            for ($i = 0; $i < $belum; $i++) {
                $rows[] = [
                    'barang_id' => $barang->id,
                    'nomor_urut' => $n++,
                    'pernah_keluar' => false,
                    'status' => 'di_gudang',
                    'created_at' => $barang->created_at,
                    'updated_at' => $barang->updated_at,
                ];
            }

            if (! empty($rows)) {
                DB::table('barang_units')->insert($rows);
            }
        }
    }

    public function down(): void
    {
        DB::table('barang_units')->truncate();
    }
};
