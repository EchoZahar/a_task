<?php

use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\LogoutController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\CustomerRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Public api routes
 */

Route::post('/login', [LoginController::class, 'signIn']);
Route::post('/register', [RegisterController::class, 'signUp']);

Route::controller(CustomerRequestController::class)->group(function ()  {
    Route::get('/requests', 'index');
    Route::post('/requests', 'store');
    Route::get('/requests/{id}', 'show');
    Route::get('/requests/search/{value}','searchByNameOrEmail');
});

/**
 * Protected api routes
 */
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::put('requests/{id}', [CustomerRequestController::class, 'update']);
    Route::delete('requests/{id}', [CustomerRequestController::class, 'destroy']);
    Route::post('/logout', [LogoutController::class, 'logout']);

    // check auth user (Bad method)
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
