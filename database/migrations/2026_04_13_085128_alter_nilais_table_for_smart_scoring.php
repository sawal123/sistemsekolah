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
        Schema::table('nilais', function (Blueprint $table) {
            $table->dropColumn(['jenis_nilai', 'skor']);
            $table->json('nilai_harian')->nullable()->after('tahun_ajaran_id');
            $table->decimal('pts', 8, 2)->nullable()->after('nilai_harian');
            $table->decimal('pas', 8, 2)->nullable()->after('pts');
            $table->decimal('skor_remedial', 8, 2)->nullable()->after('pas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilais', function (Blueprint $table) {
            $table->dropColumn(['nilai_harian', 'pts', 'pas', 'skor_remedial']);
            $table->string('jenis_nilai');
            $table->decimal('skor', 8, 2);
        });
    }
};
