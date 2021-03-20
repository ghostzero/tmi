<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;

class VipsEvent extends Event
{
    /**
     * @var Channel IRC Channel state object
     */
    public Channel $channel;

    /**
     * @var array List of usernames
     */
    public array $users;

    public function __construct(Channel $channel, array $users)
    {
        $this->channel = $channel;
        $this->users = $users;
    }
}
