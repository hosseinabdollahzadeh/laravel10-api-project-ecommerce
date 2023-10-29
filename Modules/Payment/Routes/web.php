<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::prefix('payment')->group(function () {

    //pay.ir
    Route::get('/verify/{gatewayName}', function (Request $request) {
        $gatewayName = $request->gatewayName;
        switch ($gatewayName) {
            case 'pay':
                $response = Http::post('http://laravel10-api-project-ecommerce.test/api/v1/payment/verify', [
                    'token' => $request->token,
                    'status' => $request->status,
                    'gateway' => $gatewayName,
                ]);
                return $response;
                break;
            case 'zibal':
                $response = Http::post('http://laravel10-api-project-ecommerce.test/api/v1/payment/verify', [
                    'trackId' => $request->trackId,
                    'success' => $request->success,
                    'gateway' => $gatewayName,
                ]);
                return $response;
                break;
        }
    })->name('payment_verify');
});
