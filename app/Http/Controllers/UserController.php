<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\User;
use App\Events\UsersChanged;
use Illuminate\Http\Request;

class UserController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return DB::table('users')->select('id', 'name', 'username', 'avatar')->get();
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // only the logged in user can update their data
        $user = Auth::user();

        // update user name
        if ($request->has('username')) {
            $user->username = $request->username;
            $user->save();

            // broadcast the change in the users table
            broadcast(New UsersChanged());

            return $user;
        }
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
