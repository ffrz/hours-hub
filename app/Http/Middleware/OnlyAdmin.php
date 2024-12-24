<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Symfony\Component\HttpFoundation\Response;

class OnlyAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = FacadesAuth::user();
        if (!$user) {
            return response()->redirectToRoute('admin.auth.login');
        }

        if ($user->role !== User::Role_Admin) {
            return response()->redirectToRoute('admin.dashboard');
        }

        return $next($request);
    }
}
