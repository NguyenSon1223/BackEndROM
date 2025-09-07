<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PayOSService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $payos;


    public function getAllOrders()
    {
        $orders = Order::orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Danh sách đơn hàng',
            'data' => $orders
        ], 200);
    }
    public function __construct(PayOSService $payos)
    {
        $this->payos = $payos;
    }

    // API tạo đơn hàng + tạo link thanh toán PayOS
    public function createOrder(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'amount'      => 'required|numeric|min:1000',
        ]);

        $orderCode = time(); // đơn giản, bạn có thể dùng UUID

        $order = Order::create([
            'order_code'  => $orderCode,
            'description' => $request->description,
            'amount'      => $request->amount,
            'status'      => 'pending',
        ]);

        $payment = $this->payos->createPayment(
            $order->order_code,
            $order->amount,
            $order->description,
            env('PAYOS_RETURN_URL'),
            env('PAYOS_CANCEL_URL')
        );

        return response()->json([
            'order' => $order,
            'payment' => $payment
        ]);
    }

    // Return URL
    public function handleReturn(Request $request)
    {
        $orderCode = $request->get('orderCode');
        $status = $request->get('status'); // payOS gửi kèm

        $order = Order::where('order_code', $orderCode)->first();
        if ($order) {
            $order->status = $status === 'PAID' ? 'paid' : 'failed';
            $order->save();
        }

        return response()->json([
            'message' => 'Return handled',
            'order' => $order
        ]);
    }

    // Cancel URL
    public function handleCancel(Request $request)
    {
        $orderCode = $request->get('orderCode');

        $order = Order::where('order_code', $orderCode)->first();
        if ($order) {
            $order->status = 'canceled';
            $order->save();
        }

        return response()->json([
            'message' => 'Order canceled',
            'order' => $order
        ]);
    }
}
