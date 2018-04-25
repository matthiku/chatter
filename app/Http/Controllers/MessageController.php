<?php
/**
 * Messages Controller
 * 
 * @category Controller
 * @package  Chatter
 * @author   Matthias Kuhs <matthiku@yahoo.com>
 * @license  MIT http://mit.org
 * @link     http://github.org/matthiku/chatter
 */

namespace App\Http\Controllers;

use Auth;
use App\Message;
use Illuminate\Http\Request;
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
        if (strlen($message) && $user->isMemberOf($room_id)) {
            $message = new Message(['message' => $message]);
            $message->room_id = $request->room_id;
            $user->messages()->save($message);

            // Announce that a new message was posted 
            // (will be received by the MessagePosted event)
            broadcast(new MessagePosted($message, $user));

            // return all messages incl the new
            return ['status' => 'OK'];
        }
        return 'failed!';
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Message $message Message model data
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        //
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
