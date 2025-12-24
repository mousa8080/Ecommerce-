<?php

use App\Http\Controllers\Api\Auth\AuthAdminController;
use App\Http\Controllers\Api\Auth\AuthCustomerController;
use App\Http\Controllers\Api\Auth\AuthDeleveryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::post('/register', [AuthAdminController::class, 'register']);
    Route::post('/login', [AuthAdminController::class, 'login']);
    Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
        Route::post('/logout', [AuthAdminController::class, 'logout']);
        Route::get('/me', [AuthAdminController::class, 'me']);
        Route::get('/token', [AuthAdminController::class, 'accsessToken']);
    });
});
Route::prefix('customer')->group(function () {
    Route::post('/register', [AuthCustomerController::class, 'register']);
    Route::post('/login', [AuthCustomerController::class, 'login']);
    Route::middleware(['auth:sanctum', 'isCustomer'])->group(function () {
        Route::post('/logout', [AuthCustomerController::class, 'logout']);
        Route::get('/me', [AuthCustomerController::class, 'me']);
        Route::get('/token', [AuthCustomerController::class, 'accsessToken']);
    });
});
Route::prefix('delevery')->group(function () {
    Route::post('/register', [AuthDeleveryController::class, 'register']);
    Route::post('/login', [AuthDeleveryController::class, 'login']);
    Route::middleware(['auth:sanctum', 'isDelevery'])->group(function () {
        Route::post('/logout', [AuthDeleveryController::class, 'logout']);
        Route::get('/me', [AuthDeleveryController::class, 'me']);
        Route::get('/token', [AuthDeleveryController::class, 'accsessToken']);
    });
});
