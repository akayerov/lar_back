<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SocketIOTest implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $survey;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($survey)
    {
        $this->survey = $survey;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
/*
    public function broadcastOn()
    {
        return new PublicChannel('channel-name1');
    }
*/

    public function broadcastOn()
    {
  //      return ['my-channel'];
        return new PresenceChannel('survey.' . $this->survey['id']);
    }
/*
    public function broadcastAs()
    {
        return 'my-event';
    }
*/


}
