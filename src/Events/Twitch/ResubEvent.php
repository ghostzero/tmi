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

    /**
     * @var Channel IRC Channel state object
     */
    public Channel $channel;

    /**
     * @var string Username of the viewer
     */
    public string $user;

    /**
     * @var int The number of consecutive months the user has subscribed.
     */
    public int $streakMonths;

    /**
     * @var string Message content
     */
    public string $message;

    /**
     * @var Tags Twitch Tags object
     */
    public Tags $tags;

    /**
     * @var Tags Subscription Plan object
     */
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
