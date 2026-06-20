<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


////////////// temporary (mirei)
    Route::get('/seat-selection', function () {
        return view('reservations.seat-selection');
    });   
    Route::get('/ticket-type-selection', function () {
        return view('reservations.ticket-type');
    });
//////////////