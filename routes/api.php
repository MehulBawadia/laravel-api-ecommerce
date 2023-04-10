<?php

use App\Http\Controllers\Api\v1\Admin\AuthController;
use App\Http\Controllers\Api\v1\Admin\GenerateController;
use App\Http\Controllers\Api\v1\Admin\PasswordController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::name('v1_admin')->prefix('/v1/admin')->group(function () {
    Route::post('/generate', [GenerateController::class, 'store'])->name('.generate');

    Route::middleware('guest')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('.login');
        Route::post('/forgot-password', [PasswordController::class, 'sendResetLink'])->name('.forgotPassword');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('.logout');
    });
});
