<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Twitch\HostingEvent;
use GhostZero\Tmi\Events\Twitch\UnhostEvent;

class HostTargetMessage extends IrcMessage
{
    public Channel $channel;

    public string $message;

    public function __construct(string $message)
    {
        parent::__construct($message);

        $this->message = $message;
    }

    public function handle(Client $client, array $channels): array
    {
        if (array_key_exists($this->commandSuffix, $channels)) {
            $this->channel = $channels[$this->commandSuffix];
        } else {
            $this->channel = new Channel($this->commandSuffix);
        }

        $msgSplit = explode(' ', $this->payload);
        $viewers = (int)($msgSplit[1] ?? 0);

        if ($msgSplit[0] === '-') {
            return [
                new UnhostEvent($this->channel, $viewers),
            ];
        }

        return [
            new HostingEvent($this->channel, $msgSplit[0], $viewers),
        ];
    }
}
