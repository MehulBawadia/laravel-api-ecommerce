<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Send the success response.
     *
     * @param  string  $message
     * @param  array  $data
     * @param  integer  $statusCode
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse($message, $data = [], $statusCode = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message ?? 'Success',
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Send the error response.
     *
     * @param  string  $message
     * @param  array  $data
     * @param  integer  $statusCode
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse($message, $data = [], $statusCode = 500)
    {
        return response()->json([
            'status' => 'failed',
            'message' => $message ?? 'Failed',
            'data' => $data,
        ], $statusCode);
    }
}
