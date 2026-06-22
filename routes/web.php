<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});


Auth::routes();
// ===========================
// Admin Routes
// ===========================
Route::prefix('admin')->name('admin.')->group(function () {
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
});

/**
 * Regular routes
 */
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');