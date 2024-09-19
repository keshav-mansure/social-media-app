<?php

use App\Http\Controllers\Api\Users\DeleteUserController;
use App\Http\Controllers\Api\Users\LoginController;
use App\Http\Controllers\Api\Users\RegisterController;
use App\Http\Controllers\Api\Users\UpdateUserProfileController;
use App\Http\Middleware\AuthKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::middleware(AuthKey::class)->group(function () {
    Route::post('login', LoginController::class);
    Route::post('register', RegisterController::class);
});

Route::middleware(['auth:api'])->group(function () {
    Route::put('users', UpdateUserProfileController::class);
    Route::delete('users', DeleteUserController::class);
});








