<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/success.html', function () {
    return view('success');
});

Route::get('/cancel.html', function () {
    return view('cancel');
});

Route::post('/create-payment-link', [CheckoutController::class, 'createPaymentLink']);

Route::prefix('/order')->group(function () {
    Route::post('/create', [OrderController::class, 'createOrder']);
    Route::get('/{id}', [OrderController::class, 'getPaymentLinkInfoOfOrder']);
    Route::put('/{id}', [OrderController::class, 'cancelPaymentLinkOfOrder']);
});

Route::prefix('/payment')->group(function () {
    Route::post('/payos', [PaymentController::class, 'handlePayOSWebhook']);
});
