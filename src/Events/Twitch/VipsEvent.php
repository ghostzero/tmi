<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;

class VipsEvent extends Event
{
    public Channel $channel;
    public array $users;

    public function __construct(Channel $channel, array $users)
    {
        $this->channel = $channel;
        $this->users = $users;
    }
}
