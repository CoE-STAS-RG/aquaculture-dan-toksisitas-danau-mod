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
            'temperature' => 'required|numeric',
            'ph' => 'required|numeric|between:0,14',
            'dissolved_oxygen' => 'required|numeric',
            'risk_level' => 'required|numeric|between:0,100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid data',
                'errors' => $validator->errors()
            ], 422);
        }

        $device = Device::where('device_code', $request->device_code)->first();

        $reading = SensorReading::create([
            'device_id' => $device->id,
            'temperature' => $request->temperature,
            'ph' => $request->ph,
            'dissolved_oxygen' => $request->dissolved_oxygen,
            'risk_level' => $request->risk_level,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data saved successfully',
            'data' => $reading
        ], 201);

        if (!$device) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid device code'
            ], 404);
        }
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
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $readings
    ]);
}

}