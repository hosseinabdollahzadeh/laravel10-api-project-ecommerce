<?php

namespace Modules\Payment\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Common\Http\Controllers\ApiController;
use Modules\Common\Traits\ApiResponse;
use Modules\Payment\Services\PaymentService;
use Modules\Product\Entities\Product;

class PaymentController extends ApiController
{
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'order_items' => 'required',
            'order_items.*.product_id' => 'required|integer',
            'order_items.*.quantity' => 'required|integer',
            'request_from' => 'required',
            'payment_gateway' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }


        $totalAmount = 0;
        $deliveryAmount = 0;
        foreach ($request->order_items as $orderItem) {
            $product = Product::findOrFail($orderItem['product_id']);
            if ($product->quantity < $orderItem['quantity']) {
                return $this->errorResponse('The product quantity is incorrect', 422);
            }

            $totalAmount += $product->price * $orderItem['quantity'];
            $deliveryAmount += $product->delivery_amount;
        }

        $payingAmount = $totalAmount + $deliveryAmount;

        $amounts = [
            'totalAmount' => $totalAmount,
            'deliveryAmount' => $deliveryAmount,
            'payingAmount' => $payingAmount,
        ];
        $description = "توضیحات";
        $paymentGateway = $request->payment_gateway;

        $sentPaymentResult = PaymentService::generate($paymentGateway, $amounts, $description, 1);
        if ($sentPaymentResult['status'] == 'error') {
            return $this->errorResponse($sentPaymentResult['message'], $sentPaymentResult['code']);
        } elseif ($sentPaymentResult['status'] == 'success') {
            OrderController::create($request, $amounts, $sentPaymentResult['data']['token'], $sentPaymentResult['data']['gateway']);
            return $this->successResponse([
                'url' => $sentPaymentResult['data']['url']
            ]);
        }


    }

    public function verify(Request $request)
    {
        $verifyPaymentResult = PaymentService::verify($request);
        if ($verifyPaymentResult['status'] == 'error') {
            return $this->errorResponse($verifyPaymentResult['message'], $verifyPaymentResult['code']);
        } elseif ($verifyPaymentResult['status'] == 'success') {
            $order = new OrderController();
            return $order->update($verifyPaymentResult['data']['token'], $verifyPaymentResult['data']['transId']);
        }
    }


}
