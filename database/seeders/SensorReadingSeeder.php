<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SensorReading;
use App\Models\WaterQualityReading;
use Carbon\Carbon;

class SensorReadingSeeder extends Seeder
{
    public function run()
    {
        $deviceId = 1; // GANTI SESUAI DEVICE_ID YANG ADA

        // === 25 Data ESP A → SensorReading (lama) ===
        for ($i = 0; $i < 25; $i++) {
            SensorReading::create([
                'device_id' => $deviceId,
                'water_temperature' => rand(260, 320) / 10,   // 26.0–32.0
                'ph' => rand(65, 85) / 10,                    // 6.5–8.5
                'dissolved_oxygen' => rand(40, 90) / 10,      // 4.0–9.0
                'risk_level' => rand(0, 100) / 100,
                'reading_time' => Carbon::now()->subMinutes($i * 5)
            ]);
        }

        // === 25 Data ESP B → WaterQualityReading (baru) ===
        for ($i = 0; $i < 25; $i++) {
            WaterQualityReading::create([
                'device_id' => $deviceId,
                'turbidity_ntu' => rand(0, 100) / 10,         // 0.0–10.0
                'ec_s_m' => rand(1, 50) / 10000,              // 0.0001–0.005
                'tds_ppm' => rand(100, 600),                  // 100–600
                'orp_mv' => rand(50, 500),                    // 50–500
                'risk_level' => rand(0, 100) / 100,
                'reading_time' => Carbon::now()->subMinutes($i * 5 + 2)
            ]);
        }

        $this->command->info('✅ 50 dummy readings created: 25 (ESP A) + 25 (ESP B)');
    }
}