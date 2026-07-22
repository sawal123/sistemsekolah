<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftaran_murid_barus', function (Blueprint $table) {
            $table->string('ktp_ayah')->nullable()->after('akta_kelahiran');
            $table->string('ktp_ibu')->nullable()->after('ktp_ayah');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_murid_barus', function (Blueprint $table) {
            $table->dropColumn(['ktp_ayah', 'ktp_ibu']);
        });
    }
};
