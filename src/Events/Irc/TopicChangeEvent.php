<?php

namespace GhostZero\Tmi\Events\Irc;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;

/**
 * This is an IRC event when the topic of the channel has changed. Not supported by Twitch.
 */
class TopicChangeEvent extends Event
{
    /**
     * @var Channel IRC Channel state object
     */
    public Channel $channel;

    /**
     * @var string The  new topic of the channel.
     */
    public string $topic;

    public function __construct(Channel $channel, string $topic)
    {
        $this->channel = $channel;
        $this->topic = $topic;
    }
}
