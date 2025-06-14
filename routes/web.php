<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/admin', function () {
    return view('app');
})->where('any', '.*');
Route::get('/admin/{any}', function () {
    return view('app');
})->where('any', '.*');

Route::get('/{any}', function () {
    return view('customer_app');
})->where('any', '.*');