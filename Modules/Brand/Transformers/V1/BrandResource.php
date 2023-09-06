<?php

namespace Modules\Brand\Transformers\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Product\Transformers\V1\ProductResource;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
//        return parent::toArray($request);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'products' => ProductResource::collection($this->whenLoaded('products')->load('images')),
        // OR
//            'products' => ProductResource::collection($this->whenLoaded('products', function () {
//                return $this->products->load('images');
//            }))
        ];
    }
}
