<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VehicleController;

Route::prefix('v1')->group(function () {
    Route::apiResource('vehicles', VehicleController::class);
});