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
    use AuthorizesRequests;
    
    public function index()
    {
        
        $devices = Device::where('user_id', Auth::id())->latest()->get();
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
    
        $deviceCode = 'DEV-' . Str::upper(Str::random(8)); // Format lebih mudah dibaca
    
        $device = Device::create([
            'device_code' => $deviceCode,
            'name' => $request->name,
            'user_id' => Auth::user()->id,
            'description' => $request->description,
            'location' => $request->location,
        ]);
    
        return redirect()->route('devices.show', $device)
                        ->with([
                            'success' => 'Device added successfully!',
                            'device_code' => $deviceCode // Kirim code ke view
                        ]);
    }

    

public function show(Device $device, Request $request)
{
    $this->authorize('view', $device);

    // Ambil latest reading *per device*
    $latest = $device->sensorReadings()->latest('reading_time')->first();

    // Threshold untuk semua parameter
    $thresholds = [
        'ph' => ['min' => 6.5, 'max' => 8.5],
        'water_temperature' => ['min' => 20, 'max' => 30],
        'dissolved_oxygen' => ['min' => 4, 'max' => 10],
        'turbidity_ntu' => ['max' => 5], // NTU ideal < 5 untuk akuakultur
        'ec_s_m' => ['max' => 0.005],    // 5 mS/m = 0.005 S/m
        'tds_ppm' => ['max' => 500],     // umumnya < 500 PPM
        'orp_mv' => ['min' => 100, 'max' => 500], // ORP ideal
    ];

    // Query utama
    $query = $device->sensorReadings()->orderBy('reading_time', 'desc');

    // Filter waktu
    if ($request->has('filter')) {
        $now = Carbon::now();
        switch ($request->filter) {
            case 'daily':
                $query->whereDate('reading_time', $now->toDateString());
                break;
            case 'weekly':
                $query->whereBetween('reading_time', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereMonth('reading_time', $now->month)
                      ->whereYear('reading_time', $now->year);
                break;
            case 'yearly':
                $query->whereYear('reading_time', $now->year);
                break;
        }
    }

    $readings = $query->paginate(10);

    // Notifikasi berdasarkan threshold
    $notifications = [];
    foreach ($readings as $reading) {
        // pH
        if ($reading->ph !== null && ($reading->ph < $thresholds['ph']['min'] || $reading->ph > $thresholds['ph']['max'])) {
            $notifications[] = "pH berada di luar batas normal ({$reading->ph}) pada {$reading->reading_time->format('d M Y H:i')}";
        }

        // Water Temp
        if ($reading->water_temperature !== null && ($reading->water_temperature < $thresholds['water_temperature']['min'] || $reading->water_temperature > $thresholds['water_temperature']['max'])) {
            $notifications[] = "Suhu air berada di luar batas normal ({$reading->water_temperature}°C) pada {$reading->reading_time->format('d M Y H:i')}";
        }

        // DO
        if ($reading->dissolved_oxygen !== null && $reading->dissolved_oxygen < $thresholds['dissolved_oxygen']['min']) {
            $notifications[] = "Oksigen Terlarut rendah ({$reading->dissolved_oxygen} mg/L) pada {$reading->reading_time->format('d M Y H:i')}";
        }

        // Turbidity
        if ($reading->turbidity_ntu !== null && $reading->turbidity_ntu > $thresholds['turbidity_ntu']['max']) {
            $notifications[] = "Turbidity tinggi ({$reading->turbidity_ntu} NTU) pada {$reading->reading_time->format('d M Y H:i')}";
        }

        // EC
        if ($reading->ec_s_m !== null && $reading->ec_s_m > $thresholds['ec_s_m']['max']) {
            $notifications[] = "Konduktivitas tinggi ({$reading->ec_s_m} S/m) pada {$reading->reading_time->format('d M Y H:i')}";
        }

        // TDS
        if ($reading->tds_ppm !== null && $reading->tds_ppm > $thresholds['tds_ppm']['max']) {
            $notifications[] = "TDS tinggi ({$reading->tds_ppm} PPM) pada {$reading->reading_time->format('d M Y H:i')}";
        }

        // ORP
        if ($reading->orp_mv !== null && ($reading->orp_mv < $thresholds['orp_mv']['min'] || $reading->orp_mv > $thresholds['orp_mv']['max'])) {
            $notifications[] = "ORP tidak ideal ({$reading->orp_mv} mV) pada {$reading->reading_time->format('d M Y H:i')}";
        }
    }

    return view('devices.show', compact('device', 'readings', 'notifications', 'latest'));
}


    public function edit(Device $device)
{
    $this->authorize('update', $device);
    return view('devices.edit', compact('device'));
}

public function update(Request $request, Device $device)
{
    $this->authorize('update', $device);

    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'location' => 'nullable|string|max:255',
    ]);

    $device->update([
        'name' => $request->name,
        'description' => $request->description,
        'location' => $request->location,
    ]);

    return redirect()->route('devices.show', $device)
                    ->with('success', 'Device updated successfully!');
}

public function destroy(Device $device)
{
    $this->authorize('delete', $device);

    $device->delete();

    return redirect()->route('user.dashboard')
                    ->with('success', 'Device deleted successfully!');
}

public function latestReadings(Device $device)
{
    $readings = $device->sensorReadings()
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
