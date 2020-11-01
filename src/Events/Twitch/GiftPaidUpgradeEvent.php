<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Tags;
use GhostZero\Tmi\Traits\HasTagSignature;

class GiftPaidUpgradeEvent extends Event
{
    use HasTagSignature;

    public Channel $channel;
    public string $user;
    public string $sender;
    public Tags $tags;

    public function __construct(Channel $channel, string $user, string $sender, Tags $tags)
    {
        $this->channel = $channel;
        $this->user = $user;
        $this->sender = $sender;
        $this->tags = $tags;
    }
}
