<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Tags;
use GhostZero\Tmi\Traits\HasTagSignature;

class RoomStateEvent extends Event
{
    use HasTagSignature;

    public Channel $channel;
    public Tags $tags;

    public function __construct(Channel $channel, Tags $tags)
    {
        $this->channel = $channel;
        $this->tags = $tags;
    }
}
