<?php

use App\Http\Controllers\Api\PassApiController;
use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

// Public signup endpoint
Route::post('/signup', [AccountController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    // Account endpoints
    Route::get('/account', [AccountController::class, 'show']);
    Route::put('/account', [AccountController::class, 'update']);

    // Create pass from template with custom data
    Route::post('/passes', [PassApiController::class, 'store']);

    // Get pass details
    Route::get('/passes/{pass}', [PassApiController::class, 'show']);

    // List user's passes
    Route::get('/passes', [PassApiController::class, 'index']);
});
