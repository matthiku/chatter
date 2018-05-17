<?php

namespace App\Http\Controllers\Auth;

use Log;
use Auth;
use App\User;
use Socialite;
use App\Events\UsersChanged;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
    protected $redirectTo = '/home';



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
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        if (session('lang')) {
            \App::setLocale(session('lang'));
        }
        return view('auth.login');
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
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // only for new registrations ...
        // Mail::to('matthiku@example.com')->send(new UserRegistered($user));
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
        $providerData = Socialite::driver($provider)->user();
        
        // for API usage
        // $providerData = Socialite::driver($provider)->stateless()->user();  

        // OAuth Two Providers
        $token = $providerData->token;
        $refreshToken = $providerData->refreshToken; // not always provided
        $expiresIn = $providerData->expiresIn;
        // $providerData->getId();

        $status = $this->userFindOrCreate($providerData, $provider);

        return redirect()
            ->route('home')
            ->with('status', $status . $provider);
        
    }


    public function userFindOrCreate($providerData, $provider)
    {
        // check if the email already exists, then we just log in the user
        $email = $providerData->getEmail();
        $user = User::where('email', $email)->first();
        if ($user) {
            $status = 'Account verified by '.$provider;
        } else {
            // otherwise, create a new user with a random password

            // but first make sure we have a unique username
            $userName = $providerData->getNickname(); // hopefully one from the provider
            $firstName = explode(' ', $providerData->getName())[0];
            $emailName = explode('@', $providerData->getEmail())[0];
            Log::info("nick: $userName - Name: $firstName - email: $emailName");
            // otherwise the firstname
            if (!$userName) {
                $userName = $firstName;
            }
            $testDuplicate = User::where('username', $userName)->get();
            // if duplicate, try the email address name
            if ($testDuplicate) {
                $userName = $emailName;
                $testDuplicate = User::where('username', $userName)->get();
                // last resort is the email address
                if ($testDuplicate) {
                    $userName = $email; // this definitively is unique
                }
            }
            
            // create the new USER RECORD
            $user = User::create(
                [
                    'name' => $providerData->getName(),
                    'username' => $userName,
                    'email' => $email,
                    'avatar' => $providerData->getAvatar(),
                    'provider_id' => $providerData->getId(),
                    'provider_name' => $provider,
                    'password' => str_random(25),
                ]
            );
            $status = 'Account created using credentials from ';

            // broadcast the change in the users table
            broadcast(New UsersChanged());
        }

        // in any case, log in the user now
        Auth::login($user, true);

        Log::info($user->email . ' - ' . $status);

        return $status;
    }
}
