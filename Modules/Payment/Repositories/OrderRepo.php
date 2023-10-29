<?php

namespace Modules\Payment\Repositories;

use Modules\Payment\Entities\Order;

class OrderRepo
{
    public static function store($request, $amounts)
    {
        return Order::create([
            'user_id' => $request->user_id,
            'total_amount' => $amounts['totalAmount'],
            'delivery_amount' => $amounts['deliveryAmount'],
            'paying_amount' => $amounts['payingAmount'],
        ]);
    }

    public static function findById( $orderId)
    {
        return Order::findOrFail($orderId);
    }
}
