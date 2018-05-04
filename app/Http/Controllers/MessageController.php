<?php
/**
 * Messages Controller
 * 
 * PHP version 7
 * 
 * @category Controller
 * @package  Chatter
 * @author   Matthias Kuhs <matthiku@yahoo.com>
 * @license  MIT http://mit.org
 * @link     http://github.org/matthiku/chatter * 
 */

namespace App\Http\Controllers;

use Auth;
use App\Room;
use App\Message;
use Illuminate\Http\Request;
use App\Events\RoomUpdated;
use App\Events\MessagePosted;
use App\Events\MessageUpdated;



/**
 * Handles all requests related to Chat Messages
 * 
 * @category  Class
 * @package   Chatter
 * @author    Matthias Kuhs <matthiku@yahoo.com>
 * @copyright 2018 Matthias Kuhs
 * @license   MIT http://mit.org
 * @link      http://github.org/matthiku/chatter
 */
class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Message::with('user')->get();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request HTTP request data
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // get user and create a message with the request payload
        $user = Auth::user();
        $message = $request->get('message');
        $room_id = $request->get('room_id');

        // check if we have a message and if the user is member of this room
        if (strlen($message) && $user->isMemberOf($room_id)) {
            $message = new Message(['message' => $message]);
            $message->user_id = $user->id;
            $room = Room::find($room_id);
            $room->messages()->save($message);

            // Set the update_at date in the pivot table
            // to indicate the reading progress of this user in this room
            $membership = $user->memberships()->where('room_id', $room_id)->first();
            $membership->pivot->touch();

            // inform all subscribers of this change
            broadcast(new RoomUpdated($room, $user));

            // Announce that a new message was posted 
            // - received and forwarded to the clients by the MessagePosted event
            broadcast(new MessagePosted($message, $user));

            // return all messages incl the new
            return ['status' => 'OK'];
        }
        return 'failed!';
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request HTTP request data
     * @param \App\Message             $message Message Model data
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Message $message Message Model data
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        // check if user owns this message
        $user = Auth::user();
        if ($user->id === $message->user_id) {
            // instead of actually deleting a message, we replace it with the date
            // it was created or last updated to avoid inconsistencies in the chat
            $message->update(['message' => $message->updated_at]);

            broadcast(new MessageUpdated($message, $user));
            return 'deleted';
        }
    }
}
