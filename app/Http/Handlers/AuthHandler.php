<?php

namespace App\Http\Handlers;

use App\Controllers\LoginController;
use App\Http\Requests\LoginRequest;
use App\Support\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;

class AuthHandler
{
    public function __construct(private LoginController $controller)
    {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = $this->controller->login($request->validated());

            return ApiResponse::success(
                data: $data,
                message: 'Login successful.',
                code: 'LOGIN_SUCCESS'
            );
        } catch (AuthenticationException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 'INVALID_CREDENTIALS',
                status: 401
            );
        }
    }
}
