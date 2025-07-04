<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !Auth::user()->status) {
            Auth::user()->tokens()->delete();
            return response()->json(['message' => 'Akun Anda tidak aktif.'], 403);
        }
        return $next($request);
    }
}
