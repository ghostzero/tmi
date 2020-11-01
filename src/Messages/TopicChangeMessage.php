<?php

namespace GhostZero\Tmi\Messages;

use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Irc\TopicChangeEvent;

class TopicChangeMessage extends IrcMessage
{
    public string $channel;

    public string $topic;

    public function __construct(string $message)
    {
        parent::__construct($message);

        $this->channel = strstr($this->commandSuffix, '#');
        $this->topic = $this->payload;
    }

    public function handle(Client $client, array $channels): array
    {
        $channel = $client->getChannel($this->channel);
        $channel->setTopic($this->topic);

        return [
            new TopicChangeEvent($channel, $this->topic),
        ];
    }

}
