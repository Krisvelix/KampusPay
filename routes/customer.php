<?php

use App\Http\Controllers\Customer\WalletController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'role:customer'])
    ->prefix('customer')->name('customer.')
    ->group(function () {
        Route::get('/dashboard', [WalletController::class, 'dashboard'])->name('dashboard');
        Route::get('/topup', [WalletController::class, 'topupCreate'])->name('topup.create');
        Route::post('/topup', [WalletController::class, 'topupStore'])->name('topup.store');
        Route::get('/topup/riwayat', [WalletController::class, 'topupIndex'])->name('topup.index');
    });