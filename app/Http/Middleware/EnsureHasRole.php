<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasRole
{
    /**
     * @param  array<int, string>  $roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return ApiResponse::error('Unauthenticated.', code: 'UNAUTHENTICATED', status: 401);
        }

        $currentRole = $user->role instanceof UserRole ? $user->role : UserRole::tryFrom((string) $user->role);
        $allowed = array_map(static function ($role) {
            return strtolower((string) $role);
        }, $roles);

        if ($currentRole && in_array(strtolower($currentRole->value), $allowed, true)) {
            return $next($request);
        }

        return ApiResponse::error('Forbidden.', code: 'FORBIDDEN', status: 403);
    }
}
