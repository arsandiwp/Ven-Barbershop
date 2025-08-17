<?php

use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\PrivilegeController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\UserController;


Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::get('logout', [UserController::class, 'logout']);
Route::get('verify-token', [UserController::class, 'checkToken']);
Route::post('forgot-password', [UserController::class, 'forgotPassword']);
Route::post('reset-password/{token}', [UserController::class, 'resetPassword']);


Route::get('users-export', [UserController::class, 'export']);
Route::post('users-import', [UserController::class, 'import']);
Route::get('users-template', [UserController::class, 'downloadTemplate']);

// Service
Route::get('services/paginate', [ServiceController::class, 'getPaginate']);
Route::get('services/{id}', [ServiceController::class, 'get']);

Route::get('services', [ServiceController::class, 'listAllService']);
Route::post('services', [ServiceController::class, 'create']);
Route::post('services/{id}', [ServiceController::class, 'update']);
Route::delete('services/{id}', [ServiceController::class, 'delete']);


// News
Route::get('news/paginate', [NewsController::class, 'getPaginate']);
Route::get('news/{identifier}', [NewsController::class, 'getDetail']);

Route::post('news', [NewsController::class, 'create']);
Route::post('news/{id}', [NewsController::class, 'update']);
Route::delete('news/{id}', [NewsController::class, 'delete']);


// Reservation
Route::get('reservations', [ReservationController::class, 'getPaginate']);
Route::get('reservations/{id}', [ReservationController::class, 'get']);
Route::get('reservations/available-slots', [ReservationController::class, 'availableSlots']);


Route::get('reservations/get-all', [ReservationController::class, 'listAll']);
Route::post('reservations', [ReservationController::class, 'create']);
Route::put('reservations/{id}', [ReservationController::class, 'update']);
Route::delete('reservations/{id}', [ReservationController::class, 'delete']);

Route::get('user/paginate', [UserController::class, 'getPaginate']);
Route::get('barbers/paginate', [UserController::class, 'getBarberPaginate']);


Route::group(['middleware' => ['auth.redis', 'access.control']], function () {
    Route::get('user/{id}', [UserController::class, 'get']);
    Route::post('user', [UserController::class, 'create']);
    Route::post('user/{id}', [UserController::class, 'update']);
    Route::delete('user/{id}', [UserController::class, 'delete']);
    Route::put('user/restore/{id}', [UserController::class, 'restore']);

    Route::get('role/paginate', [RoleController::class, 'getPaginate']);
    Route::get('role/{id}', [RoleController::class, 'get']);
    Route::post('role', [RoleController::class, 'create']);
    Route::put('role/{id}', [RoleController::class, 'update']);
    Route::delete('role/{id}', [RoleController::class, 'delete']);
    Route::put('role/restore/{id}', [RoleController::class, 'restore']);

    Route::get('privilege/paginate', [PrivilegeController::class, 'getPaginate']);
});

Route::group(['middleware' => ['auth.redis']], function () {
    Route::get('profile', [UserController::class, 'profile']);
    Route::post('profile', [UserController::class, 'updateProfile']);

    Route::put('user/change-password', [UserController::class, 'changePass']);
    Route::put('user/change-password/{id}', [UserController::class, 'adminChangePass']);

    Route::post('role/checker/check-name-exists', [RoleController::class, 'checkIsNameExists']);
    Route::get('roles', [RoleController::class, 'listRoles']);

    Route::get('privileges', [PrivilegeController::class, 'dataList']);

});
