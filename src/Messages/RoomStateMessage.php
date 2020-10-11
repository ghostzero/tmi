<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Events\Event;

class RoomStateMessage extends IrcMessage
{
    public string $channel;

    public function __construct(string $message)
    {
        parent::__construct($message);

        $this->channel = strstr($this->commandSuffix, '#');
    }

    public function getEvents(): array
    {
        return [
            new Event('roomstate', [$this->channel, $this->tags]),
            $this->getSpecificEvent(),
        ];
    }

    public function getSpecificEvent(): ?Event
    {
        $tags = $this->tags;

        if (array_key_exists('slow', $tags->getTags())) {
            if (is_bool($tags['slow']) && !$tags['slow']) {
                return new Event('slow', [$this->channel, false, 0]);
            }

            $minutes = (int)$tags['slow'];
            return new Event('slow', [$this->channel, true, $minutes]);
        }

        if (array_key_exists('followers-only', $tags->getTags())) {
            if ($tags['followers-only'] === '-1') {
                return new Event('followersonly', [$this->channel, false, 0]);
            }

            $minutes = (int)$tags['followers-only'];
            return new Event('followersonly', [$this->channel, true, $minutes]);
        }

        return null;
    }
}
