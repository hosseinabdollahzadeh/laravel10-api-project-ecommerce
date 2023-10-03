<?php

namespace Modules\Payment\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Modules\Payment\Gateways\Gateway;

class PaymentService
{
//    public static function generate($amount, $description, $order, User $buyer)
    public static function generate($amount, $description, $buyerId)
    {
//        if($amount <= 0 || is_null($order->id) || is_null($buyer->id)) return false;
        if($amount <= 0 || is_null($buyerId)) return false;

        $gateway = resolve(Gateway::class);
        $payment = $gateway->request($amount, $description);
        return $payment;
    }

    public static function verify(Request $request)
    {
        $gateway = resolve(Gateway::class);
        $payment = $gateway->verify($request);
        return $payment;
    }
}
