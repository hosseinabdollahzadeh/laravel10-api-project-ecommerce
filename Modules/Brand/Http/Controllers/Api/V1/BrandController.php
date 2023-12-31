<?php

namespace Modules\Brand\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Brand\Database\Repositories\Api\V1\BrandRepo;
use Modules\Brand\Entities\Brand;
use Modules\Brand\Transformers\V1\BrandResource;
use Modules\Common\Http\Controllers\ApiController;
use Modules\Common\Traits\ApiResponse;

class BrandController extends ApiController
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(BrandRepo $repo)
    {
        $brands = $repo->paginate();
        return $this->successResponse([
            'brands' => BrandResource::collection($brands),
            'links' => BrandResource::collection($brands)->response()->getData()->links,
            'meta' => BrandResource::collection($brands)->response()->getData()->meta,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, BrandRepo $repo)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:brands,name',
            'display_name' => 'required|unique:brands,display_name'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();
        $brand = $repo->store($request);
        DB::commit();

        return $this->successResponse(new BrandResource($brand), 201);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Brand $brand)
    {
        return  $this->successResponse(new BrandResource($brand));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Brand $brand, BrandRepo $repo)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:brands,name',
            'display_name' => 'required|unique:brands,display_name'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();
        $repo->update($brand->id, $request);
        DB::commit();

        return $this->successResponse(new BrandResource($brand), 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Brand $brand)
    {
        DB::beginTransaction();
        $brand->delete();
        DB::commit();
        return $this->successResponse(new BrandResource($brand), 200);
    }

    public function products(Brand $brand)
    {
        return  $this->successResponse(new BrandResource($brand->load('products')));
    }
}
