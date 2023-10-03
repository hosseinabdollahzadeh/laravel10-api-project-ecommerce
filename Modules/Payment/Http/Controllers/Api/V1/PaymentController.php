<?php

namespace Modules\Payment\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Modules\Common\Http\Controllers\ApiController;
use Modules\Payment\Services\PaymentService;

class PaymentController extends ApiController
{
    public function send()
    {
        $amount = 100000;
        $description = "توضیحات";

        $payment = PaymentService::generate($amount, $description, 1);
        return $payment;

    }

    public function verify(Request $request)
    {
        $payment = PaymentService::verify($request);
        return $payment;
    }


}
