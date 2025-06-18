<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Policies\DevicePolicy;
use Illuminate\Support\Facades\Route;
use App\Models\SensorReading;
use Illuminate\Support\Facades\View;
use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
class AppServiceProvider extends ServiceProvider
{

    protected $policies = [
        Device::class => DevicePolicy::class,
    ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::middleware('api')
        ->prefix('api')
        ->group(base_path('routes/api.php'));

    Route::middleware('web')
        ->group(base_path('routes/web.php'));
         View::composer('layouts.navigation', function ($view) {
        // Anggap ambil device pertama (atau ganti sesuai logic)
        $device = Device::first(); 
        if (!$device) return;

        $readings = $device->readings()->orderBy('reading_time', 'desc')->limit(100)->get();

        $thresholds = [
            'ph' => ['min' => 6.5, 'max' => 8.5],
            'temperature' => ['min' => 20, 'max' => 30],
            'dissolved_oxygen' => ['min' => 4, 'max' => 10],
        ];

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

        $latestNotifications = array_slice($notifications, -5);

        $view->with('latestNotifications', $latestNotifications);
    });
    Paginator::useTailwind();
    }
    
}
