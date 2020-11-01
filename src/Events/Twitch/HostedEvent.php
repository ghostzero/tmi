<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;

class HostedEvent extends Event
{
    public Channel $channel;
    public string $user;
    public int $viewers;
    public bool $autoHost;

    public function __construct(Channel $channel, string $user, int $viewers, bool $autoHost)
    {
        $this->channel = $channel;
        $this->user = $user;
        $this->viewers = $viewers;
        $this->autoHost = $autoHost;
    }
}
