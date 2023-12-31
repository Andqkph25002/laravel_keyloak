<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateUserEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $token;
    public $email;
    public $userId;
    /**
     * Create a new event instance.
     */
    public function __construct($token, $email, $userId)
    {
        $this->token = $token;
        $this->email = $email;
        $this->userId = $userId;
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
