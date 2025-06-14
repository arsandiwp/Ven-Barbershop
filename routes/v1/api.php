<?php

use App\Http\Controllers\Api\PrivilegeController;
use App\Http\Controllers\Api\RoleController;
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


Route::group(['middleware' => ['auth.redis', 'access.control']], function () {
    Route::get('user/paginate', [UserController::class, 'getPaginate']);
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
