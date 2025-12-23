<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Support\ApiResponse;

class AuthenticateWithToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();

        if (!$plainToken) {
            return ApiResponse::error('Unauthenticated.', code: 'UNAUTHORIZED', status: 401);
        }

        $hashedToken = hash('sha256', $plainToken);

        $apiToken = ApiToken::with('user')->where('token', $hashedToken)->first();

        if (!$apiToken || !$apiToken->user || !$apiToken->user->active) {
            return ApiResponse::error('Unauthenticated.', code: 'INVALID_TOKEN', status: 401);
        }

        Auth::login($apiToken->user);

        $apiToken->forceFill([
            'last_used_at' => now(),
        ])->save();

        return $next($request);
    }
}
