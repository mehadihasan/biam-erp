<?php

namespace App\Http\Middleware;

use App\Support\AdminModule;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SyncAdminModule
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->is('admin', 'admin/*')) {
            return $next($request);
        }

        $path = $request->path();

        if (str_contains($path, 'module-selector')) {
            AdminModule::set(null);
        } elseif (str_starts_with($path, 'admin/inventory')) {
            AdminModule::set(AdminModule::INVENTORY);
        } elseif (str_starts_with($path, 'admin/hostel')) {
            AdminModule::set(AdminModule::HOSTEL);
        }

        return $next($request);
    }
}
