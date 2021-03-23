<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable; //App\Traits\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Support\Facades\Log;

class MessageEvent implements ShouldBroadcast
{
    use InteractsWithSockets, Dispatchable;

    public $message;
    public $created_at;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
        $this->created_at = date('Y-m-d H:i:s');

 //       $this->dontBroadcastToCurrentUser();
    }

    /**
     * @inheritDoc
     */
    public function broadcastOn()
    {
        return new Channel('chat');
    }

    //Custom broadcast message name
    public function broadcastAs()
    {
        return 'MessageEvent';
    }

    /*public function broadcastWith()
    {
        return ['title'=>'This notification from Earth'];
    }*/
}
