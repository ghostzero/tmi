<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Event;

class PingMessage extends IrcMessage
{
    public function handle(Client $client, bool $force = false): void
    {
        if ($this->handled && !$force) {
            return;
        }

        $client->write("PONG :$this->payload");
    }

    public function getEvents(): array
    {
        return [
            new Event('ping'),
        ];
    }
}
