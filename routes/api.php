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

Route::middleware('auth:api')->get(
    '/user', function (Request $request) {
        return $request->user();
    }
);


Route::middleware('auth:api')->group(
    function () {

        // routes for chat rooms
        Route::apiResource('rooms', 'RoomController');
        
        // routes for messages
        Route::apiResource('messages', 'MessageController');

        // simple list of all users
        Route::get('users', 'HomeController@usersList');

        // allow user to leave a room
        Route::post('rooms/{room}/leave', 'RoomController@leaveRoom');
    }
);
