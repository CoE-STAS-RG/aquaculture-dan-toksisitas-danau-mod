<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Models\Product;
use App\Models\User;
use App\Http\Controllers\UserController;

use App\Http\Controllers\FishFeedingController;
use App\Http\Controllers\DeviceController;
use App\Models\Device;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    // return "hello";
    return view('welcome');
});

Route::get('user/dashboard', function () {
    return view('user.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth','role:admin')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [ProductController::class, 'index'])->name('dashboard');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Tambahkan route untuk user management
    Route::resource('users', UserController::class);
    Route::get('/admin/dashboard', [ProductController::class, 'index'])->name('admin.dashboard');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [ProductController::class, 'index'])->name('admin.dashboard');
    // route admin lainnya
});

Route::middleware(['auth', 'role:user'])->group(function () {
    // Dashboard User
    Route::get('/user/dashboard', function () {
        $user = Auth::user();
        $devices = $user->devices()->with(['readings' => function($query) {
            $query->latest()->limit(5);
        }])->get();
        
        $latestReadings = $user->sensorReadings()
                              ->with('device')
                              ->latest()
                              ->take(5)
                              ->get();
        
        return view('user.dashboard', [
            'devices' => $devices,
            'latestReadings' => $latestReadings
        ]);
    })->name('user.dashboard');

    // Add these routes
    Route::get('/recent-readings', function() {
        $readings = auth()->user()->sensorReadings()
            ->with('device')
            ->latest()
            ->take(5)
            ->get();
            
        return view('components.recent-readings-table', compact('readings'));
    })->name('sensor-readings.recent');

    Route::get('/devices/list', function() {
        $devices = auth()->user()->devices()->with('readings')->get();
        return view('components.device-list-table', compact('devices'));
    })->name('devices.list');


    Route::get('/user/manajemen-ikan', [FishFeedingController::class, 'index'])->name('index-fish');
    Route::get('/user/manajemen-ikan/tambah', [FishFeedingController::class, 'create'])->name('create-fish'); // Tambahkan ini
    Route::post('/user/manajemen-ikan/tambah', [FishFeedingController::class, 'store'])->name('store-fish');

    
    // Device Management
    Route::resource('devices', DeviceController::class);
    
    
});



require __DIR__.'/auth.php';
