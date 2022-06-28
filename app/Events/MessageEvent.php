<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class MessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $user, $message;
    /**
     * @var false|mixed
     */
    private $privado;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $message, $privado = false)
    {
        $this->user = $user;
        $this->message = $message;
        $this->privado = $privado;
    }

    public function broadcastWith()
    {
        return [
//            'id' => Str::orderedUuid(),
            'user' => $this->user,
            'message' => $this->message,
            'createdAt' => now()->toDateTimeString(),
        ];
    }

    public function broadcastAs()
    {
        return 'message.new';
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        if ($this->privado) {
            return new PrivateChannel('usuario.' . $this->user);
        } else {
            return new Channel('public.room');
        }
    }

}
