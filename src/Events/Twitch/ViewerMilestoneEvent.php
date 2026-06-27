<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Tags;
use GhostZero\Tmi\Traits\HasTagSignature;

class ViewerMilestoneEvent extends Event
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
     * @var int The number of consecutive streams the user has watched.
     */
    public int $milestoneValue;

    /**
     * @var string Message category
     */
    public string $category;

    /**
     * @var Tags Twitch Tags object
     */
    public Tags $tags;

    public function __construct(Channel $channel, string $user, int $milestoneValue, string $category, Tags $tags)
    {
        $this->channel = $channel;
        $this->user = $user;
        $this->milestoneValue = $milestoneValue;
        $this->category = $category;
        $this->tags = $tags;
    }
}
