<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\MyPage\DashboardController;
use App\Http\Controllers\MyPage\RewardsController;
use App\Http\Controllers\MyPage\MoviesWatchedController;
use App\Http\Controllers\MyPage\ReviewController as MyPageReviewController;
use App\Http\Controllers\MyPage\ReviewsWrittenController;
use App\Http\Controllers\MyPage\TicketController;
use App\Http\Controllers\MyPage\ProfileController;
use App\Http\Controllers\MyPage\CancelController;
use App\Http\Controllers\MyPage\CouponController;
use App\Http\Controllers\Api\NearByCinemasController;


Route::get('/', [HomeController::class, 'index']);

Auth::routes();

/**
 * Regular routes
 */
// home
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/movie/{movie}/release', [HomeController::class, 'release'])->name('release');
Route::get('/movie/{movie}/detail', [HomeController::class, 'movie_detail'])->name('movie_detail');
Route::get('/movies/search', [HomeController::class, 'home_search'])->name('movies.search');
Route::get('/movies/search_showtime', [HomeController::class, 'showtime_search'])->name('movies.search_showtime');
Route::get('/information', [HomeController::class, 'informationIndex'])->name('information.index');
Route::get('/information/{id}', [HomeController::class, 'informationDetail'])->name('information.detail');


//Movie showtime
Route::get('/home/showtime', [HomeController::class, 'showtime_display'])
    ->name('movie.showtime.display');
Route::get('/movies/search', [HomeController::class, 'search'])
    ->name('movies.search');
Route::get('/movies/{movie}/showtime-selection', [HomeController::class, 'showtime_selection'])
    ->name('reservations.showtime.selection');


//--------------------
// Reservation Routes
//--------------------
Route::get('/showtime_selection/{showtime}', [ReservationController::class, 'showtimeSelection'])
    ->name('reservations.showtimeSelection');
Route::get('/reservations/login-redirect',[ReservationController::class, 'loginRedirect'])
    ->name('reservations.login.redirect');
Route::post('/reservation/guest',[ReservationController::class, 'guest'])
    ->name('reservations.guest');
Route::get('/seat-selection/{showtime}', [ReservationController::class, 'seatSelection'])
    ->name('reservations.seat-selection');
Route::post('/seat-selection', [ReservationController::class, 'seatSelectionStore'])
    ->name('reservations.seat-selection.store');
Route::get('/ticket-type', [ReservationController::class, 'ticketType'])
    ->name('reservations.ticket-type');
Route::post('/save-ticket', [ReservationController::class, 'saveTicket'])
    ->name('reservations.save-ticket');
Route::get('/payment-method', [ReservationController::class, 'paymentMethod'])
    ->name('reservations.payment-method');
Route::post('/save-payment', [ReservationController::class, 'savePayment'])
    ->name('reservations.save-payment');
Route::post('/reservation-confirm', [ReservationController::class, 'confirmation'])
    ->name('reservations.confirm');
Route::post('/reservation-booking', [ReservationController::class, 'confirmBooking'])
    ->name('reservations.confirm-booking');
Route::get('/reservation-complete/{showtime}', [ReservationController::class, 'complete'])
    ->name('reservations.complete');

