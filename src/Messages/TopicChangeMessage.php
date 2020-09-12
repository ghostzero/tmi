<?php


namespace GhostZero\Tmi\Messages;


use GhostZero\Tmi\Client;
use GhostZero\Tmi\Events\Event;

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

    public function handle(Client $client, bool $force = false): void
    {
        if ($this->handled && !$force) {
            return;
        }

        $client->getChannel($this->channel)->setTopic($this->topic);
    }

    public function getEvents(): array
    {
        return [
            new Event('topic', [$this->channel, $this->topic]),
        ];
    }
}
