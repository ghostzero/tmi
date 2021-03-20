<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;

class FollowersOnlyModeEvent extends Event
{
    /**
     * @var Channel IRC Channel state object
     */
    public Channel $channel;

    /**
     * @var bool Indicates whether the mode is active
     */
    public bool $enabled;

    /**
     * @var int The number of seconds a chatter without moderator privileges must wait between sending messages
     */
    public int $minutes;

    public function __construct(Channel $channel, bool $enabled, int $minutes = 0)
    {
        $this->channel = $channel;
        $this->enabled = $enabled;
        $this->minutes = $minutes;
    }
}