//--------------------
// Review Routes
//--------------------
Route::get('/movies/{movieId}/reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::post('/movies/{movieId}/reviews', [ReviewController::class, 'store'])->name('reviews.store')->middleware('auth');
Route::get('/movies/{movieId}/reviews/create', [ReviewController::class, 'create'])->name('reviews.create')->middleware('auth');
Route::get('/movies/{movieId}/reviews/{reviewId}', [ReviewController::class, 'show'])->name('reviews.show');
Route::get('/movies/{movieId}/reviews/{reviewId}/edit', [ReviewController::class, 'edit'])->name('reviews.edit')->middleware('auth');
Route::delete('/movies/{movieId}/reviews/{reviewId}', [ReviewController::class, 'destroy'])->name('reviews.destroy')->middleware('auth');
Route::put('/movies/{movieId}/reviews/{reviewId}', [ReviewController::class, 'update'])->name('reviews.update')->middleware('auth');

// ===========================
// API Routes (Public)
// ===========================
Route::get('/api/nearby-cinemas', [NearByCinemasController::class, 'getNearby']);
Route::get('/api/dynamic-pricing/{showtimeId}', [AdminController::class, 'getDynamicPricingStats']);


// ===========================
// Admin Routes
// ===========================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Movies Management
    Route::get('/movies', [AdminController::class, 'movies'])->name('movies');
    Route::get('/movies/create', [AdminController::class, 'createMovie'])->name('movies.create');
    Route::post('/movies', [AdminController::class, 'storeMovie'])->name('movies.store');
    Route::get('/movies/{id}/details', [AdminController::class, 'movieDetails'])->name('movies.details');
    Route::get('/movies/{id}/edit', [AdminController::class, 'editMovie'])->name('movies.edit');
    Route::put('/movies/{id}', [AdminController::class, 'updateMovie'])->name('movies.update');
    Route::get('/movies/{id}/showtimes', [AdminController::class, 'movieShowtimes'])->name('movies.showtimes');
    Route::post('/movies/{id}/showtimes/generate', [AdminController::class, 'generateShowtimes'])->name('movies.showtimes.generate');
    Route::delete('/showtimes/{id}', [AdminController::class, 'deleteShowtime'])->name('showtimes.delete');
   
    // Dynamic Pricing Management
    Route::get('/dynamic-pricing', [AdminController::class, 'showtimeDynamicPricing'])->name('dynamic-pricing');
    Route::get('/dynamic-pricing/{id}/edit', [AdminController::class, 'editShowtimeDynamicPrice'])->name('dynamic-pricing.edit');
    Route::put('/dynamic-pricing/{id}', [AdminController::class, 'updateShowtimeDynamicPrice'])->name('dynamic-pricing.update');

    // Analytics & Reports
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');

    // Reservations Management
    Route::get('/reservations', [AdminController::class, 'reservations'])->name('reservations');
    Route::get('/reservations/export', [AdminController::class, 'exportReservationsCsv'])->name('reservations.export');
    Route::get('/reservations/{id}/details', [AdminController::class, 'reservationDetails'])->name('reservations.details');

    // Users Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{id}/details', [AdminController::class, 'userDetails'])->name('users.details');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');

    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');

    // Coupons & Promotions
    Route::get('/coupons-promotions', [AdminController::class, 'couponsPromotions'])->name('coupons-promotions');
    Route::post('/coupons', [AdminController::class, 'storeCoupon'])->name('coupons.store');
    Route::put('/coupons/{id}/status', [AdminController::class, 'toggleCouponStatus'])->name('coupons.toggle-status');
    Route::post('/promotions', [AdminController::class, 'storePromotion'])->name('promotions.store');
    Route::put('/promotions/{id}/status', [AdminController::class, 'togglePromotionStatus'])->name('promotions.toggle-status');

    // Reviews Management
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
    Route::put('/reviews/{id}/toggle', [AdminController::class, 'toggleReview'])->name('reviews.toggle');
    Route::get('/information', [AdminController::class, 'information'])->name('information');

    //Information
    Route::get('/information/create', [AdminController::class, 'createInformation'])->name('information.create');
    Route::post('/information', [AdminController::class, 'storeInformation'])->name('information.store');
    Route::get('/information/{id}/edit', [AdminController::class, 'editInformation'])->name('information.edit');
    Route::put('/information/{id}', [AdminController::class, 'updateInformation'])->name('information.update');
    Route::get('/information/{id}/details', [AdminController::class, 'informationDetails'])->name('information.details');
    Route::delete('/information/{id}', [AdminController::class, 'deleteInformation'])->name('information.delete');
    //Information Category
    Route::get('/information/categories', [AdminController::class, 'informationCategories'])->name('information.categories');
    Route::post('/information/categories', [AdminController::class, 'storeInformationCategory'])->name('information.categories.store');
    Route::delete('/information/categories/{id}', [AdminController::class, 'deleteInformationCategory'])->name('information.categories.delete');

    Route::put('/information/categories/{category}', [AdminController::class, 'updateInformationCategory'])->name('information.categories.update');


});

// ===========================
// My Page Routes
// ===========================
Route::middleware('auth')->prefix('mypage')->name('mypage.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/rewards', [RewardsController::class, 'index'])->name('rewards');
    Route::get('/movies-watched', [MoviesWatchedController::class, 'index'])->name('movies-watched');
    Route::post('/movies-watched/{reservation}/send-review-email', [MoviesWatchedController::class, 'sendReviewEmail'])->name('movies-watched.send-review-email');
    Route::get('/reviews/create/{movie}', [MyPageReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews', [MyPageReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{id}', [MyPageReviewController::class, 'update'])->name('reviews.update');
    Route::get('/reviews-written', [ReviewsWrittenController::class, 'index'])->name('reviews-written');
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets');
    Route::get('/tickets/{id}/qrcode', [TicketController::class, 'showQrCode'])->name('tickets.qrcode');
    Route::get('/cancel/{id}', [CancelController::class, 'show'])->name('cancel.show');
    Route::post('/cancel/{id}', [CancelController::class, 'cancel'])->name('cancel.confirm');
    Route::get('/cancel/{id}/complete', [CancelController::class, 'complete'])->name('cancel.complete');
    Route::delete('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])
        ->name('reservations.cancel');
    Route::get('/coupons', [CouponController::class, 'index'])->name('coupons');
});
