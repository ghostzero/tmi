<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Tags;
use GhostZero\Tmi\Traits\HasTagSignature;

class RitualEvent extends Event
{
    use HasTagSignature;

    public Channel $channel;
    public string $ritual;
    public string $user;
    public Tags $tags;
    public string $message;

    public function __construct(Channel $channel, string $ritual, string $user, Tags $tags, string $message)
    {
        $this->channel = $channel;
        $this->ritual = $ritual;
        $this->user = $user;
        $this->tags = $tags;
        $this->message = $message;
    }
}
