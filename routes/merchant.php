<?php

use App\Http\Controllers\Merchant\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'role:merchant'])
    ->prefix('merchant')->name('merchant.')
    ->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
    });