<?php

namespace Modules\Payment\Gateways\Pay;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Common\Entities\Status;
use Modules\Common\Traits\ApiResponse;
use Modules\Payment\Contracts\GatewayContract;
use Modules\Payment\Entities\Order;
use Modules\Payment\Entities\Transaction;

class PayAdaptor implements GatewayContract
{

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
//        $redirect = env('PAY_IR_CALLBACK_URL');
        $redirect = $this->redirect();

        $pay = new Pay();
        $result = $pay->send($api, $amount, $redirect, $mobile, $factorNumber, $description);
        $result = json_decode($result);
//        return  $result;
        if ($result->status) {
            $go = "https://pay.ir/pg/$result->token";
            return [
                'status' => 'success',
                'message' => 'عملیات موفقیت آمیز بود.',
                'data' => [
                    'url' => $go,
                    'gateway' => $this->getName(),
                    'token' => $result->token
                ]
            ];
        } else {
            return [
                'status' => 'error',
                'message' => $result->errorMessage,
                'code' => Status::STATUS_UNPROCESSABLE_ENTITY
            ];
        }
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'status' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $api = env('PAY_IR_API_KEY');
        $token = $request->token;

        $pay = new Pay();
        $result = json_decode($pay->verify($api, $token));
        if (isset($result->status)) {
            if ($result->status == 1) {
                if(Transaction::where('trans_id', $result->transId)->exists()){
                    return [
                        'status' => 'error',
                        'message' => 'این تراکنش قبلا ثبت شده است!',
                        'code' => Status::STATUS_UNPROCESSABLE_ENTITY
                    ];
                }
                return [
                    'status' => 'success',
                    'message' => 'عملیات موفقیت آمیز بود.',
                    'data' => [
                        'token' => $request->token,
                        'transId' => $result->transId
                    ]
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'تراکنش با خطا مواجه شد!',
                    'code' => Status::STATUS_UNPROCESSABLE_ENTITY
                ];
            }
        } else {
            if($request->status == 0){
                return [
                    'status' => 'error',
                    'message' => 'تراکنش با خطا مواجه شد!',
                    'code' => Status::STATUS_UNPROCESSABLE_ENTITY
                ];
            }

        }
    }

    public function redirect()
    {
        return route('payment_verify', ['gatewayName' => $this->getName()]);
    }

    public function getName()
    {
        return 'pay';
    }
}
