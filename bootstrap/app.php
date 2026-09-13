<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Explicit auth redirect targets. Without these, an authenticated
        // visitor hitting the login page falls through to the framework's
        // name-based guess ("dashboard"/"home"), which does not exist here
        // (the route is "dashboard.index"), so it bounces to "/" — and with
        // a non-default locale in session the localization middleware sends
        // "/ar" right back, creating a redirect loop.
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('dashboard.index'));

        $middleware->alias([
            /**** OTHER MIDDLEWARE ALIASES ****/
            'localize' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
            /**** SPATIE PERMISSION MIDDLEWARE ****/
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            /**** ACTIVITY LOG MIDDLEWARE ****/
            'log.activity' => \App\Http\Middleware\LogUserActivity::class,
            /**** TIMEZONE MIDDLEWARE ****/
            'timezone' => \App\Http\Middleware\SetTimezone::class,
        ]);

        // Add global middleware for authenticated users
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\LogUserActivity::class,
            \App\Http\Middleware\SetTimezone::class,
            \App\Http\Middleware\SweepMissedBookings::class,
        ]);
    })
    ->withSchedule(function ($schedule) {
        // Clean old activity logs daily at 2 AM
        $schedule->command('activity-log:clean --days=90')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->runInBackground();

        // The shared host has no scheduler, so SweepMissedBookings middleware
        // does this on the first request of each day instead. Kept here so the
        // sweep moves onto cron by itself if one ever becomes available.
        $schedule->command('bookings:mark-missed')
            ->dailyAt('00:05')
            ->withoutOverlapping();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
