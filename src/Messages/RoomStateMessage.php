<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Event;
use GhostZero\Tmi\Events\Twitch\FollowersOnlyModeEvent;
use GhostZero\Tmi\Events\Twitch\RoomStateEvent;
use GhostZero\Tmi\Events\Twitch\SlowModeEvent;

class RoomStateMessage extends IrcMessage
{
    public Channel $channel;

    public function handle(Client $client, array $channels): array
    {
        if (array_key_exists($this->commandSuffix, $channels)) {
            $this->channel = $channels[$this->commandSuffix];
        }

        $events = [
            new RoomStateEvent($this->channel, $this->tags),
        ];

        if ($event = $this->getSpecificEvent()) {
            $events[] = $event;
        }

        return $events;
    }

    public function getSpecificEvent(): ?Event
    {
        $tags = $this->tags;

        if (array_key_exists('slow', $tags->getTags())) {
            if (is_bool($tags['slow']) && !$tags['slow']) {
                return new SlowModeEvent($this->channel, false);
            }

            $minutes = (int)$tags['slow'];
            return new SlowModeEvent($this->channel, true, $minutes);
        }

        if (array_key_exists('followers-only', $tags->getTags())) {
            if ($tags['followers-only'] === '-1') {
                return new FollowersOnlyModeEvent($this->channel, false);
            }

            $minutes = (int)$tags['followers-only'];
            return new FollowersOnlyModeEvent($this->channel, true, $minutes);
        }

        return null;
    }
}
