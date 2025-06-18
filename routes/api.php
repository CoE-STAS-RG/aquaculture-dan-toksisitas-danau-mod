<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SensorDataController;

use App\Http\Controllers\Api\ApiDeviceController;
use App\Http\Controllers\Api\ApiFishFeedingController;
use App\Http\Controllers\Api\ApiRegisterController;
use App\Http\Controllers\Api\ApiLoginController;


Route::post('/register', [ApiRegisterController::class, 'register']);
Route::post('/login', [ApiLoginController::class, 'login']);

Route::post('/sensor-data', [SensorDataController::class, 'store']);
    Route::get('/sensor-data', [SensorDataController::class, 'index']);
    

Route::middleware('auth:sanctum')->group(function () {




   
    
     Route::get('/fish-feedings', [ApiFishFeedingController::class, 'index']);
    Route::post('/fish-feedings', [ApiFishFeedingController::class, 'store']);
    

    Route::get('/devices', [ApiDeviceController::class, 'index']);
    Route::post('/devices', [ApiDeviceController::class, 'store']);
    Route::get('/devices/{device}', [ApiDeviceController::class, 'show']);
    Route::put('/devices/{device}', [ApiDeviceController::class, 'update']);
    Route::delete('/devices/{device}', [ApiDeviceController::class, 'destroy']);
});
