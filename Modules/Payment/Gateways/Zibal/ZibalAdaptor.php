<?php

namespace Modules\Payment\Gateways\Zibal;

use Illuminate\Http\Request;
use Modules\Common\Traits\ApiResponse;
use Modules\Payment\Contracts\GatewayContract;

class ZibalAdaptor implements GatewayContract
{

    use ApiResponse;
    public function request($amount, $description)
    {
//      Add to .env file
//        ZIBAL_IR_API_KEY=zibal
//        ZIBAL_IR_CALLBACK_URL=http://laravel10-api-project-ecommerce.test/payment/verify

        $merchant = env('ZIBAL_IR_API_KEY');
//        $amount = 100000;
        $mobile = "شماره موبایل";
        $factorNumber = "شماره فاکتور";
//        $description = "توضیحات";
        $callbackUrl = env('ZIBAL_IR_CALLBACK_URL');

        $pay = new Zibal();
        $result = $pay->send($merchant, $amount, $callbackUrl, $mobile, $description);
        $result = json_decode($result);
//        dd($result);
        if ($result->result == 100) {
            $go = "https://gateway.zibal.ir/start/$result->trackId";
            return $this->successResponse([
                'url' => $go
            ]);
        } else {
            return $this->errorResponse('تراکنش با خطا مواجه شد!', 422);
        }
    }

    public function verify(Request $request)
    {
        $merchant = env('ZIBAL_IR_API_KEY');
        $trackId = $request->trackId;

        $pay = new Zibal();
        $result = json_decode($pay->verify($merchant,$trackId));
        return response()->json($result);
        if(isset($result->result)){
            if($result->result == 100){
                echo "<h1>تراکنش با موفقیت انجام شد</h1>";
            } else {
                echo "<h1>$result->message</h1>";
                }
            }
        }
}
