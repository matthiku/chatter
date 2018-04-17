<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VerifyController extends Controller
{
    /**
     * Verify the email address of a new user
     * 
     * @param string $token Token as provided by the verification email
     * 
     * @return redirect
     */
    public function verifyEmail($token)
    {
        User::where('token', $token)
            ->firstOrFail() // produces an 404 error if it fails
            ->update(['token' => null]); // verify the user

        return redirect()
            ->route('home')
            ->with('success', 'Account verified');
    }
}
