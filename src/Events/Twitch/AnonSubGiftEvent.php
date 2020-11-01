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

    public Channel $channel;
    public int $streakMonths;
    public string $recipient;
    public Plan $plan;
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
