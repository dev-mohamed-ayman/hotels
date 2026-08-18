<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;


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

            // Timezone Routes
            Route::post('timezone/change', [\App\Http\Controllers\Admin\TimezoneController::class, 'change'])->name('timezone.change');

            // Currencies Routes
            Route::resource('currencies', \App\Http\Controllers\Admin\CurrencyController::class);

            // Hotels Routes
            Route::post('hotels/quick-create', [\App\Http\Controllers\Admin\HotelController::class, 'quickCreate'])->name('hotels.quick-create');
            Route::resource('hotels', \App\Http\Controllers\Admin\HotelController::class);
            Route::post('hotels/{hotel}/wallet/transaction', [\App\Http\Controllers\Admin\WalletController::class, 'storeHotel'])->name('hotels.wallet.transaction');
            Route::get('hotels/{hotel}/wallet/export-pdf', [\App\Http\Controllers\Admin\WalletController::class, 'exportHotelWalletPdf'])->name('hotels.wallet.export-pdf');

            // Customers Routes
            Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class);
            Route::post('customers/{customer}/wallet/transaction', [\App\Http\Controllers\Admin\WalletController::class, 'store'])->name('customers.wallet.transaction');
            Route::put('wallet-transactions/{transaction}', [\App\Http\Controllers\Admin\WalletController::class, 'update'])->name('wallet.transactions.update');
            Route::delete('wallet-transactions/{transaction}', [\App\Http\Controllers\Admin\WalletController::class, 'destroy'])->name('wallet.transactions.destroy');
            Route::get('customers/{customer}/wallet/export-pdf', [\App\Http\Controllers\Admin\WalletController::class, 'exportWalletPdf'])->name('customers.wallet.export-pdf');

            // Follow-ups Routes
            Route::prefix('customers/{customer}/follow-ups')->name('follow-ups.')->controller(\App\Http\Controllers\Admin\FollowUpController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::put('/latest', 'updateLatest')->name('update-latest');
            });

            // Bookings Routes
            Route::post('bookings/{booking}/update-hotel-payment', [\App\Http\Controllers\Admin\BookingController::class, 'updateHotelPayment'])->name('bookings.update-hotel-payment');

            // Duplicate booking route
            Route::post('bookings/{booking}/duplicate', [\App\Http\Controllers\Admin\BookingController::class, 'duplicate'])->name('bookings.duplicate');

            // Toggle Payment Status Route
            Route::post('bookings/{booking}/toggle-status', [\App\Http\Controllers\Admin\BookingController::class, 'togglePaymentStatus'])->name('bookings.toggle-status');

            // Toggle Payment List Route
            Route::post('bookings/{booking}/toggle-payment-list', [\App\Http\Controllers\Admin\BookingController::class, 'togglePaymentList'])->name('bookings.toggle-payment-list');

            // Bulk export routes
            Route::get('bookings/export/bank', [\App\Http\Controllers\Admin\BookingController::class, 'exportBankPdf'])->name('bookings.export.bank');
            Route::get('bookings/export/detailed', [\App\Http\Controllers\Admin\BookingController::class, 'exportDetailedPdf'])->name('bookings.export.detailed');
            Route::get('bookings/export/guest', [\App\Http\Controllers\Admin\BookingController::class, 'exportGuestPdf'])->name('bookings.export.guest');
            Route::get('bookings/export/client', [\App\Http\Controllers\Admin\BookingController::class, 'exportClientPdf'])->name('bookings.export.client');
            Route::get('bookings/export/netrate', [\App\Http\Controllers\Admin\BookingController::class, 'exportNetRatePdf'])->name('bookings.export.netrate');
            Route::resource('bookings', \App\Http\Controllers\Admin\BookingController::class);

            // Booking History Routes
            Route::prefix('booking-history')->name('booking-history.')->controller(\App\Http\Controllers\Admin\BookingHistoryController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{booking}', 'show')->name('show');
                Route::delete('/{id}', 'destroy')->name('destroy');
                Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
                Route::delete('booking/{booking}/all', 'deleteAllForBooking')->name('delete-all');
            });

            // Users Routes
            Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

            // Roles Routes
            Route::prefix('roles')->name('roles.')->controller(\App\Http\Controllers\Admin\RolePermissionController::class)->group(function () {
                Route::get('/', 'rolesIndex')->name('index');
                Route::get('/create', 'rolesCreate')->name('create');
                Route::post('/', 'rolesStore')->name('store');
                Route::get('/{role}', 'rolesShow')->name('show');
                Route::get('/{role}/edit', 'rolesEdit')->name('edit');
                Route::put('/{role}', 'rolesUpdate')->name('update');
                Route::delete('/{role}', 'rolesDestroy')->name('destroy');
            });

            // Permissions Routes
            Route::prefix('permissions')->name('permissions.')->controller(\App\Http\Controllers\Admin\RolePermissionController::class)->group(function () {
                Route::get('/', 'permissionsIndex')->name('index');
                Route::get('/create', 'permissionsCreate')->name('create');
                Route::post('/', 'permissionsStore')->name('store');
                Route::get('/{permission}/edit', 'permissionsEdit')->name('edit');
                Route::put('/{permission}', 'permissionsUpdate')->name('update');
                Route::delete('/{permission}', 'permissionsDestroy')->name('destroy');
            });

            // Activity Log Routes
            Route::prefix('activity-log')->name('activity-log.')->controller(\App\Http\Controllers\Admin\ActivityLogController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{activity}', 'show')->name('show');
                Route::get('/booking/{bookingId}', 'showBookingHistory')->name('booking-history');
                Route::delete('/{activity}', 'destroy')->name('destroy');
                Route::post('/bulk-delete', 'bulkDelete')->name('bulk-delete');
                Route::get('/export/csv', 'export')->name('export');
            });

            // Logout
            Route::get('/logout', [DashboardController::class, 'logout'])->name('logout');
        });
    }
);
