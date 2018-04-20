<?php

namespace App\Http\Controllers;

use Auth;
use App\Room;
use App\Events\RoomCreated;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Get a list of all chatrooms a user is member of
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get current user
        $user = Auth::user();
        // get list of all chat rooms this user is member of
        $rooms = $user->memberships;
        // get the members and messages of each chat
        foreach ($rooms as $key => $rm) {
            $room = Room::find($rm->id);
            $rm->members = $room->users;
            $rm->messages = $room->messages;
        }
        return $rooms;
    }



    /**
     * Create a new chat room and attach the members
     *
     * @param \Illuminate\Http\Request $request the HTTP request data
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // get current user
        $user = Auth::user();
        // create a new room with an optional name
        $room = new Room();
        if ($request->has('name')) {
            $room->name = $request->name;
        }
        // define the owner of the new room
        $user->rooms()->save($room);
        // also make the owner a member (other members can see the list of members)
        $room->users()->attach($user->id);

        // add other members to this chat room
        if ($request->has('members')) {
            $room->users()->attach($request->members);
        }

        // create a new broadcasted for this event
        broadcast(new RoomCreated($room, $user));
        
        return $room;
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function show(Room $room)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Room $room)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function destroy(Room $room)
    {
        //
    }
}
