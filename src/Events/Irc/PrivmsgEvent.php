<?php

namespace GhostZero\Tmi\Events\Irc;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Tags;

class PrivmsgEvent extends Event
{
    /**
     * @var Channel|string
     */
    private $target;
    public Tags $tags;
    public string $user;
    public string $message;
    public bool $self;

    public function __construct($target, Tags $tags, string $user, string $message, bool $self)
    {
        $this->target = $target;
        $this->tags = $tags;
        $this->user = $user;
        $this->message = $message;
        $this->self = $self;
    }
}
