<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Irc\NameReplyEvent;

class NameReplyMessage extends IrcMessage
{
    public string $channel;

    public array $names;

    public function __construct(string $message)
    {
        parent::__construct($message);

        $this->channel = strstr($this->commandSuffix, '#');
        $this->names = explode(' ', $this->payload ?? '');
    }

    public function handle(Client $client, array $channels): array
    {
        $channel = $client->getChannel($this->channel);
        if (!empty($this->names)) {
            $channel->setUsers($this->names);
        }

        return [
            new NameReplyEvent($channel, $this->names),
        ];
    }
}
