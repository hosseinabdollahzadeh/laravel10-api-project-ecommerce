<?php

namespace Modules\Payment\Repositories;

use Modules\Payment\Entities\OrderItem;
use Modules\Product\Entities\Product;

class OrderItemRepo
{
    public static function store($order, $orderItem)
    {
        $product = Product::findOrFail($orderItem['product_id']);
        return OrderItem::create([
            'product_id' => $product->id,
            'order_id' => $order->id,
            'price' => $product->price,
            'quantity' => $orderItem['quantity'],
            'subtotal' => ($product->price * $orderItem['quantity'])
        ]);
    }

    public static function findItemsOfOrder($orderId)
    {
        return OrderItem::query()->where('order_id', $orderId)->get();
    }
}
