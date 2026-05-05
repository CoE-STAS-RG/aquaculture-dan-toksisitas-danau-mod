<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Requests\StoreDeviceRequest;
use App\Models\SensorReading;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::where('user_id', Auth::id())
            ->select('id', 'device_code', 'name', 'location', 'description', 'created_at')
            ->latest()
            ->get();

        return view('devices.index', compact('devices'));
    }

    public function create()
    {
        return view('devices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
        ]);

        $deviceCode = 'DEV-' . strtoupper(\Illuminate\Support\Str::random(8));

        $device = Device::create([
            'device_code' => $deviceCode,
            'name' => $request->name,
            'user_id' => Auth::id(),
            'description' => $request->description,
            'location' => $request->location,
        ]);

        return redirect()->route('devices.show', $device->id)
                        ->with([
                            'success' => 'Device added successfully!',
                            'device_code' => $deviceCode
                        ]);
    }

public function show($deviceId, Request $request)
{

    $device = Device::where('id', $deviceId)
        ->select('id', 'device_code', 'name', 'location', 'description', 'user_id')
        ->firstOrFail();

    if ($device->user_id !== Auth::id()) {
        abort(403, 'Unauthorized');
    }
        $chartLama = \App\Models\SensorReading::where('device_id', $device->id)
        ->orderBy('reading_time', 'asc')
        ->take(20)
        ->get();

    $chartBaru = \App\Models\WaterQualityReading::where('device_id', $device->id)
        ->orderBy('reading_time', 'asc')
        ->take(20)
        ->get();

    $readingLama = \App\Models\SensorReading::where('device_id', $device->id)
        ->latest('reading_time')
        ->take(10)
        ->get();

    $readingBaru = \App\Models\WaterQualityReading::where('device_id', $device->id)
        ->latest('reading_time')
        ->take(10)
        ->get();

    $latestLama = \App\Models\SensorReading::where('device_id', $device->id)
        ->latest('reading_time')
        ->first();

    $latestBaru = \App\Models\WaterQualityReading::where('device_id', $device->id)
        ->latest('reading_time')
        ->first();

    $latest = (object) array_merge(
        $latestLama?->toArray() ?? [],
        $latestBaru?->toArray() ?? []
    );

    $thresholds = [
        'ph' => ['min' => 6.5, 'max' => 8.5],
        'water_temperature' => ['min' => 20, 'max' => 30],
        'dissolved_oxygen' => ['min' => 4, 'max' => 10],
        'turbidity_ntu' => ['max' => 5],
        'ec_s_m' => ['max' => 0.005],
        'tds_ppm' => ['max' => 500],
        'orp_mv' => ['min' => 100, 'max' => 500],
    ];

    // === NOTIFIKASI: gabung dari dua sumber ===
    $notifications = [];

    // Dari SensorReading (lama)
    $readingsLama = \App\Models\SensorReading::where('device_id', $device->id)
        ->orderBy('reading_time', 'desc')
        ->limit(100)
        ->get();

    foreach ($readingsLama as $r) {
        $timeStr = $r->reading_time->format('d M Y H:i');
        if ($r->ph !== null && ($r->ph < $thresholds['ph']['min'] || $r->ph > $thresholds['ph']['max'])) {
            $notifications[] = "pH berada di luar batas normal ({$r->ph}) pada {$timeStr}";
        }
        if ($r->water_temperature !== null && ($r->water_temperature < $thresholds['water_temperature']['min'] || $r->water_temperature > $thresholds['water_temperature']['max'])) {
            $notifications[] = "Suhu air berada di luar batas normal ({$r->water_temperature}°C) pada {$timeStr}";
        }
        if ($r->dissolved_oxygen !== null && $r->dissolved_oxygen < $thresholds['dissolved_oxygen']['min']) {
            $notifications[] = "Oksigen Terlarut rendah ({$r->dissolved_oxygen} mg/L) pada {$timeStr}";
        }
    }

    // Dari WaterQualityReading (baru)
    $readingsBaru = \App\Models\WaterQualityReading::where('device_id', $device->id)
        ->orderBy('reading_time', 'desc')
        ->limit(100)
        ->get();

    foreach ($readingsBaru as $r) {
        $timeStr = $r->reading_time->format('d M Y H:i');
        if ($r->turbidity_ntu !== null && $r->turbidity_ntu > $thresholds['turbidity_ntu']['max']) {
            $notifications[] = "Turbidity tinggi ({$r->turbidity_ntu} NTU) pada {$timeStr}";
        }
        if ($r->ec_s_m !== null && $r->ec_s_m > $thresholds['ec_s_m']['max']) {
            $notifications[] = "Konduktivitas tinggi ({$r->ec_s_m} S/m) pada {$timeStr}";
        }
        if ($r->tds_ppm !== null && $r->tds_ppm > $thresholds['tds_ppm']['max']) {
            $notifications[] = "TDS tinggi ({$r->tds_ppm} PPM) pada {$timeStr}";
        }
        if ($r->orp_mv !== null && ($r->orp_mv < $thresholds['orp_mv']['min'] || $r->orp_mv > $thresholds['orp_mv']['max'])) {
            $notifications[] = "ORP tidak ideal ({$r->orp_mv} mV) pada {$timeStr}";
        }
    }

    // === DATA UNTUK TAMPILAN (tanpa paginate dulu, karena dua sumber) ===
    $readingLama = \App\Models\SensorReading::where('device_id', $device->id)
        ->orderBy('reading_time', 'desc')
        ->take(10)
        ->get();

    $readingBaru = \App\Models\WaterQualityReading::where('device_id', $device->id)
        ->orderBy('reading_time', 'desc')
        ->take(10)
        ->get();

    // Filter waktu? Bisa ditambahkan nanti per tabel jika perlu

    return view('devices.show', compact(
        'device',
        'readingLama',
        'readingBaru',
        'notifications',
        'latest',
        'device',
        'readingLama',
        'readingBaru',
        'chartLama',
        'chartBaru',
        'notifications'
    ));
}

    public function edit(Device $device)
    {
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }
        return view('devices.edit', compact('device'));
    }

    public function update(Request $request, Device $device)
    {
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
        ]);

        $device->update($request->only(['name', 'description', 'location']));

        return redirect()->route('devices.show', $device->id)
                        ->with('success', 'Device updated successfully!');
    }

    public function destroy(Device $device)
    {
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        $device->delete();

        return redirect()->route('user.dashboard')
                        ->with('success', 'Device deleted successfully!');
    }

    public function latestReadings(Device $device)
    {
        if ($device->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $readings = SensorReading::where('device_id', $device->id)
            ->select('reading_time', 'env_temperature', 'water_temperature', 'ph', 'dissolved_oxygen', 'turbidity_ntu', 'ec_s_m', 'tds_ppm', 'orp_mv', 'risk_level')
            ->orderBy('reading_time', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($reading) {
                return [
                    'time' => $reading->reading_time->format('d-m-Y H:i'),
                    'env_temperature' => $reading->env_temperature,
                    'water_temperature' => $reading->water_temperature,
                    'ph' => $reading->ph,
                    'do' => $reading->dissolved_oxygen,
                    'turbidity_ntu' => $reading->turbidity_ntu,
                    'ec_s_m' => $reading->ec_s_m,
                    'tds_ppm' => $reading->tds_ppm,
                    'orp_mv' => $reading->orp_mv,
                    'risk' => $reading->risk_level
                ];
            });

        return response()->json($readings);
    }
}
