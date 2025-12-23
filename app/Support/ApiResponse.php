<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(mixed $data = null, string $message = 'Success', string $code = 'SUCCESS', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'errors' => (object) [],
            'data' => $data,
            'code' => $code,
        ], $status);
    }

    public static function error(string $message, array $errors = [], string $code = 'ERROR', int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors ?: (object) [],
            'data' => null,
            'code' => $code,
        ], $status);
    }
}
