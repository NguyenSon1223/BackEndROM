<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentPayOSController;
use App\Http\Controllers\RomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('categories', CategoryController::class);

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

Route::apiResource('roms', RomController::class);



Route::get('/orders', [OrderController::class, 'getAllOrders']);
Route::post('/orders', [OrderController::class, 'createOrder']);
Route::get('/payos/return', [OrderController::class, 'handleReturn']);
Route::get('/payos/cancel', [OrderController::class, 'handleCancel']);


# Payment


Route::post('/payment/create', [PaymentPayOSController::class, 'createPayment']);
Route::get('/payment/success', [PaymentPayOSController::class, 'paymentSuccess']);
Route::get('/payment/cancel', [PaymentPayOSController::class, 'paymentCancel']);
