<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\Api\V1\CategoryController as V1CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/category', function (Request $request) {
//    return $request->user();
//});

Route::prefix('v1')->group(function (){
    Route::apiResource('categories', V1CategoryController::class);
    Route::get('categories/{category}/children', [V1CategoryController::class, 'children']);
    Route::get('categories/{category}/parent', [V1CategoryController::class, 'parent']);
});
