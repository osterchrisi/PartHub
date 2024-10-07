<?php

namespace App\Events;

use App\Models\Location;
use App\Models\Part;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockMovementOccured
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $stock_levels;
    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct(array $stock_level, User $user)
    {
        $this->stock_levels = $stock_level;
        // Get human-readable part name
        $this->stock_levels[3] = Part::find($stock_level[0])->part_name;
        //Replace location ID with corresponding name
        $this->stock_levels[2] = Location::find($stock_level[2])->location_name;
        $this->user = $user;
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
