<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Irc\PingEvent;

class PingMessage extends IrcMessage
{
    public function handle(Client $client, array $channels): array
    {
        $client->write("PONG :$this->payload");

        return [
            new PingEvent(),
        ];
    }
}
