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

// Route::middleware('auth:api')->get(
//     '/user', function (Request $request) {
//         return $request->user();
//     }
// );


Route::middleware('auth:api')->group(
    function () {

        // routes for chat rooms
        Route::apiResource('users', 'UserController');

        // routes for chat rooms
        Route::apiResource('rooms', 'RoomController');
        
        // routes for messages
        Route::apiResource('messages', 'MessageController');

        // allow user to leave a room
        Route::post('rooms/{room}/leave', 'RoomController@leaveRoom');

        // set reading progress of a user in a room
        Route::post('rooms/{room}/setreading', 'RoomController@setreading');

        // set reading progress of a user in a room
        Route::post('rooms/{room}/setemailnotification', 'RoomController@setemailnotification');

        // indicate that a user starts typing a message in a room
        Route::post('rooms/{room}/typing', 'RoomController@typing');
    }
);
