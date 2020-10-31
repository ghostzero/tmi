<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Event;

class HostTargetMessage extends IrcMessage
{
    public string $channel;

    public string $message;

    public function __construct(string $message)
    {
        parent::__construct($message);

        $this->channel = substr(strstr($this->commandSuffix, '#'), 1);
        $this->message = $message;
    }

    public function handle(Client $client, array $channels): array
    {
        $msgSplit = explode(' ', $this->payload);
        $viewers = (int)($msgSplit[1] ?? 0);

        if ($msgSplit[0] === '-') {
            return [
                new Event('unhost', [$this->channel, $viewers]),
            ];
        }

        return [
            new Event('hosting', [$this->channel, $msgSplit[0], $viewers]),
        ];
    }
}
