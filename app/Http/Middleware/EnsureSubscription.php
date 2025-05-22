<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $subscriptionName)
    {
        if (!$request->user() || !$request->user()->subscribed($subscriptionName)) {
            return redirect('/subscribe')->with('error', 'You need to subscribe first.');
        }

        return $next($request);
    }

}
