<?php

use App\Http\Controllers\Api\v1\Users\BillingAddressController;
use App\Http\Controllers\Api\v1\Users\ShippingAddressController;
use App\Http\Controllers\Api\v1\Users\WishlistController;
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

Route::middleware('auth:sanctum')->name('v1_user')->group(function () {
    Route::name('.billingAddress')->prefix('billing-address')->controller(BillingAddressController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/store', 'store')->name('.store');
        Route::put('/{id}', 'update')->name('.update');
        Route::delete('/{id}', 'destroy')->name('.destroy');
    });

    Route::name('.shippingAddress')->prefix('shipping-address')->controller(ShippingAddressController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/store', 'store')->name('.store');
        Route::put('/{id}', 'update')->name('.update');
        Route::delete('/{id}', 'destroy')->name('.destroy');
    });

    Route::name('.wishlist')->prefix('wishlist')->controller(WishlistController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store')->name('.store');
        Route::delete('/{productId}', 'destroy')->name('.destroy');
    });
});
