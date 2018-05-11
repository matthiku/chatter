<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];



    /**
     * Get the user who owns the chat room.
     * 
     * @return model $user
     */
    public function owner()
    {
        return $this->belongsTo('App\User');
    }

    
    /**
     * A room can have many members
     * 
     * @return collection $members
     */
    public function users()
    {
        return $this->belongsToMany('App\User')
            ->withTimestamps()
            ->withPivot('email_notification');
    }
    


    /**
     * A room can have many messages
     * 
     * @return collection $messages
     */
    public function messages()
    {
        return $this->hasMany('App\Message');
    }
    
}
