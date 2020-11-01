<?php


namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Plan;
use GhostZero\Tmi\Tags;
use GhostZero\Tmi\Traits\HasTagSignature;

class AnonSubMysteryGiftEvent extends Event
{
    use HasTagSignature;

    public Channel $channel;
    public int $giftSubCount;
    public Plan $plan;
    public Tags $tags;

    public function __construct(Channel $channel, int $giftSubCount, Plan $plan, Tags $tags)
    {
        $this->channel = $channel;
        $this->giftSubCount = $giftSubCount;
        $this->plan = $plan;
        $this->tags = $tags;
    }
}
