<?php

use App\Http\Controllers\Api\v1\Auth\LoginController;
use App\Http\Controllers\Api\v1\Auth\PasswordController;
use App\Http\Controllers\Api\v1\Auth\RegisterController;
use App\Http\Controllers\Api\v1\Settings\AccountSettingsController;
use Illuminate\Support\Facades\Route;

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
    Route::post('/register', [RegisterController::class, 'store'])->name('.register');
    Route::post('/login', [LoginController::class, 'check'])->name('.login');

    Route::post('/forgot-password', [PasswordController::class, 'sendResetLink'])->name('.forgotPassword');
    Route::post('/reset-password', [PasswordController::class, 'reset'])->name('.resetPassword');
});

Route::middleware('auth:sanctum')->name('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('.logout');

    Route::put('/account-settings/general', [AccountSettingsController::class, 'general'])->name('.accountSettings.general');
    Route::put('/account-settings/change-password', [AccountSettingsController::class, 'changePassword'])->name('.accountSettings.changePassword');
});
