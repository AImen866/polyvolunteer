<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /** Пропускает только пользователей с одной из перечисленных ролей. */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        abort_unless($user && $user->hasRole(...$roles), 403, 'Недостаточно прав для этого раздела.');

        return $next($request);
    }
}
