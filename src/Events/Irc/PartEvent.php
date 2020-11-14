<?php

namespace GhostZero\Tmi\Events\Irc;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Tags;

class PartEvent extends Event
{
    public Channel $channel;
    public Tags $tags;
    public string $message;
    public string $user;

    public function __construct(Channel $channel, Tags $tags, string $user, string $message)
    {
        $this->channel = $channel;
        $this->tags = $tags;
        $this->user = $user;
        $this->message = $message;
    }
}
