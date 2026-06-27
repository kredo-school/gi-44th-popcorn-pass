<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyPage\DashboardController;
use App\Http\Controllers\MyPage\RewardsController;

Route::get('/', [HomeController::class, 'index']);

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

//Movie showtime
Route::get('/home/showtime', [HomeController::class, 'showtime_display'])->name('movie.showtime.display');


////////////// temporary (mirei)
    Route::get('/seat-selection', function () {
        return view('reservations.seat-selection');
    });   
    Route::get('/ticket-type-selection', function () {
        return view('reservations.ticket-type');
    });
    Route::get('/payment-method' , function() {
        return view('reservations.payment-method');
    });
    Route::get ('/reservation-confirm', function () {
        return view('reservations.reservation-confirm');
    });
Route::get('/reservation-complete', function () {
    return view('reservations.reservation-complete');
});
//////////////


// ===========================
// Admin Routes
// ===========================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/movies', [AdminController::class, 'movies'])->name('movies');
    Route::get('/movies/create', [AdminController::class, 'createMovie'])->name('movies.create');
    Route::post('/movies', [AdminController::class, 'storeMovie'])->name('movies.store');
    Route::get('/movies/{id}/details', [AdminController::class, 'movieDetails'])->name('movies.details');
    Route::get('/movies/{id}/edit', [AdminController::class, 'editMovie'])->name('movies.edit');
    Route::put('/movies/{id}', [AdminController::class, 'updateMovie'])->name('movies.update');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
    Route::get('/reservations', [AdminController::class, 'reservations'])->name('reservations');
    Route::get('/reservations/export', [AdminController::class, 'exportReservationsCsv'])->name('reservations.export');
    Route::get('/reservations/{id}/details', [AdminController::class, 'reservationDetails'])->name('reservations.details');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{id}/details', [AdminController::class, 'userDetails'])->name('users.details');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    Route::get('/coupons-promotions', [AdminController::class, 'couponsPromotions'])->name('coupons-promotions');
    Route::post('/coupons', [AdminController::class, 'storeCoupon'])->name('coupons.store');
    Route::put('/coupons/{id}/status', [AdminController::class, 'toggleCouponStatus'])->name('coupons.toggle-status');
    Route::post('/promotions', [AdminController::class, 'storePromotion'])->name('promotions.store');
    Route::put('/promotions/{id}/status', [AdminController::class, 'togglePromotionStatus'])->name('promotions.toggle-status');
});

// ===========================
// My Page Routes
// ===========================
Route::middleware('auth')->prefix('mypage')->name('mypage.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/rewards', [RewardsController::class, 'index'])->name('rewards');
});

/**
 * Regular routes
 */
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');