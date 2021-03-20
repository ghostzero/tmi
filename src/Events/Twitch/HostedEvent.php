<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;

class HostedEvent extends Event
{
    /**
     * @var Channel IRC Channel state object
     */
    public Channel $channel;

    /**
     * @var string Username of the streamer
     */
    public string $user;

    /**
     * @var int Number of viewers
     */
    public int $viewers;

    /**
     * @var bool Indicates whether the host is automatically
     */
    public bool $autoHost;

    public function __construct(Channel $channel, string $user, int $viewers, bool $autoHost)
    {
        $this->channel = $channel;
        $this->user = $user;
        $this->viewers = $viewers;
        $this->autoHost = $autoHost;
    }
}
