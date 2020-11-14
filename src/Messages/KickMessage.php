<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Irc\KickEvent;

class KickMessage extends IrcMessage
{
    public Channel $channel;

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
            $this->channel = $channels[$this->target];
        } else {
            $this->channel = new Channel($this->target);
        }

        if ($client->getOptions()->getNickname() === $this->user && $client->getOptions()->shouldAutoRejoin()) {
            $client->join($this->target);
        }

        return [
            new KickEvent($this->channel, $this->user, $this->message),
        ];
    }
}
