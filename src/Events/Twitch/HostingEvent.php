<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;

class HostingEvent extends Event
{
    public Channel $channel;
    public string $target;
    public int $viewers;

    public function __construct(Channel $channel, string $target, int $viewers)
    {
        $this->channel = $channel;
        $this->target = $target;
        $this->viewers = $viewers;
    }
}
