<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;

class R9kModeEvent extends Event
{
    /**
     * @var Channel IRC Channel state object
     */
    public Channel $channel;

    /**
     * @var bool Indicates whether the mode is active
     */
    public bool $enabled;

    public function __construct(Channel $channel, bool $enabled)
    {
        $this->channel = $channel;
        $this->enabled = $enabled;
    }
}
