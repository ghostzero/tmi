<?php

namespace GhostZero\Tmi\Events\Irc;

use GhostZero\Tmi\Events\Event;

class MotdEvent extends Event
{
    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}
