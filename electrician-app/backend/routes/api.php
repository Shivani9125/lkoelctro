<?php

use App\Http\Controllers\AiAgentController;
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

// =============================================================================
// ElectroFix AI Agent Routes
// =============================================================================
Route::post('/ai-agent/chat', [AiAgentController::class, 'chat']);
Route::get('/ai-agent/tools/services', [AiAgentController::class, 'services']);
Route::get('/ai-agent/tools/electricians', [AiAgentController::class, 'electricians']);
Route::post('/ai-agent/tools/create-booking', [AiAgentController::class, 'createBooking']);
Route::get('/ai-agent/tools/booking/{reference}', [AiAgentController::class, 'bookingStatus']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
