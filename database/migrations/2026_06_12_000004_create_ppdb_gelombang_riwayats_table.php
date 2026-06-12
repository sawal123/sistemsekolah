<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb_gelombang_riwayats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppdb_gelombang_id')->constrained('ppdb_gelombangs')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('aksi');
            $table->string('status_sebelum')->nullable();
            $table->string('status_sesudah')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('terjadi_pada');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_gelombang_riwayats');
    }
};
