<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\SensorReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SensorDataController extends Controller
{
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'device_code' => 'required|string|exists:devices,device_code',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => 'Invalid device_code'], 400);
    }

    $device = Device::where('device_code', $request->device_code)->first();

    // Simpan data dasar
    $basic = SensorReading::create([
        'device_id' => $device->id,
        'env_temperature' => $request['env_temperature'],
        'water_temperature' => $request['water_temperature'],
        'ph' => $request['ph'],
        'dissolved_oxygen' => $request['dissolved_oxygen'],
        'risk_level' => $request['risk_level'] ?? null,
        'reading_time' => now(),
    ]);

    // Simpan data kualitas air (sensor baru)
    $quality = WaterQualityReading::create([
        'device_id' => $device->id,
        'turbidity_ntu' => $request['turbidity_ntu'],
        'ec_s_m' => $request['ec_s_m'],
        'tds_ppm' => $request['tds_ppm'],
        'orp_mv' => $request['orp_mv'],
        'reading_time' => now(), // atau sync timestamp dari basic
    ]);

    return response()->json([
        'status' => 'success',
        'basic_id' => $basic->id,
        'quality_id' => $quality->id
    ], 201);
}

    public function index(Request $request)
    {
        $device = Device::where('device_code', $request->device_code)->first();

        if (!$device) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid device code'
            ], 404);
        }

        $readings = SensorReading::where('device_id', $device->id)
            ->select('id', 'reading_time', 'env_temperature', 'water_temperature', 'ph', 'dissolved_oxygen', 'turbidity_ntu', 'ec_s_m', 'tds_ppm', 'orp_mv', 'risk_level')
            ->orderBy('reading_time', 'desc')
            ->limit(100)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $readings
        ]);
    }

    public function getByDeviceCode($deviceCode)
    {
        $device = Device::where('device_code', $deviceCode)->first();

        if (!$device) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid device code'
            ], 404);
        }

        $readings = SensorReading::where('device_id', $device->id)
            ->select('id', 'reading_time', 'env_temperature', 'water_temperature', 'ph', 'dissolved_oxygen', 'turbidity_ntu', 'ec_s_m', 'tds_ppm', 'orp_mv', 'risk_level')
            ->orderBy('reading_time', 'desc')
            ->limit(100)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $readings
        ]);
    }

}
