<?php

use App\Http\Controllers\Merchant\DashboardController;
use App\Http\Controllers\Merchant\OrderController;
use App\Http\Controllers\Merchant\ProductController;
use App\Http\Controllers\Merchant\StoreController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'role:merchant'])
    ->prefix('merchant')->name('merchant.')
    ->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        // Store registration, profile & management
        Route::get('/store/create', [StoreController::class, 'create'])->name('store.create');
        Route::post('/store', [StoreController::class, 'store'])->name('store.store');
        Route::get('/store', [StoreController::class, 'show'])->name('store.show');
        Route::get('/store/edit', [StoreController::class, 'edit'])->name('store.edit');
        Route::patch('/store', [StoreController::class, 'update'])->name('store.update');

        // Products CRUD & Stock
        Route::resource('products', ProductController::class);
        Route::patch('/products/{product}/toggle-available', [ProductController::class, 'toggleAvailable'])->name('products.toggle-available');
        Route::patch('/products/{product}/stock', [ProductController::class, 'updateStock'])->name('products.update-stock');

        // Incoming Orders / Transactions
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    });