<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn(['offline', 'online']);
            $table->unsignedInteger('keluar')->default(0)->after('terjual');
        });
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn('keluar');
            $table->unsignedInteger('offline')->default(0)->after('qty');
            $table->unsignedInteger('online')->default(0)->after('offline');
        });
    }
};
