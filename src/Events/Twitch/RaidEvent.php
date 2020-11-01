<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;

class RaidEvent extends Event
{
    public Channel $channel;
    public string $raidedChannel;
    public int $viewers;

    public function __construct(Channel $channel, string $raidedChannel, int $viewers)
    {
        $this->channel = $channel;
        $this->raidedChannel = $raidedChannel;
        $this->viewers = $viewers;
    }
}
