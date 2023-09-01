<?php

namespace Modules\Product\Transformers\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'image' => url('/storage' . env('PRODUCT_IMAGES_UPLOAD_PATH') . $this->image),
        ];
    }
}
