<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookingController::class, 'show'])->name('booking.show');
Route::get('/booking/status', [BookingController::class, 'status'])->name('booking.status');
Route::post('/booking', [BookingController::class, 'store'])->middleware('throttle:10,1')->name('booking.store');
Route::delete('/booking/{id}', [BookingController::class, 'cancel'])->name('booking.cancel');

require __DIR__.'/dashboard.php';
