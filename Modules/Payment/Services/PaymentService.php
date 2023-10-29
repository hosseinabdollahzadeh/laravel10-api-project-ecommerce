<?php

namespace Modules\Payment\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Modules\Payment\Gateways\Gateway;
use Modules\Payment\Gateways\Pay\PayAdaptor;
use Modules\Payment\Gateways\Zibal\ZibalAdaptor;

class PaymentService
{
//    public static function generate($amount, $description, $order, User $buyer)
    public static function generate($paymentGateway, $amounts, $description, $buyerId)
    {
        $gateways = ['pay', 'zibal'];
        if ($amounts['payingAmount'] <= 0 || is_null($buyerId) || !in_array($paymentGateway, $gateways)) return false;

//        $gateway = resolve(Gateway::class);
        switch ($paymentGateway) {
            case 'pay':
                $gateway = new PayAdaptor();
                break;

            case 'zibal':
                $gateway = new ZibalAdaptor();
                break;

            default:
                return redirect()->back();
        }

        $amount = $amounts['payingAmount'] . '0'; // Convert toman to rial
        $payment = $gateway->request($amount, $description);
        return $payment;
    }

    public static function verify(Request $request)
    {
        switch ($request->gateway) {
            case 'pay':
                $gateway = new PayAdaptor();
                break;

            case 'zibal':
                $gateway = new ZibalAdaptor();
                break;

            default:
                return false;
        }

//        $gateway = resolve(Gateway::class);
        $payment = $gateway->verify($request);
        return $payment;
    }
}
