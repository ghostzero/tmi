<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Tags;
use GhostZero\Tmi\Traits\HasTagSignature;

class AnonGiftPaidUpgradeEvent extends Event
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
     * @var Tags Twitch Tags object
     */
    public Tags $tags;

    public function __construct(Channel $channel, string $user, Tags $tags)
    {
        $this->channel = $channel;
        $this->user = $user;
        $this->tags = $tags;
    }
}
