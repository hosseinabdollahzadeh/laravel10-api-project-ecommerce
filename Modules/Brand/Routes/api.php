<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Brand\Http\Controllers\Api\V1\BrandController as V1BrandController;
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

//Route::middleware('auth:api')->get('/brand', function (Request $request) {
//    return $request->user();
//});

Route::prefix('v1')->group(function (){
    Route::apiResource('brands', V1BrandController::class);
    Route::get('/brands/{brand}/products', [V1BrandController::class, 'products']);
});
