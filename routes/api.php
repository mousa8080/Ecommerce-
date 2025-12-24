<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\productController;

Route::middleware(['auth:web','permission:view products,create products,edit products,delete products'])->group(function () {
    Route::apiResource('/products', productController::class);
});

include_once 'auth.php';
