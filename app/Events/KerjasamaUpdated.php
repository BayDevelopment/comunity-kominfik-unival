<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KerjasamaUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public array $kerjasama, // <-- array biasa, bukan Model
        public string $action
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('kerjasama');
    }

    public function broadcastAs(): string
    {
        return $this->action;
    }

    public function broadcastWith(): array
    {
        return ['data' => $this->kerjasama];
    }
}
