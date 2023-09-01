<?php

namespace Modules\Product\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Modules\Common\Http\Controllers\ApiController;
use Modules\Product\Database\Repositories\Api\V1\ProductImageRepo;
use Modules\Product\Database\Repositories\Api\V1\ProductRepo;
use Modules\Product\Entities\Product;
use Modules\Product\Transformers\V1\ProductResource;
use Nette\Utils\Image;

class ProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(ProductRepo $repo)
    {
        $products = $repo->paginate();
        return $this->successResponse([
            'products' => ProductResource::collection($products->load('images')),
            'links' => ProductResource::collection($products)->response()->getData()->links,
            'meta' => ProductResource::collection($products)->response()->getData()->meta,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, ProductRepo $productRepo, ProductImageRepo $productImageRepo)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'brand_id' => 'required|integer|exists:brands,id',
            'category_id' => 'required|integer|exists:categories,id',
            'primary_image' => 'required|image|mimes:jpg,bmp,png,jpeg',
            'description' => 'required',
            'price' => 'nullable|integer',
            'quantity' => 'nullable|integer',
            'delivery_amount' => 'nullable|integer',
            'images.*' => 'nullable|image|max:5120|mimes:jpg,bmp,png,jpeg',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();
        $product = $productRepo->store($request);

        if ($request->has('images')) {
            $images = [];
            foreach ($request->images as $image) {
                $imageName = generateFileName($image->getClientOriginalName());
                $image->storeAs(env('PRODUCT_IMAGES_UPLOAD_PATH'), $imageName, 'public');
                $images[] = $imageName;
            }
            foreach ($images as $image) {
                $productImageRepo->store($product, $image);
            }
        }
        DB::commit();

        return $this->successResponse(new ProductResource($product->load('images')), 201);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show(Product $product)
    {
        return $this->successResponse(new ProductResource($product->load('images')));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, Product $product, ProductRepo $productRepo, ProductImageRepo $productImageRepo)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'brand_id' => 'required|integer|exists:brands,id',
            'category_id' => 'required|integer|exists:categories,id',
            'primary_image' => 'nullable|image|mimes:jpg,bmp,png,jpeg',
            'description' => 'required',
            'price' => 'nullable|integer',
            'quantity' => 'nullable|integer',
            'delivery_amount' => 'nullable|integer',
            'images.*' => 'nullable|image|max:5120|mimes:jpg,bmp,png,jpeg',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();

        $product = $productRepo->update($product, $request);

        if ($request->has('images')) {
            $images = [];
            foreach ($request->images as $image) {
                $imageName = generateFileName($image->getClientOriginalName());
                $image->storeAs(env('PRODUCT_IMAGES_UPLOAD_PATH'), $imageName, 'public');
                $images[] = $imageName;
            }
            // delete previous images
            foreach ($product->images as $productImage){
                $imagePath = public_path('/storage'.env('PRODUCT_IMAGES_UPLOAD_PATH').$productImage->image);
                if(File::exists($imagePath)) {
                    File::delete($imagePath);
                }
                $productImage->delete();
            }
            // update new images
            foreach ($images as $image) {
                $productImageRepo->store($product, $image);
            }
        }
        DB::commit();

        return $this->successResponse(new ProductResource($product->load('images')));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Product $product, ProductRepo $productRepo)
    {
        DB::beginTransaction();
        // delete product images
        foreach ($product->images as $productImage){
            $imagePath = public_path('/storage'.env('PRODUCT_IMAGES_UPLOAD_PATH').$productImage->image);
            if(File::exists($imagePath)) {
                File::delete($imagePath);
            }
            $productImage->delete();
        }
        // delete product
        $productRepo->delete($product);
        DB::commit();
        return $this->successResponse(new ProductResource($product), 200);
    }
}
