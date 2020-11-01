<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Plan;
use GhostZero\Tmi\Tags;
use GhostZero\Tmi\Traits\HasTagSignature;

class SubMysteryGiftEvent extends Event
{
    use HasTagSignature;

    public Channel $channel;
    public string $user;
    public int $giftSubCount;
    public Plan $plan;
    public Tags $tags;

    public function __construct(Channel $channel, string $user, int $giftSubCount, Plan $plan, Tags $tags)
    {
        $this->channel = $channel;
        $this->user = $user;
        $this->giftSubCount = $giftSubCount;
        $this->plan = $plan;
        $this->tags = $tags;
    }
}
