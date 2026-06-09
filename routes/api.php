<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\AuthApiController;

// Authentication Routes (Public)
Route::post('/login', [AuthApiController::class, 'getToken']);

// Protected Routes (require token)
Route::middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('/logout', [AuthApiController::class, 'logout']);

    // Product Routes
    Route::get('/product', [ProductApiController::class, 'index']);
    Route::post('/product', [ProductApiController::class, 'store']);
    Route::put('/product/{id}', [ProductApiController::class, 'update']);
    Route::delete('/product/{id}', [ProductApiController::class, 'destroy']);

    // Category Routes
    Route::get('/category', [CategoryApiController::class, 'index']);
    Route::post('/category', [CategoryApiController::class, 'store']);
    Route::put('/category/{id}', [CategoryApiController::class, 'update']);
    Route::delete('/category/{id}', [CategoryApiController::class, 'destroy']);
});

// Public Routes (no authentication required)
Route::get('/product/{id}', [ProductApiController::class, 'show']);
Route::get('/category/{id}', [CategoryApiController::class, 'show']);

// User endpoint
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
