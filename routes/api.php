<?php

use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\TransactionApiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'tenant'])->group(function () {
    Route::get('/dashboard', DashboardApiController::class)->name('api.dashboard');
    Route::apiResource('products', ProductApiController::class);
    Route::get('/transactions', [TransactionApiController::class, 'index'])->name('api.transactions.index');
    Route::post('/transactions', [TransactionApiController::class, 'store'])->name('api.transactions.store');
});
