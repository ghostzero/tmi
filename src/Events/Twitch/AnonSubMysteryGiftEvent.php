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

    /**
     * @var Channel IRC Channel state object
     */
    public Channel $channel;

    /**
     * @var int Count of given gifts
     */
    public int $giftSubCount;

    /**
     * @var Tags Subscription Plan object
     */
    public Plan $plan;

    /**
     * @var Tags Twitch Tags object
     */
    public Tags $tags;

    public function __construct(Channel $channel, int $giftSubCount, Plan $plan, Tags $tags)
    {
        $this->channel = $channel;
        $this->giftSubCount = $giftSubCount;
        $this->plan = $plan;
        $this->tags = $tags;
    }
}
