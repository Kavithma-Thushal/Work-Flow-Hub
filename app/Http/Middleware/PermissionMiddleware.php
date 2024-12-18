<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\Enums\HttpStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, $permissionsList, $guard = null): Response
    {
        $authGuard = app('auth')->guard($guard);

        // If the user is not authenticated, throw unauthorized exception
        if ($authGuard->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        // Get user permissions
        $userPermissions = $authGuard->user()->getAllPermissions()->pluck('name')->toArray();

        // Convert permission list to an array if it's a string
        $permissions = is_array($permissionsList) ? $permissionsList : explode('|', $permissionsList);

        // Check if the user has at least one of the required permissions
        if (count(array_intersect($permissions, $userPermissions)) > 0) {
            return $next($request);  // If yes, continue with the request
        }

        // Log unauthorized access attempt for auditing purposes
        Log::warning('Unauthorized permission attempt by user: ' . $authGuard->user()->id);

        // Throw an exception if the user doesn't have the required permissions
        throw new HttpException(HttpStatus::FORBIDDEN, 'User does not have right permissions');
    }
}
