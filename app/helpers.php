<?php

if (!function_exists('isActiveRoute')) {
    /**
     * Check if the current route matches the given route name
     * Used for single-level menu items
     *
     * @param string|array $routeName Route name or array of route names to check
     * @param string $output The class to return if active (default: 'active')
     * @return string Returns the output class if route is active, empty string otherwise
     */
    function isActiveRoute($routeName, $output = 'active')
    {
        $routeNames = is_array($routeName) ? $routeName : [$routeName];

        foreach ($routeNames as $name) {
            // Check for wildcard match (e.g., 'profile.*')
            if (str_contains($name, '*')) {
                $pattern = str_replace('*', '', $name);
                if (str_starts_with(request()->route()->getName(), $pattern)) {
                    return $output;
                }
            }

            // Check for exact match
            if (request()->routeIs($name)) {
                return $output;
            }
        }

        return '';
    }
}

if (!function_exists('isOpenMenu')) {
    /**
     * Check if menu should be open (for multi-level menus)
     * Adds 'open' class to parent menu item when any child is active
     *
     * @param array $routeNames Array of route names to check
     * @return string Returns 'open' if any child route is active, empty string otherwise
     */
    function isOpenMenu($routeNames)
    {
        return isActiveRoute($routeNames, 'open');
    }
}

if (!function_exists('formatDateTime')) {
    /**
     * Format a date/time value to a readable format
     *
     * @param \Carbon\Carbon|string|null $dateTime
     * @param string $format
     * @return string
     */
    function formatDateTime($dateTime, $format = 'Y-m-d H:i')
    {
        if (!$dateTime) {
            return '-';
        }

        if (is_string($dateTime)) {
            $dateTime = \Carbon\Carbon::parse($dateTime);
        }

        return $dateTime->format($format);
    }
}
