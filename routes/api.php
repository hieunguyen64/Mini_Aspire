<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace("App\Http\Controllers")->group(function () {
    Route::prefix("user")->group(function () {
        Route::post("register", "UserController@register");
        Route::post("login", "UserController@authenticate");

        Route::group(["middleware" => ["jwt.verify"]], function () {
            Route::get("/", "UserController@getAuthenticatedUser");
            Route::resource("loans", "LoansController");
        });
    });
});
