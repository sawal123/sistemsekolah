<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('nisn')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Kembalikan ke NOT NULL (hati-hati: data null harus diisi dulu)
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('nisn')->nullable(false)->change();
        });
    }
};
