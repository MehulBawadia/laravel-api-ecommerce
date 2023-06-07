<?php

use App\Http\Controllers\Api\v1\Users\Auth\AuthController;
use App\Http\Controllers\Api\v1\Users\Auth\PasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for User
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your user modules. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::name('v1_user')->middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('.login');
    Route::post('/forgot-password', [PasswordController::class, 'sendResetLink'])->name('.forgotPassword');
    Route::post('/reset-password', [PasswordController::class, 'reset'])->name('.resetPassword');
});

Route::middleware('auth:sanctum')->name('v1_user')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('.logout');
});
