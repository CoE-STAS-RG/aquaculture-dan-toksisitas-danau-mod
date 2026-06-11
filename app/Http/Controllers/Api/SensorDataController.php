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
            'device_code'       => 'required|exists:devices,device_code',
            'env_temperature'   => 'nullable|numeric',
            'water_temperature' => 'nullable|numeric',
            'ph'                => 'nullable|numeric|between:0,14',
            'dissolved_oxygen'  => 'nullable|numeric',
            'risk_level'        => 'nullable|numeric|between:0,100',
            'turbidity_ntu'     => 'nullable|numeric',
            'ec_s_m'            => 'nullable|numeric',
            'tds_ppm'           => 'nullable|numeric',
            'tds_ec_mod'        => 'nullable|numeric',
            'orp_mv'            => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $device = Device::where('device_code', $request->device_code)->first();

        $reading = SensorReading::create([
            'device_id'         => $device->id,
            'env_temperature'   => $request->env_temperature,
            'water_temperature' => $request->water_temperature,
            'ph'                => $request->ph,
            'dissolved_oxygen'  => $request->dissolved_oxygen,
            'risk_level'        => $request->risk_level,
            'turbidity_ntu'     => $request->turbidity_ntu,
            'ec_s_m'            => $request->ec_s_m,
            'tds_ppm'           => $request->tds_ppm,
            'tds_ec_mod'        => $request->tds_ec_mod,
            'orp_mv'            => $request->orp_mv,
            'reading_time'      => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'data'   => $reading,
        ], 201);
    }

    public function index(Request $request)
    {
        if (!$request->device_code) {
            return response()->json([
                'status' => 'error',
                'message' => 'device_code is required',
            ], 422);
        }

        return $this->getByDeviceCode($request->device_code);
    }

    /**
     * Latest reading across all devices — powers the public landing page live card.
     */
    public function latest()
    {
        $reading = SensorReading::with('device:id,name,device_code,location')
            ->orderBy('reading_time', 'desc')
            ->first();

        return response()->json([
            'status' => 'success',
            'data'   => $reading,
        ]);
    }

    public function getByDeviceCode($deviceCode)
    {
        $device = Device::where('device_code', $deviceCode)->first();

        if (!$device) {
            return response()->json([
                'status' => 'error',
                'message' => 'Device not found',
            ], 404);
        }

        $readings = SensorReading::where('device_id', $device->id)
            ->select('id', 'reading_time', 'env_temperature', 'water_temperature', 'ph', 'dissolved_oxygen', 'turbidity_ntu', 'ec_s_m', 'tds_ppm', 'tds_ec_mod', 'orp_mv', 'risk_level')
            ->orderBy('reading_time', 'desc')
            ->limit(100)
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $readings,
        ]);
    }

}
