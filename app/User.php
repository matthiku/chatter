<?php

namespace App;

use App\Room;
use Laravel\Passport\HasApiTokens;
use App\Notifications\VerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

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
     * A user can OWN many chat rooms
     * 
     * @return object rooms
     */
    public function rooms()
    {
        return $this->hasMany('App\Room', 'owner_id');
    }

    /**
     * A user can be MEMBER of many chat rooms
     * 
     * @return object memberships
     */
    public function memberships()
    {
        return $this
            ->belongsToMany('App\Room', 'room_user', 'user_id', 'room_id')
            ->withTimestamps()
            ->withPivot('email_notification')
            ->using('App\RoomUser');
    }


    /**
     * Check if user owns a certain room
     * 
     * @param Model $room Room object model
     * 
     * @return boolean
     */
    public function isOwner(Room $room)
    {
        return $this->id === $room->owner_id;
    }



    /**
     * Check if user is Member of a certain room
     * 
     * @param Model $room Room object model
     * 
     * @return boolean
     */
    public function isMemberOf($room_id)
    {
        return $this->memberships->contains('id', $room_id);
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
