<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_popups', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 150);
            $table->string('gambar');
            $table->string('link', 2048);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('website_popup_impressions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_popup_id')->constrained()->cascadeOnDelete();
            $table->char('visitor_key', 64);
            $table->char('ip_hash', 64);
            $table->char('device_hash', 64);
            $table->timestamp('displayed_at');
            $table->unique(['website_popup_id', 'visitor_key'], 'website_popup_visitor_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_popup_impressions');
        Schema::dropIfExists('website_popups');
    }
};
