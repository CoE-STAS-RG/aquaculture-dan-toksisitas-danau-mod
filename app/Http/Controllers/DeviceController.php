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
        // Hanya ambil kolom yang dibutuhkan
        $device = Device::where('id', $deviceId)
            ->select('id', 'device_code', 'name', 'location', 'description', 'user_id')
            ->firstOrFail();

        // Pastikan user memiliki akses
        if ($device->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Ambil latest reading terpisah
        $latest = SensorReading::where('device_id', $device->id)
            ->select('reading_time', 'ph', 'water_temperature', 'dissolved_oxygen', 'turbidity_ntu', 'ec_s_m', 'tds_ppm', 'orp_mv', 'risk_level')
            ->latest('reading_time')
            ->first();

        // Threshold parameter
        $thresholds = [
            'ph' => ['min' => 6.5, 'max' => 8.5],
            'water_temperature' => ['min' => 20, 'max' => 30],
            'dissolved_oxygen' => ['min' => 4, 'max' => 10],
            'turbidity_ntu' => ['max' => 5],
            'ec_s_m' => ['max' => 0.005],
            'tds_ppm' => ['max' => 500],
            'orp_mv' => ['min' => 100, 'max' => 500],
        ];

        // === 1. Query untuk NOTIFIKASI (batasi maks 100 data terbaru) ===
        $notificationReadings = SensorReading::where('device_id', $device->id)
            ->select('reading_time', 'ph', 'water_temperature', 'dissolved_oxygen', 'turbidity_ntu', 'ec_s_m', 'tds_ppm', 'orp_mv')
            ->orderBy('reading_time', 'desc')
            ->limit(100) // Batasi agar tidak kehabisan memori
            ->get();

        $notifications = [];
        foreach ($notificationReadings as $reading) {
            $timeStr = $reading->reading_time->format('d M Y H:i');

            if ($reading->ph !== null && ($reading->ph < $thresholds['ph']['min'] || $reading->ph > $thresholds['ph']['max'])) {
                $notifications[] = "pH berada di luar batas normal ({$reading->ph}) pada {$timeStr}";
            }

            if ($reading->water_temperature !== null && ($reading->water_temperature < $thresholds['water_temperature']['min'] || $reading->water_temperature > $thresholds['water_temperature']['max'])) {
                $notifications[] = "Suhu air berada di luar batas normal ({$reading->water_temperature}°C) pada {$timeStr}";
            }

            if ($reading->dissolved_oxygen !== null && $reading->dissolved_oxygen < $thresholds['dissolved_oxygen']['min']) {
                $notifications[] = "Oksigen Terlarut rendah ({$reading->dissolved_oxygen} mg/L) pada {$timeStr}";
            }

            if ($reading->turbidity_ntu !== null && $reading->turbidity_ntu > $thresholds['turbidity_ntu']['max']) {
                $notifications[] = "Turbidity tinggi ({$reading->turbidity_ntu} NTU) pada {$timeStr}";
            }

            if ($reading->ec_s_m !== null && $reading->ec_s_m > $thresholds['ec_s_m']['max']) {
                $notifications[] = "Konduktivitas tinggi ({$reading->ec_s_m} S/m) pada {$timeStr}";
            }

            if ($reading->tds_ppm !== null && $reading->tds_ppm > $thresholds['tds_ppm']['max']) {
                $notifications[] = "TDS tinggi ({$reading->tds_ppm} PPM) pada {$timeStr}";
            }

            if ($reading->orp_mv !== null && ($reading->orp_mv < $thresholds['orp_mv']['min'] || $reading->orp_mv > $thresholds['orp_mv']['max'])) {
                $notifications[] = "ORP tidak ideal ({$reading->orp_mv} mV) pada {$timeStr}";
            }
        }

        // === 2. Query untuk TAMPILAN (gunakan paginate) ===
        $displayQuery = SensorReading::where('device_id', $device->id)
            ->select('id', 'reading_time', 'ph', 'water_temperature', 'dissolved_oxygen', 'turbidity_ntu', 'ec_s_m', 'tds_ppm', 'orp_mv', 'risk_level')
            ->orderBy('reading_time', 'desc');

        // Filter waktu
        if ($request->filled('filter')) {
            $now = Carbon::now();
            switch ($request->filter) {
                case 'daily':
                    $displayQuery->whereDate('reading_time', $now->toDateString());
                    break;
                case 'weekly':
                    $displayQuery->whereBetween('reading_time', [
                        $now->copy()->startOfWeek(),
                        $now->copy()->endOfWeek()
                    ]);
                    break;
                case 'monthly':
                    $displayQuery->whereMonth('reading_time', $now->month)
                                 ->whereYear('reading_time', $now->year);
                    break;
                case 'yearly':
                    $displayQuery->whereYear('reading_time', $now->year);
                    break;
            }
        }

        $readings = $displayQuery->paginate(10);

        return view('devices.show', compact('device', 'readings', 'notifications', 'latest'));
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
