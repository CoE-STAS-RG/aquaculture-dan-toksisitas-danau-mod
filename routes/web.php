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
    return view('welcome');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [ProductController::class, 'index'])->name('dashboard');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::resource('users', UserController::class);
    Route::get('/admin/dashboard', [ProductController::class, 'index'])->name('admin.dashboard');
});

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', function () {
        $user = Auth::user();

        // Gunakan eager loading untuk performa lebih baik
        $devices = $user->devices()
            ->with(['readings' => function($q) {
                $q->latest('reading_time')->limit(3);
            }])
            ->limit(20)
            ->get();

        $latestReadings = \App\Models\SensorReading::whereIn('device_id', $devices->pluck('id'))
            ->with('device:id,name,device_code')
            ->latest('reading_time')
            ->paginate(10);

        return view('user.dashboard', compact('devices', 'latestReadings'));
    })->name('user.dashboard');

    Route::get('/user/manajemen-ikan', [FishFeedingController::class, 'index'])->name('index-fish');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/user/manajemen-ikan/tambah', [FishFeedingController::class, 'create'])->name('create-fish');
    Route::post('/user/manajemen-ikan/tambah', [FishFeedingController::class, 'store'])->name('store-fish');

    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::get('/devices/create', [DeviceController::class, 'create'])->name('devices.create');
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
    Route::get('/devices/{deviceId}', [DeviceController::class, 'show'])->name('devices.show');
    Route::get('/devices/{device}/edit', [DeviceController::class, 'edit'])->name('devices.edit');
    Route::put('/devices/{device}', [DeviceController::class, 'update'])->name('devices.update');
    Route::delete('/devices/{device}', [DeviceController::class, 'destroy'])->name('devices.destroy');
});

require __DIR__.'/auth.php';
