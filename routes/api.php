<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Google OAuth routes (public)
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// Email verification routes (public - accessed via email link)
Route::get('/users/{id}/verify', [UserController::class, 'verify'])->name('verification.verify');
Route::post('/users/{id}/resend-verification', [UserController::class, 'resendVerification'])->name('verification.resend');

// Counter routes (public - for getting simple counter list)
Route::get('/counters/list', [CounterController::class, 'listPublic']);

// Queue routes (public - for patients to take queue number and view display)
Route::post('/queues', [QueueController::class, 'store']);
Route::get('/queues/display', [QueueController::class, 'display']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth endpoints
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // User management endpoints
    Route::apiResource('users', UserController::class);

    // Counter management endpoints (protected)
    Route::apiResource('counters', CounterController::class);

    // Queue management endpoints (protected)
    Route::get('/queues', [QueueController::class, 'index']);
    Route::get('/queues/{id}', [QueueController::class, 'show']);
    Route::put('/queues/{id}', [QueueController::class, 'update']);
    Route::patch('/queues/{id}', [QueueController::class, 'update']);

    // Dashboard endpoints (protected)
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard/charts', [DashboardController::class, 'charts']);
    Route::get('/dashboard/status-distribution', [DashboardController::class, 'statusDistribution']);
});
