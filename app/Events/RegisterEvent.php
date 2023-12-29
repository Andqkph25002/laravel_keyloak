<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;

class RegisterEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
   
    public $email;
    public $username;
    public $password;
    public $name;
    /**
     * Create a new event instance.
     */
    public function __construct( $email, $username, $password , $name)
    {
       
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->name = $name;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
