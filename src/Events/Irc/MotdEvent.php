<?php

namespace GhostZero\Tmi\Events\Irc;

class MotdEvent
{
    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}
