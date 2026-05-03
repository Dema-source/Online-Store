<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    // Public routes
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('guest')
        ->name('login');

    Route::post('/register/customer', [AuthController::class, 'registerCustomer'])
        ->middleware('guest')
        ->name('register.customer');

    Route::post('/register/admin', [AuthController::class, 'registerAdmin'])
        ->middleware(['auth:sanctum', 'role:super_administrator']) // Only super administrators can register new admins
        ->name('register.admin');

    Route::post('/forgot-password', [PasswordController::class, 'forgotPassword'])
        ->middleware('guest')
        ->name('password.email');

    Route::post('/reset-password', [PasswordController::class, 'resetPassword'])
        ->middleware('guest')
        ->name('password.store');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('logout');

        Route::post('/refresh', [AuthController::class, 'refresh'])
            ->name('refresh');

        Route::get('/profile', [AuthController::class, 'profile'])
            ->name('profile');

        Route::post('/email/verification-notification', [AuthController::class, 'sendEmailVerification'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

        Route::post('/verify-email', [AuthController::class, 'verifyEmail'])
            ->middleware('throttle:6,1')
            ->name('verification.verify');
    });
});
