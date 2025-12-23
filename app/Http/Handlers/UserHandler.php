<?php

namespace App\Http\Handlers;

use App\Controllers\UserController;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\ListUsersRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Auth\Access\AuthorizationException;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class UserHandler
{
    public function __construct(private UserController $controller)
    {
    }

    public function store(CreateUserRequest $request): JsonResponse
    {
        $data = $this->controller->store($request->validated());

        return ApiResponse::success(
            data: $data,
            message: 'User created.',
            code: 'USER_CREATED',
            status: 201
        );
    }

    public function index(ListUsersRequest $request): JsonResponse
    {
        $data = $this->controller->index(
            filters: $request->validated(),
            currentUser: $request->user()
        );

        return ApiResponse::success(
            data: $data,
            message: 'Users fetched.',
            code: 'USERS_LIST'
        );
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            $data = $this->controller->update(
                currentUser: $request->user(),
                user: $user,
                data: $request->validated()
            );
        } catch (AuthorizationException $e) {
            return ApiResponse::error(
                message: 'Forbidden.',
                code: 'FORBIDDEN',
                status: 403
            );
        }

        return ApiResponse::success(
            data: $data,
            message: 'User updated.',
            code: 'USER_UPDATED'
        );
    }
}
