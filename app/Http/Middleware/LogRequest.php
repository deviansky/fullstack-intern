<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog; // 1. Import model ActivityLog
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // 2. Import Auth
use Symfony\Component\HttpFoundation\Response;

class LogRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jalankan request terlebih dahulu
        $response = $next($request);

        // Hanya catat jika pengguna sudah login
        if (Auth::check()) {
            // 3. Buat entri baru di database
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $request->getMethod() . ' ' . $request->getPathInfo(),
                'description' => 'User ' . Auth::user()->name . ' accessed ' . $request->fullUrl(),
                'logged_at' => now()
            ]);
        }

        return $response;
    }
}