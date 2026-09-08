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
        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->boolean('terjual')->default(false)->after('kembali');
            $table->unsignedBigInteger('terjual_dari')->nullable()->after('kembali_dari');
            $table->foreign('terjual_dari')->references('id')->on('barang_keluars')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->dropForeign(['terjual_dari']);
            $table->dropColumn(['terjual', 'terjual_dari']);
        });
    }
};
