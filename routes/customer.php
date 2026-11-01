<?php

use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CatalogController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\WalletController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'role:customer'])
    ->prefix('customer')->name('customer.')
    ->group(function () {
        Route::get('/dashboard', [WalletController::class, 'dashboard'])->name('dashboard');
        Route::get('/topup', [WalletController::class, 'topupCreate'])->name('topup.create');
        Route::post('/topup', [WalletController::class, 'topupStore'])->name('topup.store');
        Route::get('/topup/riwayat', [WalletController::class, 'topupIndex'])->name('topup.index');

        Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');

        Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
        Route::post('/keranjang/{product}', [CartController::class, 'store'])->name('cart.store');
        Route::patch('/keranjang/{cartItem}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/keranjang/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

        Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');
    });