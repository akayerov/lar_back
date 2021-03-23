<?php


namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;  //App\Traits\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Support\Facades\Log;

class PrivateChat extends Event implements ShouldBroadcast
{
    use InteractsWithSockets, Dispatchable;

    public $data;
    public $created_at;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->created_at = date('Y-m-d H:i:s');

        $this->dontBroadcastToCurrentUser();
    }

    /**
     * @inheritDoc
     */
    public function broadcastOn()
    {
        return new PrivateChannel('room.' . $this->data['room_id']);
    }
}
