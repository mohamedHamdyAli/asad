<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserAndGuestAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasHeader('Authorization')) {
            if (!auth('api')->check()) {
                return failReturnMsg('You are not authenticated', 401);
            }
            $user = auth('api')->user();
            if ($user && $user->is_enabled === 0) {
                Auth::logout();
                return failReturnMsg('account is disabled');
            }
        } else {
            return failReturnMsg('Authorization header is missing');
        }
        return $next($request);
    }
}
