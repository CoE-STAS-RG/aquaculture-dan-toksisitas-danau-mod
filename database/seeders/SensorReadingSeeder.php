<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SensorReading;
use Carbon\Carbon;

class SensorReadingSeeder extends Seeder
{
    public function run()
    {
        $deviceId = 1; // GANTI SESUAI KEBUTUHAN

        for ($i = 0; $i < 50; $i++) {
            SensorReading::create([
                'device_id' => $deviceId,
                'env_temperature' => rand(250, 350) / 10,
                'water_temperature' => rand(260, 320) / 10,
                'ph' => rand(65, 85) / 10,
                'dissolved_oxygen' => rand(40, 90) / 10,
                'turbidity_ntu' => rand(0, 100) / 10,
                'ec_s_m' => rand(1, 50) / 10000,
                'tds_ppm' => rand(100, 600),
                'orp_mv' => rand(50, 500),
                'risk_level' => rand(0, 100) / 100,
                'reading_time' => Carbon::now()->subMinutes($i * 5)
            ]);
        }
    }
}