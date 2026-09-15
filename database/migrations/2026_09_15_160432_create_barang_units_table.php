<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('nomor_urut');
            $table->boolean('pernah_keluar')->default(false);
            $table->string('status', 20)->default('di_gudang');
            $table->timestamps();

            $table->unique(['barang_id', 'nomor_urut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_units');
    }
};
