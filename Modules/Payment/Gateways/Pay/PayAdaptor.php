<?php
namespace Modules\Payment\Gateways\Pay;

use Illuminate\Http\Request;
use Modules\Common\Traits\ApiResponse;
use Modules\Payment\Contracts\GatewayContract;
use Modules\Payment\Entities\Order;

class PayAdaptor implements GatewayContract{

    use ApiResponse;
    public function request($amount, $description)
    {
//      Add to .env file
//      PAY_IR_API_KEY=test
//      PAY_IR_CALLBACK_URL=http://laravel10-api-project-ecommerce.test/payment/verify

        $api = env('PAY_IR_API_KEY');
//        $amount = 100000;
        $mobile = "شماره موبایل";
        $factorNumber = "شماره فاکتور";
//        $description = "توضیحات";
        $redirect = env('PAY_IR_CALLBACK_URL');

        $pay = new Pay();
        $result = $pay->send($api, $amount, $redirect, $mobile, $factorNumber, $description);
        $result = json_decode($result);
//        dd($result);
        if ($result->status) {
            $go = "https://pay.ir/pg/$result->token";
            return $this->successResponse([
                'url' => $go
            ]);
        } else {
            return $this->errorResponse($result->errorMessage, 422);
        }
    }

    public function verify(Request $request)
    {
        $api = env('PAY_IR_API_KEY');
        $token = $request->token;

        $pay = new Pay();
        $result = json_decode($pay->verify($api,$token));
        return response()->json($result);
        if(isset($result->status)){
            if($result->status == 1){
                echo "<h1>تراکنش با موفقیت انجام شد</h1>";
            } else {
                echo "<h1>تراکنش با خطا مواجه شد</h1>";
            }
        } else {
            if($_GET['status'] == 0){
                echo "<h1>تراکنش با خطا مواجه شد</h1>";
            }
        }
    }
}
