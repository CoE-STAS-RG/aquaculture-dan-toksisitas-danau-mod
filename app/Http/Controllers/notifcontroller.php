<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

class notifcontroller extends Controller
{
    use AuthorizesRequests;

    public function show(Device $device, Request $request)
    {
        try {
            $this->authorize('view', $device);

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

            $readings = $query->limit(100)->get();

            // Generate notifications
            $notifications = [];
            foreach ($readings as $reading) {
                if ($reading->ph !== null && ($reading->ph < $thresholds['ph']['min'] || $reading->ph > $thresholds['ph']['max'])) {
                    $notifications[] = "PH berada di luar batas normal ({$reading->ph}) pada {$reading->reading_time}";
                }
                if ($reading->temperature !== null && ($reading->temperature < $thresholds['temperature']['min'] || $reading->temperature > $thresholds['temperature']['max'])) {
                    $notifications[] = "Suhu berada di luar batas normal ({$reading->temperature}°C) pada {$reading->reading_time}";
                }
                if ($reading->dissolved_oxygen !== null && $reading->dissolved_oxygen < $thresholds['dissolved_oxygen']['min']) {
                    $notifications[] = "Oksigen Terlarut rendah ({$reading->dissolved_oxygen} mg/L) pada {$reading->reading_time}";
                }
            }

            $latestNotifications = array_slice($notifications, -5);

            return view('layouts.navigation', compact('device', 'readings', 'notifications','latestNotifications'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('user.dashboard')->with('error', 'You are not authorized to view this device.');
        } catch (\Exception $e) {
            \Log::error('Error showing notifications: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load notifications.');
        }
    }
}
