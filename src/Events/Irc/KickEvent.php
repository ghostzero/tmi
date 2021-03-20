<?php

namespace GhostZero\Tmi\Events\Irc;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;

/**
 * This event is triggered when a viewer has been kicked by the channel moderator.
 */
class KickEvent extends Event
{
    /**
     * @var Channel IRC Channel state object
     */
    public Channel $channel;

    /**
     * @var string Username of the viewer
     */
    public string $user;

    /**
     * @var string Message content
     */
    public string $message;

    public function __construct(Channel $channel, string $user, string $message)
    {
        $this->channel = $channel;
        $this->user = $user;
        $this->message = $message;
    }
}
