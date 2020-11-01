<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Irc\MotdEvent;

class MotdMessage extends IrcMessage
{
    public function handle(Client $client, array $channels): array
    {
        return [
            new MotdEvent($this->payload),
        ];
    }
}
