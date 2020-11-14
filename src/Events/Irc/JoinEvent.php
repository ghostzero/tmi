<?php

namespace GhostZero\Tmi\Events\Irc;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Tags;

class JoinEvent extends Event
{
    /**
     * @var Channel|string
     */
    public Channel $target;
    public Tags $tags;
    public string $message;
    public string $user;

    public function __construct($target, Tags $tags, string $user, string $message)
    {
        $this->target = $target;
        $this->tags = $tags;
        $this->user = $user;
        $this->message = $message;
    }
}
