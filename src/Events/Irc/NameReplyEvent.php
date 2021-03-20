<?php

namespace GhostZero\Tmi\Events\Irc;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;

class NameReplyEvent extends Event
{
    /**
     * @var Channel IRC Channel state object
     */
    public Channel $channel;

    /**
     * @var array List of usernames that are visible
     */
    public array $names;

    public function __construct(Channel $channel, array $names)
    {
        $this->channel = $channel;
        $this->names = $names;
    }
}
