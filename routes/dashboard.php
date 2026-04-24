<?php

// dashboard routes

use App\Http\Controllers\Dashboard\ActivityLogController;
use App\Http\Controllers\Dashboard\AidDistributionController;
use App\Http\Controllers\Dashboard\BookingController as DashboardBookingController;
use App\Http\Controllers\Dashboard\ConstantController;
use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\SettingsController;
use App\Http\Controllers\Dashboard\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '',
    'middleware' => ['check.cookie'],
    'as' => 'dashboard.',
], function () {
    /* ********************************************************** */

    // Dashboard ************************
    Route::get('dashboard', [HomeController::class, 'index'])->name('home');
    Route::post('dashboard/refresh-cache', [HomeController::class, 'refreshDashboardCache'])->name('home.refresh-cache');

    // Logs ************************
    Route::get('logs', [ActivityLogController::class, 'index'])->name('logs.index');
    Route::get('getLogs', [ActivityLogController::class, 'getLogs'])->name('logs.getLogs');

    // users ************************
    Route::get('profile/settings', [UserController::class, 'settings'])->name('profile.settings');

    Route::get('aid-distributions-filters/{cloumn}', [AidDistributionController::class, 'getFilterOptions'])->name('aid-distributions.filters');
    Route::post('aid-distributions/export-excel', [AidDistributionController::class, 'exportExcel'])->name('aid-distributions.export-excel');

    Route::get('bookings', [DashboardBookingController::class, 'index'])->name('bookings.index');
    Route::post('bookings/{id}/confirm', [DashboardBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::get('bookings/export', [DashboardBookingController::class, 'export'])->name('bookings.export');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/day-config', [SettingsController::class, 'updateDayConfig'])->name('settings.day-config');

    /* ********************************************************** */

    // Resources

    Route::resource('constants', ConstantController::class)->only(['index', 'store', 'destroy']);

    Route::resources([
        'users' => UserController::class,
        'aid-distributions' => AidDistributionController::class,
    ]);
});
