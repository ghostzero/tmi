<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Tags;
use GhostZero\Tmi\Traits\HasTagSignature;

class CheerEvent extends Event
{
    use HasTagSignature;

    /**
     * @var Channel IRC Channel state object
     */
    public Channel $channel;

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

    public function __construct(Channel $channel, Tags $tags, string $user, string $message, bool $self)
    {
        $this->channel = $channel;
        $this->tags = $tags;
        $this->user = $user;
        $this->message = $message;
        $this->self = $self;
    }
}
