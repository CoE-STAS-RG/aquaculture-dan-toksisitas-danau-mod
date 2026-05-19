<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sensor_readings', function (Blueprint $table) {
            $table->float('tds_ec_mod')->nullable()->after('tds_ppm');
        });

        Schema::table('water_quality_readings', function (Blueprint $table) {
            $table->float('tds_ec_mod')->nullable()->after('tds_ppm');
        });
    }

    public function down(): void
    {
        Schema::table('sensor_readings', function (Blueprint $table) {
            $table->dropColumn('tds_ec_mod');
        });

        Schema::table('water_quality_readings', function (Blueprint $table) {
            $table->dropColumn('tds_ec_mod');
        });
    }
};
