<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Modules\Core\Enums\ResponseCode;

if(!function_exists('successResponse')) {
    function successResponse($data, $message = '', $code = ResponseCode::OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $code);
    }
}

if(!function_exists('authSuccessResponse')) {
    function authSuccessResponse($token, $expires_in, $message = '', $code = ResponseCode::OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'expires_in' => $expires_in,
            'message' => $message,
        ], $code);
    }
}

if(!function_exists('failedResponse')) {
    function failedResponse($message, $code = ResponseCode::SERVER_ERROR): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }
}

if(!function_exists('fileResponse')){
    function fileResponse($data) {
        $file = $data['file'];
        $type = $data['type'];
        return Response::make($file, 200)->header('Content-Type', $type);
    }
}

