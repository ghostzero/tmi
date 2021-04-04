<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Irc\WelcomeEvent;

class WelcomeMessage extends IrcMessage
{
    public function handle(Client $client, array $channels): array
    {
        foreach ($client->getOptions()->getChannels() as $channel) {
            $client->join($channel);
        }

        return [
            new WelcomeEvent(),
        ];
    }
}
