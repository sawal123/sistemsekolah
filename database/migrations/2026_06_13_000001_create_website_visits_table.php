<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_visits', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->nullable();
            $table->string('method', 10);
            $table->string('path', 500);
            $table->text('full_url')->nullable();
            $table->text('referer')->nullable();
            $table->text('user_agent')->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->boolean('is_suspicious')->default(false);
            $table->string('threat_type')->nullable();
            $table->text('threat_reason')->nullable();
            $table->timestamp('visited_at')->index();
            $table->timestamps();

            $table->index(['is_suspicious', 'visited_at']);
            $table->index(['ip_address', 'visited_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_visits');
    }
};
