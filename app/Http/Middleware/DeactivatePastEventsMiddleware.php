<?php

namespace App\Http\Middleware;

use App\Models\Udalost;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class DeactivatePastEventsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (Schema::hasTable('udalosti')) {
                Udalost::deactivatePastEvents();
            }
        } catch (\Throwable) {
            // Ignore request-time database issues; the cleanup will retry on a later request.
        }

        return $next($request);
    }
}
