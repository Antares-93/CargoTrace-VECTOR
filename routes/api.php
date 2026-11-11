<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarrierController;
use App\Http\Controllers\Api\ExceptionController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\ShipmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API Routes - CargoTrace Global Visibility Platform
|--------------------------------------------------------------------------
| Complies strictly with UAS Web Programming 2 API Contract (Slide 12-13)
*/

// Public Authentication Endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Authenticated Endpoints (Laravel Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Current user & revocation
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Shipments Resource
    Route::get('/shipments', [ShipmentController::class, 'index']);
    Route::post('/shipments', [ShipmentController::class, 'store']);
    Route::get('/shipments/{id}', [ShipmentController::class, 'show']);
    Route::match(['put', 'patch'], '/shipments/{id}', [ShipmentController::class, 'update']);
    Route::delete('/shipments/{id}', [ShipmentController::class, 'destroy']);

    // Tracking Events
    Route::get('/shipments/{id}/tracking', [ShipmentController::class, 'tracking']);
    Route::post('/shipments/{id}/tracking', [ShipmentController::class, 'addTracking']);

    // Carriers & Locations
    Route::get('/carriers', [CarrierController::class, 'index']);
    Route::post('/carriers', [CarrierController::class, 'store']);
    Route::get('/locations', [LocationController::class, 'index']);
    Route::post('/locations', [LocationController::class, 'store']);

    // Exceptions
    Route::get('/shipments/{id}/exceptions', [ExceptionController::class, 'index']);
    Route::post('/shipments/{id}/exceptions', [ExceptionController::class, 'store']);
    Route::patch('/exceptions/{id}', [ExceptionController::class, 'update']);
});
