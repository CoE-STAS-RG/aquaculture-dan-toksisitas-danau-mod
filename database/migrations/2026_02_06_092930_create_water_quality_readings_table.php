<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('water_quality_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->float('turbidity_ntu')->nullable();
            $table->float('ec_s_m')->nullable();
            $table->float('tds_ppm')->nullable();
            $table->float('orp_mv')->nullable();
            $table->timestamp('reading_time')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('water_quality_readings');
    }
};