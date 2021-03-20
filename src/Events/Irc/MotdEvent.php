<?php

namespace GhostZero\Tmi\Events\Irc;

use GhostZero\Tmi\Events\Event;

/**
 * This event is triggered when the IRC client enters a IRC server.
 */
class MotdEvent extends Event
{
    /**
     * @var string Message content
     */
    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}
