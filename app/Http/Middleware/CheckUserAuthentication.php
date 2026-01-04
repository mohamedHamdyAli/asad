<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserAuthentication
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth('api')->check()) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Unauthenticated',
            ], 401);
        }

        $user = auth('api')->user();

        if ((int) $user->is_enabled === 0) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Account is disabled',
            ], 403);
        }

        return $next($request);
    }
}
