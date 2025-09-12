<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use PayOS\PayOS;

class CheckoutController extends Controller
{

    use AuthorizesRequests, ValidatesRequests;

    protected PayOS $payOS;

    public function __construct()
    {
        $this->payOS = new PayOS(
            env("PAYOS_API_KEY"),
            env("PAYOS_CLIENT_ID"),
            env("PAYOS_CHECKSUM_KEY")
        );
    }

    protected function handleException(\Throwable $th){
        return response()->json([
            "error" => $th->getCode(),
            "message" => $th->getMessage(),
            "data" => null
        ]);
    }
    public function createPaymentLink(Request $request)
    {
        $domain = $request->getSchemeAndHttpHost();
        $data = [
            "orderCode" => intval(substr(strval(microtime(true) * 100000), -6)),
            "amount" => 2000,
            "description" => "Thanh toán đơn hàng",
            "returnUrl" => $domain."/success.html",
        ];
        error_log($data['orderCode']);


        try {
            $response = $this->payOS->createPaymentLink($data);

            return redirect($response['checkoutUrl']);
        }catch (\Throwable $th) {
            return $this->handleException($th);
        }
    }
}
