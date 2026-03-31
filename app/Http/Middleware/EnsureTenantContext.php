<?php

namespace App\Http\Middleware;

use App\Support\Tenant\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantContext
{
    /**
     * Store the authenticated user's company in a small in-memory tenant manager.
     * Models use this later to apply company scoping automatically.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            app(TenantManager::class)->set($request->user()->company);
        }

        return $next($request);
    }
}
