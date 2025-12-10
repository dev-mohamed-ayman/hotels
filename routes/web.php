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

            // Hotels Routes
            Route::post('hotels/quick-create', [\App\Http\Controllers\Admin\HotelController::class, 'quickCreate'])->name('hotels.quick-create');
            Route::resource('hotels', \App\Http\Controllers\Admin\HotelController::class);

            // Customers Routes
            Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class);

            // Follow-ups Routes
            Route::prefix('customers/{customer}/follow-ups')->name('follow-ups.')->controller(\App\Http\Controllers\Admin\FollowUpController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::put('/latest', 'updateLatest')->name('update-latest');
            });

            // Bookings Routes
            Route::post('bookings/{booking}/update-payment', [\App\Http\Controllers\Admin\BookingController::class, 'updatePayment'])->name('bookings.update-payment');
            Route::get('bookings/{booking}/pdf/customer', [\App\Http\Controllers\Admin\BookingController::class, 'downloadCustomerPdf'])->name('bookings.pdf.customer');
            Route::get('bookings/{booking}/pdf/system', [\App\Http\Controllers\Admin\BookingController::class, 'downloadSystemPdf'])->name('bookings.pdf.system');
            Route::get('bookings/{booking}/pdf/hotel', [\App\Http\Controllers\Admin\BookingController::class, 'downloadHotelPdf'])->name('bookings.pdf.hotel');
            Route::resource('bookings', \App\Http\Controllers\Admin\BookingController::class);

            // Logout
            Route::get('/logout', [DashboardController::class, 'logout'])->name('logout');
        });
    }
);
