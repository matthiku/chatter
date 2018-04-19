<?php

namespace App\Http\Controllers;

use Auth;
use App\Message;
use Illuminate\Http\Request;
use App\Events\MessagePosted;

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
        if (strlen($message) && $request->has('room_id')) {
            $message = new Message(['message' => $message]);
            $message->room_id = $request->room_id;
            $user->messages()->save($message);

            // Announce that a new message was posted
            broadcast(new MessagePosted($message, $user));

            // return all messages incl the new
            return ['status' => 'OK'];
        }
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        //
    }
}
