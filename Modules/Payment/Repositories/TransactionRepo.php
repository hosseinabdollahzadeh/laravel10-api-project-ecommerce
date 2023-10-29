<?php

namespace Modules\Payment\Repositories;

use Modules\Payment\Entities\Transaction;

class TransactionRepo
{
    public static function store($request, $order, $amounts, $token, $gateway)
    {
        return Transaction::create([
           'user_id' => $request->user_id,
           'order_id' => $order->id,
           'amount' => $amounts['payingAmount'],
           'token' => $token,
           'gateway' => $gateway,
           'request_from' => $request->request_from,
        ]);
    }

    public static function findByToken($token)
    {
        return Transaction::query()->where('token', $token)->firstOrFail();
    }
}
