<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Events\Event;

class MotdMessage extends IrcMessage
{
    public function getEvents(): array
    {
        return [
            new Event('motd', [$this->payload]),
        ];
    }
}
