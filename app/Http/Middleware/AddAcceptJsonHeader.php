<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AddAcceptJsonHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info("test json middleware");
        // Add the Accept: application/json header if it's not present
        // if (!$request->hasHeader('Accept')) {
            $request->headers->set('Accept', 'application/json');
        // }

        return $next($request);
    }
}
