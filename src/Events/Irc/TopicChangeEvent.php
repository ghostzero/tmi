<?php

namespace GhostZero\Tmi\Events\Irc;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;

class TopicChangeEvent extends Event
{
    public Channel $channel;
    public string $topic;

    public function __construct(Channel $channel, string $topic)
    {
        $this->channel = $channel;
        $this->topic = $topic;
    }
}
