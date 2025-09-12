<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PayOsController extends Controller
{
    /**
     * Tạo yêu cầu thanh toán
     */
    public function createPayment(Request $request)
    {
        $orderCode   = time(); // Mã đơn hàng duy nhất
        $amount      = $request->amount ?? 10000; // số tiền (integer VND)
        $description = $request->description ?? "Thanh toán đơn hàng #" . $orderCode;

        // Payload chỉ giữ các field mà PayOS yêu cầu
        $payload = [
            "orderCode"   => $orderCode,
            "amount"      => (int) $amount,
            "description" => $description,
            "returnUrl"   => env("PAYOS_RETURN_URL", "http://localhost:8000/payment/success"),
            "cancelUrl"   => env("PAYOS_CANCEL_URL", "http://localhost:8000/payment/cancel")
        ];

        // 👉 Endpoint Sandbox để test
        $url = "https://api-sandbox.payos.vn/v2/payment-requests";

        $response = Http::withHeaders([
            "x-client-id"  => env("PAYOS_CLIENT_ID"),
            "x-api-key"    => env("PAYOS_API_KEY"),
            "Content-Type" => "application/json"
        ])->post($url, $payload);

        if ($response->failed()) {
            return response()->json([
                "status"  => "error",
                "message" => $response->body()
            ], 400);
        }

        $json = $response->json();
        $checkoutUrl = $json['data']['checkoutUrl'] ?? null;

        if (!$checkoutUrl) {
            return response()->json([
                "status"   => "error",
                "message"  => "Không tìm thấy checkoutUrl",
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
     * Xử lý webhook từ PayOS
     */
    public function handleWebhook(Request $request)
    {
        $body = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                "error"   => 1,
                "message" => "Invalid JSON payload"
            ], 400);
        }

        if (!isset($body["data"])) {
            return response()->json([
                "error"   => 1,
                "message" => "Dữ liệu webhook không hợp lệ"
            ], 400);
        }

        // 👉 Tại đây bạn xử lý DB: lưu orderCode, cập nhật trạng thái thanh toán,...
        return response()->json([
            "error"   => 0,
            "message" => "Webhook received successfully",
            "data"    => $body["data"]
        ]);
    }
}
