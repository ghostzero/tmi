<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;

class UnhostEvent extends Event
{
    public Channel $channel;
    public int $viewers;

    public function __construct(Channel $channel, int $viewers)
    {
        $this->channel = $channel;
        $this->viewers = $viewers;
    }
}
