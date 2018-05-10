<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RoomUser extends Pivot
{
    //
    protected $fillable = ['email_notification'];
}
