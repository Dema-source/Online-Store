<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Guest - Limited access
Route::prefix('guest')->group(function () {
    require __DIR__ . '/apis_by_roles/guest.php';
});

Route::middleware('auth:sanctum')->group(function () {
    // Super Administrator routes - full access
    Route::prefix('admin')->middleware('role:super_administrator')->group(function () {
        require __DIR__ . '/apis_by_roles/super_administrator.php';
    });

    // Customer - Limited access
    Route::prefix('customer')->middleware(['role:customer'])->group(function () {
        require __DIR__ . '/apis_by_roles/customer.php';
    });
});
require __DIR__ . '/auth.php';
