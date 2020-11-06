<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;

class RaidEvent extends Event
{
    public Channel $channel;
    public string $user;
    public int $viewers;

    public function __construct(Channel $channel, string $user, int $viewers)
    {
        $this->channel = $channel;
        $this->user = $user;
        $this->viewers = $viewers;
    }
}
