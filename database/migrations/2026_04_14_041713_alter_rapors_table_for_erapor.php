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
        Schema::table('rapors', function (Blueprint $table) {
            $table->json('ekskul')->nullable()->after('catatan_wali_kelas');
            $table->json('prestasi')->nullable()->after('ekskul');
            $table->json('karakter')->nullable()->after('prestasi');
            $table->boolean('is_locked')->default(false)->after('keputusan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rapors', function (Blueprint $table) {
            $table->dropColumn(['ekskul', 'prestasi', 'karakter', 'is_locked']);
        });
    }
};
