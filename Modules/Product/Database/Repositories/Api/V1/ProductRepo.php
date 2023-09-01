<?php

namespace Modules\Product\Database\Repositories\Api\V1;


use Modules\Product\Entities\Product;

class ProductRepo
{
    public $query;

    public function __construct()
    {
        $this->query = Product::query();
    }

    public function paginate($perPage = 10)
    {
        return $this->query->paginate($perPage);
    }

    public function findById($id)
    {
        return Product::query()->findOrFail($id);
    }

    public function store($values)
    {
        $primaryImageName = generateFileName($values->primary_image->getClientOriginalName());
        $values->primary_image->storeAs('images/products', $primaryImageName, 'public');

        $product = Product::create([
            'name' => $values->name,
            'brand_id' => $values->brand_id,
            'category_id' => $values->category_id,
            'primary_image' => $primaryImageName,
            'description' => $values->description,
            'price' => $values->price,
            'quantity' => $values->quantity,
            'delivery_amount' => $values->delivery_amount,
        ]);
        return $product;
    }

    public function update($id, $values)
    {
        $brand = $this->findById($id);
        return $brand->update([
            'name' => $values->name,
            'display_name' => $values->display_name,
        ]);
    }
}
