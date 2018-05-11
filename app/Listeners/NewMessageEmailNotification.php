<?php

namespace App\Listeners;

use Log;
use Mail;
use Pusher;
use App\User;
use App\Room;
use App\Events\MessagePosted;
use App\Mail\ChatMessageReceived;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewMessageEmailNotification
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MessagePosted  $event
     * @return void
     */
    public function handle(MessagePosted $event)
    {
        // the event contains a message and it's author
        $author = $event->user;
        $message = $event->message;
        $room = Room::find($message->room->id);
        $members = $room->users; // all members of this room

        // get list of currently online users
        $pusher = new Pusher\Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            array('cluster' => env('PUSHER_APP_CLUSTER'))
        );
        $users = [];
        $response = $pusher->get( '/channels/presence-'.env('MAIN_CHATROOM_NAME', 'chatroom').'/users' );
        if( $response['status'] == 200 ) {
            // convert to associative array for easier consumption
            // $users = json_decode( $response[ 'body' ], true )['users'];
            $users = $response['result']['users'];
        }
        $online_users = [];
        foreach ($users as $user) {
            array_push($online_users, $user['id']);
        }
        
        // check for each room member if they want to have email notifications sent
        foreach ($members as $member) {
            // no notification for the sender - for others only when offline
            if ($author->id !== $member->id && $member->pivot->email_notification) {
                // check if this member is NOT online
                if (!array_search($member->id, $online_users)) {
                    // send the notification
                    Log::info( 'sending email notification to '. $member->username );
                    Mail::to( $member->email )
                        ->send( new ChatMessageReceived($author, $room, $message, $room) );
                }
            }
        }
    }

}
