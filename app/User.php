<?php

namespace App;

use App\Notifications\VerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'token',
        'provider_id',
        'provider_name',
        'avatar'
    ];



    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];



    /**
     * A user can have many messages
     * 
     * @return object messages
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }


    /**
     * Returns true if the user's email has been verified
     * 
     * @return bool user is verified
     */
    public function isVerified()
    {
        return $this->token === null;
    }


    /**
     * Send email address verification email
     * 
     * @return void
     */
    public function sendVerificationEmail()
    {
        $this->notify(new VerifyEmail($this));
    }

}
