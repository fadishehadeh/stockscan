<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();

        abort_unless($user, 403);

        $allowedRoles = explode('|', $roles);

        abort_unless(in_array($user->role, $allowedRoles), 403);

        return $next($request);
    }
}
