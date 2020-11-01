<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Tags;
use GhostZero\Tmi\Traits\HasTagSignature;

class AnonGiftPaidUpgradeEvent extends Event
{
    use HasTagSignature;

    public Channel $channel;
    public string $user;
    public Tags $tags;

    public function __construct(Channel $channel, string $user, Tags $tags)
    {
        $this->channel = $channel;
        $this->user = $user;
        $this->tags = $tags;
    }
}
