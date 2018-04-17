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


// get all messages
Route::get(
    '/messages', function () {
        return App\Message::with('user')->get();
    }
)->middleware('auth');


// STORE a new message
Route::post(
    '/messages', function () {
        // get user and create a message with the request payload
        $user = Auth::user();
        $message = request()->get('message');
        if (strlen($message)) {
            $message = $user->messages()->create(['message' => $message]);

            // Announce that a new message was posted
            broadcast(new MessagePosted($message, $user));

            // return all messages incl the new
            return ['status' => 'OK'];
        }
    }
)->middleware('auth');

