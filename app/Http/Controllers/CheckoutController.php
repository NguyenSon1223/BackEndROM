<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
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
