<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Tangani permintaan masuk berdasarkan peran pengguna.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role !== $role) {
            $homeRoute = method_exists($user, 'homeRoute') ? $user->homeRoute() : ($user->role === 'admin' ? 'dashboard' : 'pengajuan.index');

            return redirect()->route($homeRoute);
        }

        return $next($request);
    }
}

