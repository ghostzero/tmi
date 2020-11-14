<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Channel;
use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Irc\JoinEvent;

class JoinMessage extends IrcMessage
{
    public Channel $channel;
    public string $message;
    public string $user;

    public function __construct(string $message)
    {
        parent::__construct($message);

        $this->user = strstr($this->source, '!', true);
        $this->message = $message;
    }

    public function handle(Client $client, array $channels): array
    {
        if (array_key_exists($this->commandSuffix, $channels)) {
            $this->channel = $channels[$this->commandSuffix];
        } else {
            $this->channel = new Channel($this->commandSuffix);
        }

        return [
            new JoinEvent($this->channel, $this->tags, $this->user, $this->message),
        ];
    }
}
