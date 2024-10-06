<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegisteredWithPlan
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $plan;
    public $priceId;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\User $user
     * @param string $plan
     * @param string $priceId
     */
    public function __construct($user, $plan, $priceId)
    {
        $this->user = $user;
        $this->plan = $plan;
        $this->priceId = $priceId;
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
