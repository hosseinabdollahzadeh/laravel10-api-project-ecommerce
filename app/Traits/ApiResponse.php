<?php

namespace App\Traits;

Trait ApiResponse
{
    protected function successResponse($data, $code = 200, $message = null)
    {
        return response()->json([
           'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function errorResponse($message = null, $code)
    {
        return response()->json([
           'status' => 'error',
            'message' => $message,
            'data' => null
        ], $code);
    }
}
