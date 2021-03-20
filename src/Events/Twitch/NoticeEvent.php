<?php

namespace GhostZero\Tmi\Events\Twitch;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Events\Event;

class NoticeEvent extends Event
{
    /**
     * @var Channel IRC Channel state object
     */
    public Channel $channel;

    /**
     * @var string Unique message identifier
     */
    public string $messageId;

    public function __construct(Channel $channel, string $messageId)
    {
        $this->channel = $channel;
        $this->messageId = $messageId;
    }
}
