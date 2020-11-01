<?php

namespace GhostZero\Tmi\Events\Irc;

use GhostZero\Tmi\Channel;

class NameReplyEvent
{
    public Channel $channel;
    public array $names;

    public function __construct(Channel $channel, array $names)
    {
        $this->channel = $channel;
        $this->names = $names;
    }
}
