<?php

namespace Modules\Product\Database\Repositories\Api\V1;

use Modules\Product\Entities\ProductImage;

class ProductImageRepo
{
    public function store($product, $image)
    {
        return ProductImage::create([
            'product_id' => $product->id,
            'image' => $image,
        ]);
    }
}
