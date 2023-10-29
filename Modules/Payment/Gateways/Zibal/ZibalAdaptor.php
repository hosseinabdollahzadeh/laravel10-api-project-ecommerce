<?php

namespace Modules\Payment\Gateways\Zibal;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Common\Entities\Status;
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
//        $callbackUrl = env('ZIBAL_IR_CALLBACK_URL');
        $callbackUrl = $this->redirect();

        $pay = new Zibal();
        $result = $pay->send($merchant, $amount, $callbackUrl, $mobile, $description);
        $result = json_decode($result);
        if ($result->result == 100) {
            $go = "https://gateway.zibal.ir/start/$result->trackId";
            return [
                'status' => 'success',
                'message' => 'عملیات موفقیت آمیز بود.',
                'data' => [
                    'url' => $go,
                    'gateway' => $this->getName(),
                    'token' => $result->trackId
                ]
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'تراکنش با خطا مواجه شد!',
                'code' => Status::STATUS_UNPROCESSABLE_ENTITY
            ];
        }
    }


    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trackId' => 'required',
            'success' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $merchant = env('ZIBAL_IR_API_KEY');
        $trackId = $request->trackId;

        $zibal = new Zibal();
        $result = json_decode($zibal->verify($merchant, $trackId));

//        return $result;
        if (isset($result->result)) {
            if ($result->result == 100) {
                return [
                    'status' => 'success',
                    'message' => 'عملیات موفقیت آمیز بود.',
                    'data' => [
                        'token' => $request->trackId,
                        'transId' => $result->refNumber
                    ]
                ];
            } elseif ($result->result == 201) {
                return [
                    'status' => 'error',
                    'message' => 'این تراکنش قبلا ثبت شده است!',
                    'code' => Status::STATUS_UNPROCESSABLE_ENTITY
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'تراکنش با خطا مواجه شد!',
                    'code' => Status::STATUS_UNPROCESSABLE_ENTITY
                ];
            }
        } else {
            return [
                'status' => 'error',
                'message' => 'تراکنش با خطا مواجه شد!',
                'code' => Status::STATUS_UNPROCESSABLE_ENTITY
            ];
        }
    }

    public function redirect()
    {
        return route('payment_verify', ['gatewayName' => 'zibal']);
    }

    public function getName()
    {
        return "zibal";
    }
}
