<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check() || auth()->user()->role !== $role) {
            abort(403, 'Akses Ditolak. Halaman ini hanya untuk pengguna dengan peran ' . ucfirst($role) . '.');
        }

        return $next($request);
    }
}
