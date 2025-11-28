<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;


Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {

        // Login Routes
        Route::name('login')->controller(LoginController::class)->middleware('guest')->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'login');
        });


        Route::middleware(['auth'])->group(function () {

            // Dashboard Routes
            Route::prefix('dashboard')->name('dashboard.')->controller(DashboardController::class)->group(function () {
                Route::get('/', 'index')->name('index');
            });

            // Profile Routes
            Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/update', 'update')->name('update');
                Route::post('/avatar', 'updateAvatar')->name('avatar');
                Route::post('/password', 'updatePassword')->name('password');
            });

            // Currencies Routes
            Route::resource('currencies', \App\Http\Controllers\Admin\CurrencyController::class);

            // Logout
            Route::get('/logout', [DashboardController::class, 'logout'])->name('logout');
        });
    }
);
