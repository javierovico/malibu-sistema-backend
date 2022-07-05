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

class MesaAsignacionEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private Mesa $mesa;

    public function __construct(Mesa $mesa)
    {
        $this->mesa = $mesa;
    }

    public function broadcastWith()
    {
        $this->mesa->load([
            Mesa::RELACION_ULTIMO_CARITO . '.' . Carrito::RELACION_MOZO,
            Mesa::RELACION_CARRITO_ACTIVO . '.' . Carrito::RELACION_MOZO,
        ]);
        return $this->mesa->toArray();
    }

    public function broadcastAs()
    {
        return 'mesa-asignada';
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('mesa');
    }
}
