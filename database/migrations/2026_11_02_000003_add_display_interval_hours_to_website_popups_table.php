<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_popups', function (Blueprint $table) {
            $table->unsignedSmallInteger('display_interval_hours')->default(24)->after('link');
        });
    }

    public function down(): void
    {
        Schema::table('website_popups', function (Blueprint $table) {
            $table->dropColumn('display_interval_hours');
        });
    }
};
