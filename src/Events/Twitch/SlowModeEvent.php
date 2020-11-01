<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;

class SlowModeEvent extends Event
{
    public Channel $channel;
    public bool $enabled;
    public int $minutes;

    public function __construct(Channel $channel, bool $enabled, int $minutes = 0)
    {
        $this->channel = $channel;
        $this->enabled = $enabled;
        $this->minutes = $minutes;
    }
}
