<?php

use App\Http\Controllers\Api\v1\Admin\AccountSettingsController;
use App\Http\Controllers\Api\v1\Admin\AuthController;
use App\Http\Controllers\Api\v1\Admin\BrandsController;
use App\Http\Controllers\Api\v1\Admin\CategoriesController;
use App\Http\Controllers\Api\v1\Admin\GenerateController;
use App\Http\Controllers\Api\v1\Admin\PasswordController;
use App\Http\Controllers\Api\v1\Admin\TagsController;
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
        Route::post('/reset-password', [PasswordController::class, 'reset'])->name('.resetPassword');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('.logout');

        Route::put('/account-settings/general', [AccountSettingsController::class, 'general'])->name('.accountSettings.general');
        Route::put('/account-settings/change-password', [AccountSettingsController::class, 'changePassword'])->name('.accountSettings.changePassword');

        Route::prefix('categories')->name('.categories')->group(function () {
            Route::get('/', [CategoriesController::class, 'index']);
            Route::post('/', [CategoriesController::class, 'store'])->name('.store');
            Route::get('/{id}', [CategoriesController::class, 'show'])->name('.show');
            Route::put('/{id}', [CategoriesController::class, 'update'])->name('.update');
            Route::delete('/{id}', [CategoriesController::class, 'destroy'])->name('.destroy');
        });

        Route::prefix('tags')->name('.tags')->controller(TagsController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store')->name('.store');
            Route::get('/{id}', 'show')->name('.show');
            Route::put('/{id}', 'update')->name('.update');
            Route::delete('/{id}', 'destroy')->name('.destroy');
        });
        Route::prefix('brands')->name('.brands')->controller(BrandsController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store')->name('.store');
            Route::get('/{id}', 'show')->name('.show');
            Route::put('/{id}', 'update')->name('.update');
            Route::delete('/{id}', 'destroy')->name('.destroy');
        });
    });
});
