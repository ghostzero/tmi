<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Plan;
use GhostZero\Tmi\Tags;
use GhostZero\Tmi\Traits\HasTagSignature;

class AnonSubGiftEvent extends Event
{
    use HasTagSignature;

    /**
     * @var Channel IRC Channel state object
     */
    public Channel $channel;

    /**
     * @var int The number of consecutive months the user has subscribed.
     */
    public int $streakMonths;

    /**
     * @var string Username of the recipient
     */
    public string $recipient;

    /**
     * @var Tags Subscription Plan object
     */
    public Plan $plan;

    /**
     * @var Tags Twitch Tags object
     */
    public Tags $tags;

    public function __construct(Channel $channel, int $streakMonths, string $recipient, Plan $plan, Tags $tags)
    {
        $this->channel = $channel;
        $this->streakMonths = $streakMonths;
        $this->recipient = $recipient;
        $this->plan = $plan;
        $this->tags = $tags;
    }
}
