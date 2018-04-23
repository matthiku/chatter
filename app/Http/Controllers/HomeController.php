<?php
/**
 * Home Controller
 * 
 * @category Controller
 * @package  Chatter
 * @author   Matthias Kuhs <matthiku@yahoo.com>
 * @license  MIT http://mit.org
 * @link     http://github.org/matthiku/chatter
 */

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

/**
 * Handles some basic requests
 * 
 * @category  Class
 * @package   Chatter
 * @author    Matthias Kuhs <matthiku@yahoo.com>
 * @copyright 2018 Matthias Kuhs
 * @license   MIT http://mit.org
 * @link      http://github.org/matthiku/chatter
 */
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
