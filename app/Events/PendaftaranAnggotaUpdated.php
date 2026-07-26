<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PendaftaranAnggotaUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public array $pendaftaran, // <-- array biasa, bukan Model
        public string $action
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('pendaftaran-anggota');
    }

    public function broadcastAs(): string
    {
        return $this->action;
    }

    public function broadcastWith(): array
    {
        return ['data' => $this->pendaftaran];
    }
}
