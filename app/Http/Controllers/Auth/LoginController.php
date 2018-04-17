<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/chat';



    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }



    /**
     * Define the field that is used to identify a user
     * (By default, Laravel uses the email field for authentication)
     * 
     * @return fieldName
     */
    public function username()
    {
        return 'username';
    }


    /**
     * Redirect the user to the Socialite Provider authentication page.
     * 
     * @param string $provider provider name ('google', 'github' etc.)
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from the Socialite Provider.
     * 
     * @param string $provider provider name ('google', 'github' etc.)
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->user();
        
        // for API usage
        // $user = Socialite::driver($provider)->stateless()->user();  

        // dd($user)

        // OAuth Two Providers
        $token = $user->token;
        $refreshToken = $user->refreshToken; // not always provided
        $expiresIn = $user->expiresIn;
        // $user->getId();
        // $user->getAvatar();

        // check if the email already exists, then we just log in the user
        $finduser = User::where('email', $user->getEmail());
        if ($finduser->count()) {
            Auth::login($finduser->first());
            $status = 'Account verified by ';

        } else {
            // otherwise, create a new user
            $user = User::create(
                [
                    'name' => $user->getName(),
                    'username' => $user->getNickname(),
                    'email' => $user->getEmail(),
                    'password' => str_random(25),
                ]
            );
            $status = 'Account created using credentials from ';
        }

        return redirect()
            ->route('home')
            ->with('status', $status . $provider);
        
    }


}
