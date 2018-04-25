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
 * RoomUpdated Event
 * 
 * @category Event
 * @package  Chatter
 * @author   Matthias Kuhs <matthiku@yahoo.com>
 * @license  MIT http://mit.org
 * @link     http://github.org/matthiku/chatter
 */
class RoomUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Room
     * 
     * @var Room
     */
    public $room;
    /**
     * User
     * 
     * @var User
     */
    public $user;


    /**
     * Create a new event instance.
     * 
     * @param Room $room model
     * @param User $user model
     *
     * @return void
     */
    public function __construct(Room $room, User $user)
    {
        $this->room = $room;
        $this->room->users = $room->users;
        $this->room->messages = $room->messages;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('chatroom');
    }
}
