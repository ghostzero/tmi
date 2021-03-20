<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Tags;
use GhostZero\Tmi\Traits\HasTagSignature;

class RitualEvent extends Event
{
    use HasTagSignature;

    /**
     * @var Channel IRC Channel state object
     */
    public Channel $channel;

    /**
     * @var string Name of the ritual
     */
    public string $ritual;

    /**
     * @var string Username of the viewer
     */
    public string $user;

    /**
     * @var Tags Twitch Tags object
     */
    public Tags $tags;

    /**
     * @var string Message content
     */
    public string $message;

    public function __construct(Channel $channel, string $ritual, string $user, Tags $tags, string $message)
    {
        $this->channel = $channel;
        $this->ritual = $ritual;
        $this->user = $user;
        $this->tags = $tags;
        $this->message = $message;
    }
}
