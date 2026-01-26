<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetTimezone
{
    public function handle(Request $request, Closure $next)
    {
        $timezone = $request->cookie('timezone', config('timezones.default', 'UTC'));

        // Validate timezone
        $supportedTimezones = config('timezones.supported', []);
        if (array_key_exists($timezone, $supportedTimezones)) {
            config(['app.timezone' => $timezone]);
            date_default_timezone_set($timezone);
        }

        return $next($request);
    }
}
