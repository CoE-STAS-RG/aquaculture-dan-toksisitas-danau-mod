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
     $latest = SensorReading::latest()->first();

    $thresholds = [
        'ph' => ['min' => 6.5, 'max' => 8.5],
        'temperature' => ['min' => 20, 'max' => 30],
        'dissolved_oxygen' => ['min' => 4, 'max' => 10],
    ];

    $query = $device->readings()->orderBy('reading_time', 'desc');

    if ($request->has('filter')) {
        $now = Carbon::now();

        switch ($request->filter) {
            case 'daily':
                $query->whereDate('reading_time', $now->toDateString());
                break;
            case 'weekly':
                $query->whereBetween('reading_time', [$now->startOfWeek(), $now->endOfWeek()]);
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

    $readings = $query->paginate(10);// <-- Pastikan ini dijalankan dulu
    // dd($device);
    // dd($readings);
    $readings->withPath('/api/sensor-data?device_code=' . $device->device_code);



    // Pindahkan foreach setelah $readings didefinisikan
    $notifications = [];

    foreach ($readings as $reading) {
        if ($reading->ph < $thresholds['ph']['min'] || $reading->ph > $thresholds['ph']['max']) {
            $notifications[] = "PH berada di luar batas normal ({$reading->ph}) pada {$reading->reading_time}";
        }
        if ($reading->temperature < $thresholds['temperature']['min'] || $reading->temperature > $thresholds['temperature']['max']) {
            $notifications[] = "Suhu berada di luar batas normal ({$reading->temperature}°C) pada {$reading->reading_time}";
        }
        if ($reading->dissolved_oxygen < $thresholds['dissolved_oxygen']['min']) {
            $notifications[] = "Oksigen Terlarut rendah ({$reading->dissolved_oxygen} mg/L) pada {$reading->reading_time}";
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
    $readings = $device->readings()
        ->orderBy('reading_time', 'desc')
        ->limit(10) // ambil 10 data terbaru
        ->get()
        ->paginate(50)
        ->map(function ($reading) {
            return [
                'time' => $reading->reading_time->format('d-m-Y H:i'),
                'temperature' => $reading->temperature,
                'ph' => $reading->ph,
                'do' => $reading->dissolved_oxygen,
                'risk' => $reading->risk_level
            ];
        });

    return response()->json($readings);
}


}
