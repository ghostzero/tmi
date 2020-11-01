<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Plan;
use GhostZero\Tmi\Tags;
use GhostZero\Tmi\Traits\HasTagSignature;

class ResubEvent extends Event
{
    use HasTagSignature;

    public Channel $channel;
    public string $user;
    public int $streakMonths;
    public string $message;
    public Tags $tags;
    public Plan $plan;

    public function __construct(Channel $channel, string $user, int $streakMonths, string $message, Tags $tags, Plan $plan)
    {
        $this->channel = $channel;
        $this->user = $user;
        $this->streakMonths = $streakMonths;
        $this->message = $message;
        $this->tags = $tags;
        $this->plan = $plan;
    }
}
