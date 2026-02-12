<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('water_quality_readings', function (Blueprint $table) {
            $table->float('risk_level')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('water_quality_readings', function (Blueprint $table) {
            $table->dropColumn('risk_level');
        });
    }
};