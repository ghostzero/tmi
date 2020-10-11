<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Events\Event;

class HostTargetMessage extends IrcMessage
{
    public string $channel;

    public string $message;

    public function __construct(string $message)
    {
        parent::__construct($message);

        $this->channel = strstr($this->commandSuffix, '#');
        $this->message = $message;
    }

    public function getEvents(): array
    {
        $msgSplit = explode(' ', $this->message);
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
