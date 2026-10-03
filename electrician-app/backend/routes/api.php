<?php

use App\Http\Controllers\ElectricianController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'Electrician API is working',
    ]);
});

// Desktop / IP location detection
Route::get('/my-location', [ElectricianController::class, 'myLocation']);

// Nearby electricians from MySQL database
Route::get('/electricians/nearby', [ElectricianController::class, 'nearby']);

// Intelligent geocoding endpoint for Lucknow coordinates
Route::get('/geocode', [ElectricianController::class, 'geocode']);

// Dynamic nearby electricians endpoint
Route::get('/electricians', [ElectricianController::class, 'index']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
