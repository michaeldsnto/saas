<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanySettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('verified')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::get('/company/settings', [CompanySettingsController::class, 'edit'])->middleware('owner')->name('company.settings.edit');
        Route::put('/company/settings', [CompanySettingsController::class, 'update'])->middleware('owner')->name('company.settings.update');
        Route::get('/company/users', [UserManagementController::class, 'index'])->middleware('owner')->name('company.users.index');
        Route::post('/company/users', [UserManagementController::class, 'store'])->middleware('owner')->name('company.users.store');

        Route::get('/inventory/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/inventory/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/inventory/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/inventory/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/inventory/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::post('/inventory/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::put('/inventory/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/inventory/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

        Route::get('/inventory/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
        Route::post('/inventory/warehouses', [WarehouseController::class, 'store'])->name('warehouses.store');
        Route::put('/inventory/warehouses/{warehouse}', [WarehouseController::class, 'update'])->name('warehouses.update');
        Route::delete('/inventory/warehouses/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');

        Route::get('/inventory/products', [ProductController::class, 'index'])->name('products.index');
        Route::post('/inventory/products', [ProductController::class, 'store'])->name('products.store');
        Route::put('/inventory/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/inventory/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/inventory/products/import', [ProductController::class, 'import'])->name('products.import');
        Route::get('/reports/inventory.xlsx', [ProductController::class, 'exportExcel'])->name('products.export.excel');
        Route::get('/reports/inventory.pdf', [ProductController::class, 'exportPdf'])->name('products.export.pdf');

        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');

        Route::get('/billing', [SubscriptionController::class, 'index'])->middleware('owner')->name('billing.index');
        Route::post('/billing/checkout', [SubscriptionController::class, 'checkout'])->middleware('owner')->name('billing.checkout');

        Route::get('/audit-logs', [AuditLogController::class, 'index'])->middleware('owner')->name('audit.index');
    });
});

require __DIR__ . '/auth.php';
