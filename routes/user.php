<?php

use App\Http\Controllers\Api\v1\Users\AddressController;
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

Route::middleware('auth:sanctum')->name('v1_user')->group(function () {
    Route::put('/addresses/billing', [AddressController::class, 'updateBilling'])->name('.addresses.billing');
    Route::put('/addresses/shipping', [AddressController::class, 'updateShipping'])->name('.addresses.shipping');
});
