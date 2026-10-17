<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'role:merchant'])
    ->prefix('merchant')->name('merchant.')
    ->group(function () {
        Route::view('/dashboard', 'merchant.dashboard')->name('dashboard');
    });