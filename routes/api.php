<?php

use App\Http\Controllers\Api\Users\LoginController;
use App\Http\Controllers\Api\Users\RegisterController;
use App\Http\Middleware\AuthKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::middleware(AuthKey::class)->group(function () {
    Route::post('login', LoginController::class);
    Route::post('register', RegisterController::class);
});





// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');
