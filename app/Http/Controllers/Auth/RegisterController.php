<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Events\UsersChanged;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;


    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';



    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }




    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        if (session('lang')) {
            \App::setLocale(session('lang'));
        }
        return view('auth.register');
    }



    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data login form data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make(
            $data, [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'username' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]
        );
    }



    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data form data
     *
     * @return \App\User
     */
    public function create(array $data)
    {
        $user = User::create(
            [
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'token' => str_random(25),
            ]
        );


        // broadcast the change in the users table
        broadcast(New UsersChanged());
       

        $user->sendVerificationEmail();

        return $user;
    }


}
