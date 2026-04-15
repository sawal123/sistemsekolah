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
        Schema::table('kegiatan_alumnis', function (Blueprint $table) {
            $table->enum('status_verifikasi', ['Verified', 'Pending', 'Rejected'])->default('Verified')->after('deskripsi');
            $table->boolean('is_public')->default(true)->after('status_verifikasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatan_alumnis', function (Blueprint $table) {
            $table->dropColumn(['status_verifikasi', 'is_public']);
        });
    }
};
