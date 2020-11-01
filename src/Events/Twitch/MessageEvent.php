<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Tags;
use GhostZero\Tmi\Traits\HasTagSignature;

class MessageEvent extends Event
{
    use HasTagSignature;

    public Channel $channel;
    public Tags $tags;
    public string $user;
    public string $message;
    public string $self;

    public function __construct(Channel $channel, Tags $tags, string $user, string $message, string $self)
    {
        $this->channel = $channel;
        $this->tags = $tags;
        $this->user = $user;
        $this->message = $message;
        $this->self = $self;
    }
}
