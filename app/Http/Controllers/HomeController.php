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
        $this->middleware('auth')->except('index');
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($lang = null)
    {
        if ($lang) {
            \App::setLocale($lang);
            session(['lang' => $lang]);
        } else {
            \App::setLocale('en');
            session(['lang' => '']);
        }
        return view('home');
    }


    public function getLatestFrontendVersion()
    {
        return response(
            [
                'status' => 'OK',
                'frontendVersion' => filemtime(base_path().'/public/js/app.js')
            ]
        );
    }


}
