<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk')->unique();
            $table->string('jenis_barang');
            $table->string('merk_produk');
            $table->string('ukuran_produk');
            $table->string('warna_produk');
            $table->string('kondisi_barang')->default('baru');
            $table->unsignedInteger('qty')->default(0);
            $table->unsignedInteger('offline')->default(0);
            $table->unsignedInteger('online')->default(0);
            $table->unsignedInteger('terjual')->default(0);
            $table->unsignedInteger('sisa_stok')->default(0);
            $table->unsignedInteger('harga_gudang')->default(0);
            $table->unsignedInteger('harga_jual')->default(0);
            $table->unsignedInteger('margin_kotor')->default(0);
            $table->decimal('margin_persen', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
