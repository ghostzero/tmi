<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;

class UnhostEvent extends Event
{
    /**
     * @var Channel IRC Channel state object
     */
    public Channel $channel;

    /**
     * @var int Number of viewers
     */
    public int $viewers;

    public function __construct(Channel $channel, int $viewers)
    {
        $this->channel = $channel;
        $this->viewers = $viewers;
    }
}
