<?php

namespace Modules\Product\Database\Repositories\Api\V1;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
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

    public static function findById($id)
    {
        return Product::query()->findOrFail($id);
    }

    public function store($values)
    {
        $primaryImageName = generateFileName($values->primary_image->getClientOriginalName());
        $values->primary_image->storeAs(env('PRODUCT_IMAGES_UPLOAD_PATH'), $primaryImageName, 'public');

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

    public function update($product, $values)
    {
        if($values->has('primary_image')){
            // delete previous primary_image
            $primaryImagePath = public_path('/storage'.env('PRODUCT_IMAGES_UPLOAD_PATH').$product->primary_image);
            if(File::exists($primaryImagePath)) {
                File::delete($primaryImagePath);
            }
            $primaryImageName = generateFileName($values->primary_image->getClientOriginalName());
            $values->primary_image->storeAs(env('PRODUCT_IMAGES_UPLOAD_PATH'), $primaryImageName, 'public');
        }

        $product->update([
            'name' => $values->name,
            'brand_id' => $values->brand_id,
            'category_id' => $values->category_id,
            'primary_image' => $primaryImageName ?? $product->primary_image,
            'description' => $values->description,
            'price' => $values->price,
            'quantity' => $values->quantity,
            'delivery_amount' => $values->delivery_amount,
        ]);
        return $product;
    }

    public function delete($product)
    {
        // delete primary_image
        $primaryImagePath = public_path('/storage'.env('PRODUCT_IMAGES_UPLOAD_PATH').$product->primary_image);
        if(File::exists($primaryImagePath)) {
            File::delete($primaryImagePath);
        }
    }
}
