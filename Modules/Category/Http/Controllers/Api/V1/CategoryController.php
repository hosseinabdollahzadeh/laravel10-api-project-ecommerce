<?php

namespace Modules\Category\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Category\Database\Repositories\Api\V1\CtegoryRepo;
use Modules\Category\Entities\Category;
use Modules\Category\Transformers\V1\CategoryResource;
use Modules\Common\Http\Controllers\ApiController;

class CategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(CtegoryRepo $repo)
    {
        $category = $repo->paginate();
        return $this->successResponse([
            'brands' => CategoryResource::collection($category),
            'links' => CategoryResource::collection($category)->response()->getData()->links,
            'meta' => CategoryResource::collection($category)->response()->getData()->meta,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, CtegoryRepo $repo)
    {
        $validator = Validator::make($request->all(), [
            'parent_id' => 'required|integer',
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();
        $category = $repo->store($request);
        DB::commit();

        return $this->successResponse(new CategoryResource($category), 201);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show(Category $category)
    {
        return  $this->successResponse(new CategoryResource($category));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, Category $category, CtegoryRepo $repo)
    {
        $validator = Validator::make($request->all(), [
            'parent_id' => 'required|integer',
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();
        $repo->update($category->id, $request);
        DB::commit();

        return $this->successResponse(new CategoryResource($category), 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Category $category)
    {
        DB::beginTransaction();
        $category->delete();
        DB::commit();
        return $this->successResponse(new CategoryResource($category), 200);
    }

    public function children(Category $category)
    {
        return $this->successResponse(new CategoryResource($category->load('children')), 200);
    }

    public function parent(Category $category)
    {
        return $this->successResponse(new CategoryResource($category->load('parent')), 200);
    }
}
