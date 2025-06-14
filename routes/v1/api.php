<?php

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ModelSettingsController;
use App\Http\Controllers\Api\PrivilegeController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\API\TripAdvisorController;
use App\Http\Controllers\Api\TripAdvisorNewController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\QnaController;
use App\Http\Controllers\Api\TemplateWebController;

Route::get('/locations', [TripAdvisorNewController::class, 'autoCompleteLocation']);
Route::get('/hotels', [TripAdvisorNewController::class, 'searchHotels']);
Route::get('/restaurant', [TripAdvisorNewController::class, 'searchRestaurant']);
Route::get('/vacation-rental', [TripAdvisorNewController::class, 'searchVacationRental']);
Route::get('/airport', [TripAdvisorNewController::class, 'searchAirport']);
Route::get('/attractions', [TripAdvisorNewController::class, 'getAttractions']);


Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::get('logout', [UserController::class, 'logout']);
Route::get('verify-token', [UserController::class, 'checkToken']);
Route::post('forgot-password', [UserController::class, 'forgotPassword']);
Route::post('reset-password/{token}', [UserController::class, 'resetPassword']);

Route::get('/models', [ChatController::class, 'models']);

Route::post('/chatbot', [ChatController::class, 'chatBot']);

Route::get('users-export', [UserController::class, 'export']);
Route::post('users-import', [UserController::class, 'import']);
Route::get('users-template', [UserController::class, 'downloadTemplate']);

// Note (Masih ambigu setnya setelah chat dimulai tapi udah di set default 24jam chat expirednya)
Route::post('/chat/set-expiry', [ChatController::class, 'setChatExpiry']);


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
    Route::post('/chat', [ChatController::class, 'chat2']);
    Route::get('/saved-models', [ChatController::class, 'savedModels']);

    Route::get('/model-settings', [ModelSettingsController::class, 'index']);
    Route::post('/model-settings', [ModelSettingsController::class, 'store']);
    Route::get('/model-settings/{id}', [ModelSettingsController::class, 'show']);
    Route::put('/model-settings/{id}', [ModelSettingsController::class, 'update']);
    Route::delete('/model-settings/{id}', [ModelSettingsController::class, 'destroy']);

    Route::get('/chat/sessions', [ChatController::class, 'sessions']);
    Route::get('/chat/history/{sessionId}', [ChatController::class, 'history']);
    Route::post('/chat/continue', [ChatController::class, 'continueChat']);
    Route::delete('/chat/sessions/{sessionId}', [ChatController::class, 'deleteSession']);

    Route::get('profile', [UserController::class, 'profile']);
    Route::post('profile', [UserController::class, 'updateProfile']);

    Route::put('user/change-password', [UserController::class, 'changePass']);
    Route::put('user/change-password/{id}', [UserController::class, 'adminChangePass']);

    Route::post('role/checker/check-name-exists', [RoleController::class, 'checkIsNameExists']);
    Route::get('roles', [RoleController::class, 'listRoles']);

    Route::get('privileges', [PrivilegeController::class, 'dataList']);

    Route::get('qna/paginate', [QnaController::class, 'getPaginate']);
    Route::get('qna/{id}', [QnaController::class, 'get']);
    Route::post('qna', [QnaController::class, 'create']);
    Route::put('qna/{id}', [QnaController::class, 'update']);
    Route::delete('qna/{id}', [QnaController::class, 'delete']);

    Route::get('template-web/paginate', [TemplateWebController::class, 'getPaginate']);
    Route::get('template-web-list', [TemplateWebController::class, 'getList']);
    Route::get('template-web/{id}', [TemplateWebController::class, 'get']);
    Route::post('template-web', [TemplateWebController::class, 'create']);
    Route::put('template-web/{id}', [TemplateWebController::class, 'update']);
    Route::delete('template-web/{id}', [TemplateWebController::class, 'delete']);
});
