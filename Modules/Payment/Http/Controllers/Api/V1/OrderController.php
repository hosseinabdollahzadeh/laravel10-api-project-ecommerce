<?php

namespace Modules\Payment\Http\Controllers\Api\V1;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Common\Http\Controllers\ApiController;
use Modules\Common\Traits\ApiResponse;
use Modules\Payment\Repositories\OrderItemRepo;
use Modules\Payment\Repositories\OrderRepo;
use Modules\Payment\Repositories\TransactionRepo;
use Modules\Product\Database\Repositories\Api\V1\ProductRepo;

class OrderController extends ApiController
{
    use ApiResponse;
    public static function create(Request $request, $amounts, $token, $gateway)
    {
        DB::beginTransaction();

        // Store Order
        $order = OrderRepo::store($request, $amounts);

        // Store OrderItem
        foreach ($request->order_items as $orderItem){
            OrderItemRepo::store($order, $orderItem);
        }

        // Store Transaction
        TransactionRepo::store($request, $order, $amounts, $token, $gateway);

        DB::commit();
    }

    public function update($token, $transId)
    {
        DB::beginTransaction();
        // Find and update transaction
        $transaction = TransactionRepo::findByToken($token);
        $transaction->update([
            'status' => 1,
            'trans_id' => $transId,
        ]);

        // Find and update order
        $order = OrderRepo::findById($transaction->order_id);
        $order->update([
            'status' => 1,
            'payment_status' => 1
        ]);

        // Find and update orderItems
        $orderItems = OrderItemRepo::findItemsOfOrder($order->id);
        foreach ($orderItems as $item){
         $product = ProductRepo::findById($item->product_id);
         $product->update([
            'quantity' => $product->quantity - $item->quantity
         ]);
        }
        DB::commit();

        return $this->successResponse(null, 200, 'تراکنش با موفقیت انجام شد.');
    }
}
