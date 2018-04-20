<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }



    /**
     * Provide simple list of all users
     *
     * @return \Illuminate\Http\Response
     */
    public function usersList()
    {
        return DB::table('users')->select('id', 'name', 'username', 'avatar')->get();
    }

}
