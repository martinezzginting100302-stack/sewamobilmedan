<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next, string $role = 'admin'): Response
    {
        if (! $request->user() || $request->user()->role !== $role) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Anda tidak memiliki izin untuk resource ini.',
                ], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
        }

        return $next($request);
    }
}