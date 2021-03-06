<?php

namespace Larapacks\Authorization\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Run the request filter.
     *
     * @param Request $request
     * @param Closure $next
     * @param array   $roles
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roles = null)
    {
        $args = func_get_args();

        if ($args > 3) {
            // If we've been given more than one role, we
            // need to retrieve all of them from the method.
            $roles = array_slice($args, 2);
        }

        if (! $request->user()->hasRoles($roles)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
