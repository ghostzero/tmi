<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Events\Irc\MotdEvent;

class MotdMessage extends IrcMessage
{
    public function handle(Client $client, array $channels): array
    {
        return [
            new Event('motd', [$this->payload]),
            new Event(MotdEvent::class, [new MotdEvent($this->payload)]),
        ];
    }
}
