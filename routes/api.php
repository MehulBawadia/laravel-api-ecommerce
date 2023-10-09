<?php

use App\Http\Controllers\Api\v1\Admin\BrandsController;
use App\Http\Controllers\Api\v1\Admin\CategoriesController;
use App\Http\Controllers\Api\v1\Admin\GenerateController;
use App\Http\Controllers\Api\v1\Admin\ProductsController;
use App\Http\Controllers\Api\v1\Admin\TagsController;
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

Route::name('v1_admin')->prefix('/v1/admin')->group(function () {
    Route::post('/generate', [GenerateController::class, 'store'])->name('.generate');

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('categories')->name('.categories')->controller(CategoriesController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store')->name('.store');
            Route::get('/{id}', 'show')->name('.show');
            Route::put('/{id}', 'update')->name('.update');
            Route::delete('/{id}', 'destroy')->name('.destroy');
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

        Route::prefix('products')->name('.products')->controller(ProductsController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store')->name('.store');
            Route::get('/{id}', 'show')->name('.show');
            Route::put('/{id}', 'update')->name('.update');
            Route::delete('/{id}', 'destroy')->name('.destroy');
        });
    });
});
