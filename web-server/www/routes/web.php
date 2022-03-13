<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\registerController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('{all}', '\App\Http\Controllers\GatewayController@send' )->where('all','^(?!string1$|string2$)([a-zA-Z0-9-]+)');;
Route::post('{all}', '\App\Http\Controllers\GatewayController@send' )->where('all','^(?!string1$|string2$)([a-zA-Z0-9-]+)');;