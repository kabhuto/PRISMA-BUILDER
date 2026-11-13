<?php

namespace App\Http\Middleware;

use App\Support\Access;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pages that run the app (API access, admins, templates, menu and tools, which forms can be used)
 * are for a superadmin only.
 */
class SuperadminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(Access::isSuperadmin(), 403, 'Halaman ini khusus superadmin.');

        return $next($request);
    }
}
