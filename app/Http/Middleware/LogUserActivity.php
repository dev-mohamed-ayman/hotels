<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log user activity after the request is processed
        if (Auth::check()) {
            $this->logActivity($request);
        }

        return $response;
    }

    private function logActivity(Request $request)
    {
        $user = Auth::user();
        $route = $request->route();
        
        if (!$route) {
            return;
        }

        $routeName = $route->getName();
        
        // Skip logging for certain routes to avoid noise
        $skipRoutes = [
            'dashboard.index',
            'profile.index',
            // Add other routes you want to skip
        ];

        if (\in_array($routeName, $skipRoutes)) {
            return;
        }

        // Log different types of activities
        $description = $this->getActivityDescription($routeName);
        
        if ($description) {
            activity()
                ->causedBy($user)
                ->withProperties([
                    'route' => $routeName,
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ])
                ->log($description);
        }
    }

    private function getActivityDescription($routeName)
    {
        $userName = Auth::user()->name;

        // Map route patterns to descriptions
        $patterns = [
            'login' => __('activity.successful_login', ['name' => $userName]),
            'logout' => __('activity.logout_by', ['name' => $userName]),
            'bookings.create' => __('activity.accessed_booking_creation', ['name' => $userName]),
            'bookings.store' => __('activity.created_new_booking', ['name' => $userName]),
            'bookings.edit' => __('activity.accessed_booking_edit', ['name' => $userName]),
            'bookings.update' => __('activity.updated_booking', ['name' => $userName]),
            'bookings.destroy' => __('activity.deleted_booking', ['name' => $userName]),
            'customers.create' => __('activity.accessed_customer_creation', ['name' => $userName]),
            'customers.store' => __('activity.created_new_customer', ['name' => $userName]),
            'customers.edit' => __('activity.accessed_customer_edit', ['name' => $userName]),
            'customers.update' => __('activity.updated_customer', ['name' => $userName]),
            'customers.destroy' => __('activity.deleted_customer', ['name' => $userName]),
            'users.create' => __('activity.accessed_user_creation', ['name' => $userName]),
            'users.store' => __('activity.created_new_user', ['name' => $userName]),
            'users.edit' => __('activity.accessed_user_edit', ['name' => $userName]),
            'users.update' => __('activity.updated_user', ['name' => $userName]),
            'users.destroy' => __('activity.deleted_user', ['name' => $userName]),
        ];

        return $patterns[$routeName] ?? null;
    }
}