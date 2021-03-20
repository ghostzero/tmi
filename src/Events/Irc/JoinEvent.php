<?php

namespace GhostZero\Tmi\Events\Irc;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Tags;

/**
 * This event is triggered when a viewer enters the chat.
 */
class JoinEvent extends Event
{
    /**
     * @var Channel IRC Channel state object
     */
    public Channel $channel;

    /**
     * @var Tags Twitch Tags object
     */
    public Tags $tags;

    /**
     * @var string Message content
     */
    public string $message;

    /**
     * @var string Username of the viewer
     */
    public string $user;

    public function __construct(Channel $channel, Tags $tags, string $user, string $message)
    {
        $this->channel = $channel;
        $this->tags = $tags;
        $this->user = $user;
        $this->message = $message;
    }
}
