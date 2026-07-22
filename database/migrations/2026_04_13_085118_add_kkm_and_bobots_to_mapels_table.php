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
        Schema::table('mapels', function (Blueprint $table) {
            $table->integer('kkm')->default(75)->after('kelompok');
            $table->integer('bobot_harian')->default(40)->after('kkm');
            $table->integer('bobot_pts')->default(30)->after('bobot_harian');
            $table->integer('bobot_pas')->default(30)->after('bobot_pts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mapels', function (Blueprint $table) {
            $table->dropColumn(['kkm', 'bobot_harian', 'bobot_pts', 'bobot_pas']);
        });
    }
};
