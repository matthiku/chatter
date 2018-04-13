<?php

use App\Events\MessagePosted;

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

Route::get(
    '/', function () {
        return view('chat');
    }
);


// get the CHAT room
Route::get(
    '/chat', function () {
        return view('chat');
    }
)->middleware('auth');


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




Auth::routes();


Route::get('/home', 'HomeController@index')->name('home');
