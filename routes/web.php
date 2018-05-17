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


/**
 * Authentication Routes
 */
Auth::routes();


Route::get(
    'verify/{token}', 'Auth\VerifyController@verifyEmail'
)->name('verify');

Route::get(
    'sendverifyemail', 'Auth\VerifyController@sendVerifyEmail'
)->name('sendVerifyEmail');

// Socialite Authentication provider routes
Route::get(
    'login/{provider}', 'Auth\LoginController@redirectToProvider'
);
Route::get(
    'login/{provider}/callback', 'Auth\LoginController@handleProviderCallback'
);


/**
 * Log Viewer for Admins
 */
Route::get(
    'logs',
    '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index'
)->middleware('auth');



/**
 * Basic Pages Routes
 */

Route::get('/home/{lang?}', 'HomeController@index')->name('home');

Route::get(
    '/', function () {
        return view('home');
    }
);
