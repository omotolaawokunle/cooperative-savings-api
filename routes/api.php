<?php

use Illuminate\Http\Request;

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

Route::post('/register', 'UserController@register');
Route::post('/login', 'UserController@login');

Route::group(['middleware' => 'auth.jwt'], function () {
    Route::post('/logout', 'UserController@logout');
    Route::get('/groups', 'GroupController@index');
    Route::get('/groups/join/{group}', 'GroupController@join');
    Route::post('/groups/add_user/{group}', 'GroupController@addUserToGroup');
    Route::post('/groups', 'GroupController@store');
    Route::get('/groups/{group}/users', 'GroupController@list');
});
