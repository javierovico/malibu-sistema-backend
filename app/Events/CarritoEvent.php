<?php

namespace App\Events;

use App\Models\Carrito;
use App\Models\Mesa;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CarritoEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private Carrito $carrito;

    public function __construct(Carrito $carrito)
    {
        $this->carrito = $carrito;
    }

    public function broadcastWith(): array
    {
        return $this->carrito->toArray();
    }

    public function broadcastAs(): string
    {
        return $this->carrito->status;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('carrito');
    }
}
