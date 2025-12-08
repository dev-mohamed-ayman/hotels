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
     * Format date and time in a beautiful and user-friendly way
     *
     * @param \Carbon\Carbon|string|null $date The date to format
     * @param bool $showTime Whether to show time or not
     * @return string Formatted date string
     */
    function formatDateTime($date, $showTime = true)
    {
        if (!$date) {
            return '-';
        }

        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }

        $now = \Carbon\Carbon::now();
        $diffInDays = $date->diffInDays($now);
        $diffInHours = $date->diffInHours($now);
        $diffInMinutes = $date->diffInMinutes($now);

        // Today
        if ($date->isToday()) {
            if ($showTime) {
                return __('Today at :time', ['time' => $date->format('H:i')]);
            }
            return __('Today');
        }

        // Yesterday
        if ($date->isYesterday()) {
            if ($showTime) {
                return __('Yesterday at :time', ['time' => $date->format('H:i')]);
            }
            return __('Yesterday');
        }

        // This week
        if ($diffInDays <= 7) {
            if ($showTime) {
                return $date->format('l') . ' ' . __('at') . ' ' . $date->format('H:i');
            }
            return $date->format('l');
        }

        // This year
        if ($date->isCurrentYear()) {
            if ($showTime) {
                return $date->format('M d') . ' ' . __('at') . ' ' . $date->format('H:i');
            }
            return $date->format('M d');
        }

        // Older dates
        if ($showTime) {
            return $date->format('M d, Y') . ' ' . __('at') . ' ' . $date->format('H:i');
        }
        return $date->format('M d, Y');
    }
}
