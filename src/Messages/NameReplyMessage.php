<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Event;

class NameReplyMessage extends IrcMessage
{
    public string $channel;

    public array $names;

    public function __construct(string $message)
    {
        parent::__construct($message);

        $this->channel = strstr($this->commandSuffix, '#');
        $this->names = explode(' ', $this->payload);
    }

    public function handle(Client $client, bool $force = false): void
    {
        if ($this->handled && !$force) {
            return;
        }

        if (!empty($this->names)) {
            $client->getChannel($this->channel)->setUsers($this->names);
        }
    }

    public function getEvents(): array
    {
        return [
            new Event('names', [$this->channel, $this->names]),
        ];
    }
}
