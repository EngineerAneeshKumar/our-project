<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('send-reset-password-email', [PasswordResetController::class, 'send_reset_email']);
Route::post('reset-password/{token}', [PasswordResetController::class, 'reset_password']);


Route::middleware('auth:sanctum')->group(function(){
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('thoughts', PostController::class);
    Route::put('updateUserProfile', [AuthController::class, 'update']);
    Route::post('updatePassword', [AuthController::class, 'change_password']);

    Route::get('getLoggedUser', [AuthController::class, 'get_logged_user_details']);
});


