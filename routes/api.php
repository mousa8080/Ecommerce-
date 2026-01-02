<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\productController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\checkoutController;

Route::apiResource('products', productController::class)->only(['index', 'show']);
Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    Route::apiResource('products', productController::class)->except(['index', 'show']);
});
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('cart', CartController::class);
    Route::post('/cart/clear', [CartController::class, 'clearCart']);
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/checkout', [checkoutController::class, 'checkout']);
    Route::get('/orders', [checkoutController::class, 'orderHestory']);
    Route::get('/orders/{orders}', [checkoutController::class, 'orderDetails']);
});

include_once 'auth.php';
