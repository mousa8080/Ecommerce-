<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,$permission, $guard): Response
    {
        if (!$request->user() || !$request->user()->hasPermissionTo($permission, $guard)) {

            return response()->json(['message' => 'you do not have permission to access this resource'], 403);
        }
        return $next($request);
    }
}
