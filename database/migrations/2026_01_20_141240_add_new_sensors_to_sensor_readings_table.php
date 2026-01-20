<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sensor_readings', function (Blueprint $table) {
            // Turbidity: NTU
            $table->float('turbidity_ntu')->nullable()->after('dissolved_oxygen');
            // EC: Siemens per meter (S/m)
            $table->float('ec_s_m')->nullable()->after('turbidity_ntu');
            // TDS: PPM
            $table->float('tds_ppm')->nullable()->after('ec_s_m');
            // ORP: mV
            $table->float('orp_mv')->nullable()->after('tds_ppm');
        });
    }

    public function down(): void
    {
        Schema::table('sensor_readings', function (Blueprint $table) {
            $table->dropColumn([
                'turbidity_ntu',
                'ec_s_m',
                'tds_ppm',
                'orp_mv'
            ]);
        });
    }
};