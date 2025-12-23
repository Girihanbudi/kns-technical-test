<?php

namespace App\Http\Requests;

use App\Enums\UserRole;

class UpdateUserRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|min:3|max:50',
            'role' => 'sometimes|in:'.implode(',', array_column(UserRole::cases(), 'value')),
            'emailVerifiedAt' => 'sometimes|nullable|date',
            'active' => 'sometimes|boolean',
        ];
    }
}
