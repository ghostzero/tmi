<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Plan;
use GhostZero\Tmi\Tags;
use GhostZero\Tmi\Traits\HasTagSignature;

class SubEvent extends Event
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
     * @var Tags Subscription Plan object
     */
    public Plan $plan;

    /**
     * @var string Message content
     */
    public string $message;

    /**
     * @var Tags Twitch Tags object
     */
    public Tags $tags;

    public function __construct(Channel $channel, string $user, Plan $plan, string $message, Tags $tags)
    {
        $this->channel = $channel;
        $this->user = $user;
        $this->plan = $plan;
        $this->message = $message;
        $this->tags = $tags;
    }
}
