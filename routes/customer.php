<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'role:customer'])
    ->prefix('customer')->name('customer.')
    ->group(function () {
        Route::view('/dashboard', 'customer.dashboard')->name('dashboard');
    });