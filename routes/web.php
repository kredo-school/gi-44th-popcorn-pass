<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/seat-selection', function () {
    return view('reservations.seat-selection');
});   //temporary (mirei)

Auth::routes();
// ===========================
// Admin Routes
// ===========================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
});

/**
 * Regular routes
 */
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
