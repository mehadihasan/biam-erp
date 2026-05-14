<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Http\Request;

class AuthenticateFilamentOrCadre extends Authenticate
{
    public function handle($request, Closure $next, ...$guards)
    {
        $response = parent::handle($request, $next, ...$guards);

        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }

    /**
     * @param  array<string>  $guards
     */
    protected function authenticate($request, array $guards): void
    {
        if ($this->isCadreAllowed($request)) {
            return;
        }

        parent::authenticate($request, $guards);
    }

    protected function redirectTo($request): ?string
    {
        return route('home');
    }

    private function isCadreAllowed(Request $request): bool
    {
        return $request->session()->get('cadre_auth') === true
            && $request->is('admin/hostel/bookings/new', 'admin/hostel/rooms/detail');
    }
}
