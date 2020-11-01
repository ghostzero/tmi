<?php

namespace GhostZero\Tmi\Events\Irc;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;

class KickEvent extends Event
{
    public Channel $channel;
    public string $user;
    public string $message;

    public function __construct(Channel $channel, string $user, string $message)
    {
        $this->channel = $channel;
        $this->user = $user;
        $this->message = $message;
    }
}