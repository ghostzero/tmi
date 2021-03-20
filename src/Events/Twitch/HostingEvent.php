<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;

class HostingEvent extends Event
{
    /**
     * @var Channel IRC Channel state object
     */
    public Channel $channel;

    /**
     * @var string Username of the steamer
     */
    public string $target;

    /**
     * @var int Number of viewers
     */
    public int $viewers;

    public function __construct(Channel $channel, string $target, int $viewers)
    {
        $this->channel = $channel;
        $this->target = $target;
        $this->viewers = $viewers;
    }
}
