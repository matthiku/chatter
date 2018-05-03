<?php
/**
 * Event should fire when a room was created
 * 
 * @category  Event
 * @package   Chatter
 * @author    Matthias Kuhs <matthiku@yahoo.com>
 * @copyright 2018 Matthias Kuhs
 * @license   MIT http://mit.org
 * @link      http://github.org/matthiku/chatter
 */

namespace App\Http\Controllers;

use Auth;
use App\Room;
use App\Events\RoomCreated;
use App\Events\RoomUpdated;
use App\Events\RoomDeleted;
use Illuminate\Http\Request;


/**
 * RoomCreated Event class
 * 
 * @category Event
 * @package  Chatter
 * @author   Matthias Kuhs <matthiku@yahoo.com>
 * @license  MIT http://mit.org
 * @link     http://github.org/matthiku/chatter
 */
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

        // get list of all chat rooms this user is member of and
        // make sure the most recently updated room is coming first
        $rooms = $user->memberships;

        // get the members and messages of each chat
        foreach ($rooms as $key => $rm) {
            $room = Room::find($rm->id);
            $rm->users = $room->users;
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
     * @param \App\Room $room Model
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Room $room)
    {
        // get another room object
        $rm = Room::find($room->id);
        $rm->messages = $room->messages;
        return $rm;
    }



    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request HTTP data
     * @param \App\Room                $room    Model
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Room $room)
    {
        // get current user
        $user = Auth::user();

        // check if user owns this room
        if (! $user->isOwner($room)) {
            return 'failed';
        }

        // update optional chatroom name
        if ($request->has('name')) {
            $room->name = $request->name;
            $room->save();
        }

        // attach/detach members to this chat room according to the request
        $existingUsers = $room->users->pluck('id')->all();
        $requestedUsers = $request->members;
        foreach ($requestedUsers as $member) {
            // do we have a new user for this room?
            if (!in_array($member, $existingUsers)) {
                // To indicate the reading progress of this user in this room,
                // set update_at in the pivot table to the creation date of this room
                $room->users()->attach($member, ['updated_at' => $room->created_at]);
            }
        }
        foreach ($existingUsers as $member) {
            // Do we have a user removed from this room?
            if (!in_array($member, $requestedUsers)) {
                $room->users()->detach($member);
            }
        }

        // In any case, add the current user to the list of members
        if (! in_array($user->id, $request->members)) {
            $room->users()->attach($user->id); 
        }

        // create a new broadcasted for this event
        broadcast(new RoomUpdated($room, $user));

        return $room;
    }



    /**
     * Allow a user to leave a chat room
     *
     * @param \App\Room $room Model
     *
     * @return \Illuminate\Http\Response
     */
    public function leaveRoom(Room $room)
    {
        // get current user
        $user = Auth::user();

        // the owner cannot desert his own room
        if ($user->isOwner($room)) {
            return 'failed';
        }

        // remove this user from the chat room
        $room->users()->detach($user->id);

        // create a new broadcasted for this event
        broadcast(new RoomUpdated($room, $user));

        return 'You have left this room!';
    }


    /**
     * Set reading progress of a user in a room
     *
     * @param \App\Room $room Model
     *
     * @return \Illuminate\Http\Response
     */
    public function setreading(Room $room)
    {
        // get current user
        $user = Auth::user();

        // Set the update_at date in the pivot table
        // to indicate the reading progress of the user in this room
        $membership = $room->users()->where('user_id', $user->id)->first();
        $membership->pivot->touch();

        // inform all subscribers of this change
        broadcast(new RoomUpdated($room, $user));

        return $room;
    }




    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Room $room Model
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Room $room)
    {
        // check if room exists and user owns this room
        if (!$room || $room->owner->id !== Auth::user()->id) {
            return;
        }

        // broadcast the deletion event
        \Log::info('RoomDeleted event prepared! id: ' . $room->id);
        broadcast(new RoomDeleted($room->id));

        // remove all attached users (chatroom members)
        $room->users()->detach();
        // delete all related messages
        $room->messages()->delete();
        // now delete the room (messages should be deleted by foreignKey/cascade)
        $room->delete();

        return 'deleted!';
    }
}
