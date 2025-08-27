<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VehicleController;

Route::prefix('v1')->group(function () {
    Route::apiResource('vehicles', VehicleController::class);
});

Route::get('/v1/log/vehicles', [\App\Http\Controllers\Api\LogController::class, 'index']);