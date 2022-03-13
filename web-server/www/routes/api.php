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

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::group([
    'prefix' => 'v1/auth'
], function ($router) {
    Route::post('register', [\App\Http\Controllers\Api\v1\AuthController::class, 'register'])->name('register');
    Route::post('login', [\App\Http\Controllers\Api\v1\AuthController::class, 'login'])->name('login');
    Route::post('loginExt', [\App\Http\Controllers\Api\v1\AuthController::class, 'loginExt'])->name('loginExt');
    Route::post('logout', [\App\Http\Controllers\Api\v1\AuthController::class, 'logout'])->name('logout');
    Route::post('refresh', [\App\Http\Controllers\Api\v1\AuthController::class, 'refresh'])->name('refresh');
    Route::post('me', [\App\Http\Controllers\Api\v1\AuthController::class, 'me'])->name('me');
    Route::post('getUserById', [\App\Http\Controllers\Api\v1\AuthController::class, 'getUserById'])->name('getUserById');
    Route::post('registerExtranetUser', [\App\Http\Controllers\Api\v1\RegisterController::class, 'register']);
});

Route::group([
    'prefix' => 'v1'
], function ($router) {
    Route::get('/ping',  '\App\Http\Controllers\Api\v1\PingController@ping');
    Route::post('/ping',  '\App\Http\Controllers\Api\v1\PingController@ping');
    Route::get('{all}', '\App\Http\Controllers\Api\v1\ApiGatewayController@send' )->where('all','^(?!string1$|string2$)([a-zA-Z0-9-]+)');
    Route::post('{all}', '\App\Http\Controllers\Api\v1\ApiGatewayController@send' )->where('all','^(?!string1$|string2$)([a-zA-Z0-9-]+)');
});
