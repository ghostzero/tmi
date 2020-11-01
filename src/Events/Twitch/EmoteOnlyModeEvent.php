<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;

class EmoteOnlyModeEvent extends Event
{
    public Channel $channel;
    public bool $enabled;

    public function __construct(Channel $channel, bool $enabled)
    {
        $this->channel = $channel;
        $this->enabled = $enabled;
    }
}
