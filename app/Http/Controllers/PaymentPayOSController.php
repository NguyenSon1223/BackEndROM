<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentPayOSController extends Controller
{
    public function createPayment(Request $request)
{
    $orderCode = time(); // mã đơn hàng duy nhất
    $amount = $request->amount ?? 10000;
    $description = $request->description ?? "Thanh toán đơn hàng #" . $orderCode;

    $payload = [
        "orderCode"   => $orderCode,
        "amount"      => $amount,
        "description" => $description,
        "returnUrl"   => env("PAYOS_RETURN_URL"),
        "cancelUrl"   => env("PAYOS_CANCEL_URL"),
        "buyerName"   => "Nguyen Van A",
        "buyerEmail"  => "a@gmail.com",
        "buyerPhone"  => "0901234567"
    ];

    $response = Http::withHeaders([
        "x-client-id"  => env("PAYOS_CLIENT_ID"),
        "x-api-key"    => env("PAYOS_API_KEY"),
        "Content-Type" => "application/json"
    ])->post(env("PAYOS_BASE_URL") . "/payment-requests", $payload);

    if ($response->failed()) {
        return response()->json([
            "status"  => "error",
            "message" => $response->body()
        ], 400);
    }

    $json = $response->json();

    // Lấy checkoutUrl linh hoạt
    $checkoutUrl = $json['data']['checkoutUrl']
        ?? $json['checkoutUrl']
        ?? null;

    if (!$checkoutUrl) {
        return response()->json([
            "status"  => "error",
            "message" => "Không tìm thấy checkoutUrl trong response",
            "response" => $json
        ], 500);
    }

    return response()->json([
        "status"     => "success",
        "paymentUrl" => $checkoutUrl,
        "orderCode"  => $orderCode
    ]);
}


    /**
     * Callback khi thanh toán thành công
     */
    public function paymentSuccess(Request $request)
    {
        return response()->json([
            "status" => "success",
            "message" => "Thanh toán thành công!",
            "data" => $request->all()
        ]);
    }

    /**
     * Callback khi thanh toán thất bại / bị hủy
     */
    public function paymentCancel(Request $request)
    {
        return response()->json([
            "status" => "cancelled",
            "message" => "Thanh toán đã bị hủy.",
            "data" => $request->all()
        ]);
    }

}
