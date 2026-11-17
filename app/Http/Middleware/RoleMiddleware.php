<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        if (empty($roles) || in_array($user->role, $roles, true)) {
            return $next($request);
        }

        // Unauthorized role redirect according to actual role
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard')->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk halaman tersebut.'),
            'walikelas' => redirect()->route('walikelas.dashboard')->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk halaman tersebut.'),
            'kepsek' => redirect()->route('kepsek.dashboard')->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk halaman tersebut.'),
            default => redirect()->route('login')->with('error', 'Role pengguna tidak valid.'),
        };
    }
}

