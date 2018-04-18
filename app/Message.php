<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    protected $fillable = [ 'message' ];


    /**
     * Each message is owned by a user
     * 
     * @return model $user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }



    /**
     * Each message belongs to a chat room.
     * 
     * @return model $room
     */
    public function room()
    {
        return $this->belongsTo('App\Room');
    }


}
