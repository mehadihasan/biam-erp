<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCadreAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('cadre_auth') !== true) {
            return redirect()->route('home');
        }

        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }
}
