<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Event;

class WelcomeMessage extends IrcMessage
{
    public function handle(Client $client, bool $force = false): void
    {
        if ($this->handled && !$force) {
            return;
        }

        foreach ($client->getChannels() as $channel) {
            $client->join($channel->getName());
        }
    }

    public function getEvents(): array
    {
        return [
            new Event('registered'),
        ];
    }
}
