<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ApiDeviceController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $devices = Device::where('user_id', Auth::id())->latest()->get();
        return response()->json(['devices' => $devices]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
        ]);

        $deviceCode = 'DEV-' . Str::upper(Str::random(8));

        $device = Device::create([
            'device_code' => $deviceCode,
            'name' => $request->name,
            'user_id' => Auth::id(),
            'description' => $request->description,
            'location' => $request->location,
        ]);

        return response()->json([
            'message' => 'Device created successfully.',
            'device' => $device,
            'device_code' => $deviceCode
        ], 201);
    }

    public function show(Device $device, Request $request)
    {
        $this->authorize('view', $device);

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
                    $query->whereMonth('reading_time', $now->month)->whereYear('reading_time', $now->year);
                    break;
                case 'yearly':
                    $query->whereYear('reading_time', $now->year);
                    break;
            }
        }

        $readings = $query->limit(100)->get();

        return response()->json([
            'device' => $device,
            'readings' => $readings,
        ]);
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

        return response()->json([
            'message' => 'Device updated successfully.',
            'device' => $device,
        ]);
    }

    public function destroy(Device $device)
    {
        $this->authorize('delete', $device);

        $device->delete();

        return response()->json([
            'message' => 'Device deleted successfully.'
        ]);
    }
    public function latestReadings(Device $device)
{
    $readings = $device->readings()
        ->orderBy('reading_time', 'desc')
        ->limit(10) // ambil 10 data terbaru
        ->get()
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
