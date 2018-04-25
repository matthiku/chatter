<?php
/**
 * Event should fire when a room was deleted
 * 
 * @category  Event
 * @package   Chatter
 * @author    Matthias Kuhs <matthiku@yahoo.com>
 * @copyright 2018 Matthias Kuhs
 * @license   MIT http://mit.org
 * @link      http://github.org/matthiku/chatter
 */

namespace App\Events;

use App\User;
use App\Room;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * RoomDeleted Event
 * 
 * @category Event
 * @package  Chatter
 * @author   Matthias Kuhs <matthiku@yahoo.com>
 * @license  MIT http://mit.org
 * @link     http://github.org/matthiku/chatter
 */
class RoomDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Room
     * 
     * @var Room
     */
    public $room;


    /**
     * Create a new event instance.
     * 
     * @param Room $room model
     *
     * @return void
     */
    public function __construct($room)
    {
        $this->room = $room;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        \Log::info('RoomDeleted event should fire! id:' . $this->room);
        return new PresenceChannel(env('MAIN_CHATROOM_NAME', 'chatroom'));
    }
}
