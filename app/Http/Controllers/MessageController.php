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

use Log;
use Auth;
use File;

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

            // return confirmation and latest frontend version timestamp
            return response(
                [
                    'status' => 'OK',
                    'frontendVersion' => filemtime(base_path().'/public/js/app.js')
                ]
            );
        }
        return 'failed!';
    }


    /**
     * Upload a file (photos, videos, documents) into this room
     *
     * @param \Illuminate\Http\Request $request HTTP request data
     * @param \App\Room                $room    Room Model data
     * 
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request, Room $room)
    {
        $user = Auth::user();

        // check if user is member of this room and for the file
        if ($user->isMemberOf($room->id) && $request->file('file')) {

            // get the file and store it in the images folder
            $image = $request->file('file');
            $filename = $image->getClientOriginalName();
            $extension = $image->getClientOriginalExtension();
            $name = time().$filename;

            // determine the proper file type
            switch ($extension) {
            case 'mp3': $type = 'audio';
                break;
            case 'aac': $type = 'audio';
                break;
            case 'amr': $type = 'audio';
                break;
            case 'mp4': $type = 'video';
                break;
            case 'png': $type = 'image';
                break;
            case 'gif': $type = 'image';
                break;
            case 'jpg': $type = 'image';
                break;
            case 'jpeg': $type = 'image';
                break;
            default: $type = explode('/', $image->getMimeType())[0]; // for other file extensions...
            }
            // if still not clear, we assume audio
            if (!$type) $type = 'audio'; 

            $image->move(public_path().'/images/', $name);

            // create a new message for this room
            $message = new Message(
                [
                    'message' => $filename,
                    'filename' => $name,
                    'filetype' => $type,
                ]
            );
            $message->user_id = $user->id;
            $room->messages()->save($message);

            // Set the update_at date in the pivot table
            // to indicate the reading progress of this user in this room
            $membership = $user->memberships()->where('room_id', $room->id)->first();
            $membership->pivot->touch();

            // inform all subscribers of this change
            broadcast(new RoomUpdated($room, $user));

            // Announce that a new message was posted 
            // - received and forwarded to the clients by the MessagePosted event
            broadcast(new MessagePosted($message, $user));

            return $name;
        }
        return 'failed!';
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

            // Delete file if there was one attached to this message
            if ($message->filename) {
                $path = public_path().'/images/';
                unlink($path.$message->filename);
                if (file_exists($path.$message->filename))
                    Log::info($path." file $message->filename was NOT deleted");
                else
                    Log::info($path." file $message->filename was deleted");                
            }

            // instead of actually deleting the message, we replace it with the date
            // it was created or last updated to avoid inconsistencies in the chat room
            $message->update(
                [
                    'message' => $message->updated_at,
                    'filename' => null,
                    'filetype' => null,
                ]
            );
            broadcast(new MessageUpdated($message, $user));

            return 'deleted';
        }
    }
}
