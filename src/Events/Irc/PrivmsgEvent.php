<?php

namespace GhostZero\Tmi\Events\Irc;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Tags;

/**
 * PRIVMSG is used to send private messages between users.
 */
class PrivmsgEvent extends Event
{
    /**
     * @var Channel|string IRC Channel state object or username
     */
    private $target;

    /**
     * @var Tags Twitch Tags object
     */
    public Tags $tags;

    /**
     * @var string Username of the viewer
     */
    public string $user;

    /**
     * @var string Message content
     */
    public string $message;

    /**
     * @var bool Indicates if the message is from ourselves.
     */
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
