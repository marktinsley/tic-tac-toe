<?php

namespace App\Events;

use App\Broadcasting\MatchChannel;
use App\Move;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MoveRecorded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Move
     */
    public $move;

    /**
     * Create a new event instance.
     *
     * @param Move $move
     */
    public function __construct(Move $move)
    {
        $this->move = $move;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new MatchChannel($this->move->match);
    }
}
