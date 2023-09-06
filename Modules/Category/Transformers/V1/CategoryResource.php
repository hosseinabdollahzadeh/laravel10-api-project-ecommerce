<?php

namespace Modules\Category\Transformers\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Product\Transformers\V1\ProductResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'parent' => new CategoryResource($this->whenLoaded('parent')),
            'products' => ProductResource::collection($this->whenLoaded('products')->load('images')),
            // OR
//            'products' => ProductResource::collection($this->whenLoaded('products', function (){
//                return $this->products->load('images');
//            })),
        ];
    }
}
