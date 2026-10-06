<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\HouseholdController;
use App\Http\Controllers\Api\IncidentController;
use App\Http\Controllers\Api\ResidentController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:5,1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum', 'active'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/residents/me', [ResidentController::class, 'me']);
    Route::get('/certificates', [CertificateController::class, 'index']);
    Route::post('/certificates', [CertificateController::class, 'store']);
    Route::get('/certificates/{certificate}', [CertificateController::class, 'show']);
    Route::get('/incidents', [IncidentController::class, 'index']);
    Route::post('/incidents', [IncidentController::class, 'store']);
    Route::get('/incidents/{incident}', [IncidentController::class, 'show']);

    Route::middleware('role:admin,staff')->group(function () {
        Route::apiResource('households', HouseholdController::class);
        Route::apiResource('residents', ResidentController::class)->except('me');
        Route::patch('/certificates/{certificate}', [CertificateController::class, 'update']);
        Route::patch('/incidents/{incident}', [IncidentController::class, 'update']);
        Route::get('/dashboard', DashboardController::class);
    });
});
