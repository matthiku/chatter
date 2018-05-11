<?php

namespace App\Mail;

use App\User;
use App\Room;
use App\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChatMessageReceived extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user and message instances.
     *
     * @return void
     */
    public $user;
    public $room;
    public $message;


    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct(User $user, Room $room, Message $message)
    {
        //
        $this->user = $user;
        $this->room = $room;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.chatMessageReceived');
    }
}
