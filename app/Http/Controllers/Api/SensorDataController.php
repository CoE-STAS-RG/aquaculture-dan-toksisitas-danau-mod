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
            'device_code' => 'required|exists:devices,device_code',
            'suhuDHT' => 'required|numeric',
            'suhu' => 'required|numeric',
            'ph' => 'required|numeric|between:0,14',
            'do' => 'required|numeric',
            'risiko' => 'required|numeric|between:0,100',
            'turbidity_ntu' => 'nullable|numeric',
            'ec_s_m' => 'nullable|numeric',
            'tds_ppm' => 'nullable|numeric',
            'orp_mv' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid data',
                'errors' => $validator->errors()
            ], 422);
        }

        $device = Device::where('device_code', $request->device_code)->first();

        if (!$device) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid device code'
            ], 404);
        }

        $reading = SensorReading::create([
            'device_id' => $device->id,
            'env_temperature' => $request->suhuDHT,
            'water_temperature' => $request->suhu,
            'ph' => $request->ph,
            'dissolved_oxygen' => $request->do,
            'risk_level' => $request->risiko,
            'turbidity_ntu' => $request->turbidity_ntu,
            'ec_s_m' => $request->ec_s_m,
            'tds_ppm' => $request->tds_ppm,
            'orp_mv' => $request->orp_mv,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data saved successfully',
            'data' => $reading
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
