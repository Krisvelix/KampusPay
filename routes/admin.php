<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MerchantController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'role:admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])
            ->name('users.toggle-active');

        Route::get('/merchants', [MerchantController::class, 'index'])->name('merchants.index');
        Route::patch('/merchants/{merchant}/approve', [MerchantController::class, 'approve'])->name('merchants.approve');
        Route::patch('/merchants/{merchant}/reject', [MerchantController::class, 'reject'])->name('merchants.reject');
    });