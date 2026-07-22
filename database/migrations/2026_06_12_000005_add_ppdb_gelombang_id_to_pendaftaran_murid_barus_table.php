<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftaran_murid_barus', function (Blueprint $table) {
            $table->foreignId('ppdb_gelombang_id')
                ->nullable()
                ->after('id')
                ->constrained('ppdb_gelombangs')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_murid_barus', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ppdb_gelombang_id');
        });
    }
};
