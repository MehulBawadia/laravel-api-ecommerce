<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\Auth\LoginController;
use App\Http\Controllers\Api\v1\Auth\PasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('guest')->name('auth')->group(function () {
    Route::post('/login', [LoginController::class, 'check'])->name('.login');

    Route::post('/forgot-password', [PasswordController::class, 'sendResetLink'])->name('.forgotPassword');
    Route::post('/reset-password', [PasswordController::class, 'reset'])->name('.resetPassword');
});

Route::middleware('auth:sanctum')->name('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('.logout');
});
