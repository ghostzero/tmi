<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Event;

class KickMessage extends IrcMessage
{
    public string $channel;

    public string $message;

    private string $target;

    public string $user;

    public function __construct(string $message)
    {
        parent::__construct($message);

        [$this->target, $this->user] = explode(' ', $this->commandSuffix);
        $this->message = $this->payload;
    }

    public function handle(Client $client, array $channels): array
    {
        if (array_key_exists($this->target, $channels)) {
            $this->channel = $channels[$this->target]->getName();
        }

        if ($client->getOptions()->getNickname() === $this->user && $client->getOptions()->shouldAutoRejoin()) {
            $client->join($this->target);
        }

        return [
            new Event('kick', [$this->channel, $this->user, $this->message]),
        ];
    }
}
